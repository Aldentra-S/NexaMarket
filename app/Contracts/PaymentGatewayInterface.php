<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Create or retrieve a payment transaction session (e.g. Midtrans Snap token) for the specified order.
     *
     * @param Order $order
     * @return Payment
     * @throws \Exception
     */
    public function createTransaction(Order $order): Payment;

    /**
     * Verify webhook / notification payload signature and integrity.
     *
     * @param array $payload
     * @return bool
     */
    public function verifyNotification(array $payload): bool;
}
