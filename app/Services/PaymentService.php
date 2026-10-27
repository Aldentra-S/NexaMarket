<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private ?PaymentGatewayInterface $gateway = null
    ) {}

    /**
     * Create or retrieve an active Midtrans Snap transaction for the pending order.
     *
     * @throws ValidationException
     */
    public function createSnapTransaction(Order $order): Payment
    {
        if (! $order->isPending()) {
            throw ValidationException::withMessages([
                'order' => 'Pesanan ini sudah tidak dalam status menunggu pembayaran.',
            ]);
        }

        if (! $this->gateway) {
            throw ValidationException::withMessages([
                'gateway' => 'Gateway pembayaran belum dikonfigurasi.',
            ]);
        }

        try {
            return $this->gateway->createTransaction($order);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'gateway' => 'Gagal membuat sesi pembayaran Snap: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Reconcile pending order payment status directly with the payment gateway.
     */
    public function reconcileStatusFromGateway(Order $order): ?Payment
    {
        if (! $this->gateway) {
            return null;
        }

        $payment = $order->payments()
            ->where('payment_method', 'midtrans')
            ->where('status', PaymentStatus::PENDING)
            ->whereNotNull('payment_number')
            ->latest()
            ->first();

        if (! $payment) {
            return null;
        }

        if (method_exists($this->gateway, 'checkTransactionStatus')) {
            $payload = $this->gateway->checkTransactionStatus($payment->payment_number);
            if ($payload && is_array($payload) && ! empty($payload['transaction_status'])) {
                try {
                    return $this->processNotification($payload);
                } catch (ValidationException $e) {
                    \Illuminate\Support\Facades\Log::warning("Reconciliation verification failed for order #{$order->id}: " . $e->getMessage());
                }
            }
        }

        return null;
    }

    /**
     * Process incoming Midtrans webhook notification with signature validation,
     * status transitions, idempotency, anti-regression, and atomic stock restoration.
     *
     * @throws ValidationException
     */
    public function processNotification(array $payload): Payment
    {
        if (! $this->gateway || ! $this->gateway->verifyNotification($payload)) {
            throw ValidationException::withMessages([
                'signature_key' => 'Tanda tangan digital (signature key) Midtrans tidak valid.',
            ]);
        }

        $orderId = $payload['order_id'] ?? null;
        if (! $orderId) {
            throw ValidationException::withMessages([
                'order_id' => 'Parameter order_id tidak ditemukan dalam notifikasi.',
            ]);
        }

        $payment = Payment::where('payment_number', $orderId)->first();
        if (! $payment) {
            throw ValidationException::withMessages([
                'order_id' => "Data transaksi dengan ID '{$orderId}' tidak ditemukan di sistem.",
            ]);
        }

        $grossAmountPayload = (float) ($payload['gross_amount'] ?? 0);
        $expectedAmount = (float) $payment->amount;

        if (abs($grossAmountPayload - $expectedAmount) > 0.01 || abs($grossAmountPayload - (float) $payment->order->total_amount) > 0.01) {
            throw ValidationException::withMessages([
                'gross_amount' => 'Nominal transaksi pada notifikasi tidak cocok dengan data pesanan di sistem.',
            ]);
        }

        return DB::transaction(function () use ($payment, $payload) {
            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::where('id', $payment->id)->lockForUpdate()->firstOrFail();
            /** @var Order $lockedOrder */
            $lockedOrder = Order::where('id', $lockedPayment->order_id)->lockForUpdate()->firstOrFail();

            $transactionStatus = $payload['transaction_status'] ?? '';
            $fraudStatus = $payload['fraud_status'] ?? '';

            $isSuccess = ($transactionStatus === 'settlement')
                || ($transactionStatus === 'capture' && $fraudStatus === 'accept');
            $isPending = ($transactionStatus === 'pending')
                || ($transactionStatus === 'capture' && $fraudStatus === 'challenge');
            $isCancelled = in_array($transactionStatus, ['deny', 'cancel'], true);
            $isExpired = ($transactionStatus === 'expire');

            if ($isSuccess) {
                // Anti out-of-order: do NOT resurrect an already cancelled or expired order whose stock was released
                if ($lockedOrder->isCancelled() || $lockedOrder->isExpired()) {
                    $lockedPayment->update([
                        'gateway_transaction_id' => $payload['transaction_id'] ?? $lockedPayment->gateway_transaction_id,
                        'payload' => array_merge((array) $lockedPayment->payload, [
                            'webhook' => $payload,
                            'note' => 'Pembayaran berhasil di gateway tetapi pesanan sudah dibatalkan/kedaluwarsa sebelumnya.',
                        ]),
                    ]);

                    return $lockedPayment;
                }

                // Idempotent: If still pending, mark paid
                if ($lockedOrder->isPending()) {
                    $lockedOrder->update([
                        'status' => OrderStatus::PAID,
                        'paid_at' => now(),
                    ]);

                    $lockedOrder->shopOrders()->update([
                        'status' => OrderStatus::PAID,
                    ]);
                }

                $lockedPayment->update([
                    'status' => PaymentStatus::SUCCESS,
                    'paid_at' => $lockedPayment->paid_at ?: now(),
                    'gateway_transaction_id' => $payload['transaction_id'] ?? $lockedPayment->gateway_transaction_id,
                    'payload' => array_merge((array) $lockedPayment->payload, [
                        'webhook' => $payload,
                    ]),
                ]);

                return $lockedPayment;
            }

            if ($isPending) {
                // Anti out-of-order: do NOT regress if order is already paid or completed
                if (! $lockedOrder->isPaid() && ! $lockedOrder->isCompleted()) {
                    $lockedPayment->update([
                        'status' => PaymentStatus::PENDING,
                        'gateway_transaction_id' => $payload['transaction_id'] ?? $lockedPayment->gateway_transaction_id,
                        'payload' => array_merge((array) $lockedPayment->payload, [
                            'webhook' => $payload,
                        ]),
                    ]);
                }

                return $lockedPayment;
            }

            if ($isCancelled || $isExpired) {
                // Anti out-of-order: do NOT cancel an already paid or completed order
                if ($lockedOrder->isPaid() || $lockedOrder->isCompleted()) {
                    return $lockedPayment;
                }

                $targetOrderStatus = $isCancelled ? OrderStatus::CANCELLED : OrderStatus::EXPIRED;
                $targetPaymentStatus = $isCancelled ? PaymentStatus::FAILED : PaymentStatus::EXPIRED;

                // Only restore stock if the order has not been cancelled/expired yet (idempotent stock safety)
                if ($lockedOrder->isPending()) {
                    $lockedOrder->update([
                        'status' => $targetOrderStatus,
                        'cancelled_at' => now(),
                    ]);

                    $lockedOrder->shopOrders()->update([
                        'status' => $targetOrderStatus,
                    ]);

                    foreach ($lockedOrder->shopOrders as $shopOrder) {
                        foreach ($shopOrder->items as $item) {
                            if ($item->product_id) {
                                Product::where('id', $item->product_id)
                                    ->lockForUpdate()
                                    ->increment('stock', $item->quantity);
                            }
                        }
                    }
                }

                $lockedPayment->update([
                    'status' => $targetPaymentStatus,
                    'gateway_transaction_id' => $payload['transaction_id'] ?? $lockedPayment->gateway_transaction_id,
                    'payload' => array_merge((array) $lockedPayment->payload, [
                        'webhook' => $payload,
                    ]),
                ]);

                return $lockedPayment;
            }

            return $lockedPayment;
        });
    }

    /**
     * Simulate local payment lifecycle transition for an order (QA mock).
     *
     * @throws ValidationException
     */
    public function simulate(Order $order, string $result): Payment
    {
        if (app()->isProduction() || config('app.env') === 'production' || config('services.midtrans.is_production')) {
            throw ValidationException::withMessages([
                'simulation' => 'Simulasi pembayaran lokal dinonaktifkan pada lingkungan produksi.',
            ]);
        }

        return DB::transaction(function () use ($order, $result) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (! $lockedOrder || ! $lockedOrder->isPending()) {
                throw ValidationException::withMessages([
                    'payment' => 'Pesanan ini sudah diproses atau tidak lagi dalam status menunggu pembayaran.',
                ]);
            }

            $paymentNumber = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));

            if ($result === 'success') {
                $lockedOrder->update([
                    'status' => OrderStatus::PAID,
                    'paid_at' => now(),
                ]);

                $lockedOrder->shopOrders()->update([
                    'status' => OrderStatus::PAID,
                ]);

                return Payment::create([
                    'order_id' => $lockedOrder->id,
                    'payment_number' => $paymentNumber,
                    'status' => PaymentStatus::SUCCESS,
                    'amount' => $lockedOrder->total_amount,
                    'payment_method' => 'mock_payment',
                    'paid_at' => now(),
                    'payload' => [
                        'simulation' => true,
                        'outcome' => 'success',
                        'message' => 'Simulasi pembayaran berhasil secara lokal.',
                    ],
                ]);
            }

            $newStatus = ($result === 'failed') ? OrderStatus::CANCELLED : OrderStatus::EXPIRED;
            $paymentStatus = ($result === 'failed') ? PaymentStatus::FAILED : PaymentStatus::EXPIRED;

            $lockedOrder->update([
                'status' => $newStatus,
                'cancelled_at' => now(),
            ]);

            $lockedOrder->shopOrders()->update([
                'status' => $newStatus,
            ]);

            foreach ($lockedOrder->shopOrders as $shopOrder) {
                foreach ($shopOrder->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)
                            ->lockForUpdate()
                            ->increment('stock', $item->quantity);
                    }
                }
            }

            return Payment::create([
                'order_id' => $lockedOrder->id,
                'payment_number' => $paymentNumber,
                'status' => $paymentStatus,
                'amount' => $lockedOrder->total_amount,
                'payment_method' => 'mock_payment',
                'paid_at' => null,
                'payload' => [
                    'simulation' => true,
                    'outcome' => $result,
                    'message' => "Simulasi pembayaran {$result} secara lokal. Stok telah dipulihkan.",
                ],
            ]);
        });
    }
}
