<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Exception;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap as MidtransSnap;

class MidtransPaymentGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    private function configureMidtrans(): void
    {
        MidtransConfig::$serverKey = config('services.midtrans.server_key', '');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production', false);
        MidtransConfig::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        MidtransConfig::$is3ds = (bool) config('services.midtrans.is_3ds', true);
    }

    /**
     * Create or reuse an active Snap transaction session.
     *
     * @throws Exception
     */
    public function createTransaction(Order $order): Payment
    {
        // 1. Idempotency: Reuse existing active pending Snap token if available
        $existingPayment = Payment::where('order_id', $order->id)
            ->where('payment_method', 'midtrans')
            ->where('status', PaymentStatus::PENDING)
            ->whereNotNull('snap_token')
            ->latest()
            ->first();

        if ($existingPayment) {
            return $existingPayment;
        }

        $paymentNumber = 'PAY-MID-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
        $grossAmount = (int) round((float) $order->total_amount);

        // 2. Build item details server-side
        $itemDetails = [];
        foreach ($order->shopOrders as $shopOrder) {
            foreach ($shopOrder->items as $item) {
                $itemDetails[] = [
                    'id' => (string) ($item->product_id ?: 'item-' . $item->id),
                    'price' => (int) round((float) $item->unit_price),
                    'quantity' => (int) $item->quantity,
                    'name' => mb_substr($item->product_name, 0, 50),
                ];
            }
        }

        $params = [
            'transaction_details' => [
                'order_id' => $paymentNumber,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name ?? 'Pelanggan',
                'email' => $order->user->email ?? 'customer@example.com',
            ],
            'item_details' => $itemDetails,
        ];

        try {
            $snapTransaction = MidtransSnap::createTransaction($params);
            $token = is_object($snapTransaction) ? $snapTransaction->token : $snapTransaction['token'];
            $redirectUrl = is_object($snapTransaction) ? $snapTransaction->redirect_url : $snapTransaction['redirect_url'];
        } catch (Exception $e) {
            throw new Exception('Gagal menghubungi gateway pembayaran Midtrans: ' . $e->getMessage(), 0, $e);
        }

        return Payment::create([
            'order_id' => $order->id,
            'payment_number' => $paymentNumber,
            'status' => PaymentStatus::PENDING,
            'amount' => $order->total_amount,
            'payment_method' => 'midtrans',
            'snap_token' => $token,
            'snap_redirect_url' => $redirectUrl,
            'payload' => [
                'gateway' => 'midtrans_sandbox',
                'params' => [
                    'order_id' => $paymentNumber,
                    'gross_amount' => $grossAmount,
                ],
            ],
        ]);
    }

    /**
     * Verify Midtrans notification signature key.
     */
    public function verifyNotification(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signature = $payload['signature_key'] ?? '';
        $serverKey = config('services.midtrans.server_key', '');

        if (empty($orderId) || empty($statusCode) || empty($grossAmount) || empty($signature) || empty($serverKey)) {
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signature);
    }
}
