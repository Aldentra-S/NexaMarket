<?php

namespace App\Services;

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
    /**
     * Simulate local payment lifecycle transition for an order.
     *
     * @throws ValidationException
     */
    public function simulate(Order $order, string $result): Payment
    {
        return DB::transaction(function () use ($order, $result) {
            // Lock order for update
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

            // If failed or expired: transition to cancelled or expired, and restore product stock
            $newStatus = ($result === 'failed') ? OrderStatus::CANCELLED : OrderStatus::EXPIRED;
            $paymentStatus = ($result === 'failed') ? PaymentStatus::FAILED : PaymentStatus::EXPIRED;

            $lockedOrder->update([
                'status' => $newStatus,
                'cancelled_at' => now(),
            ]);

            $lockedOrder->shopOrders()->update([
                'status' => $newStatus,
            ]);

            // Atomically restore product stock
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
