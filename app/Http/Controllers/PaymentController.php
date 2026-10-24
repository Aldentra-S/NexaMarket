<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\SimulatePaymentRequest;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /**
     * Create or retrieve Midtrans Snap token for a pending order.
     */
    public function createSnapToken(Order $order, PaymentService $paymentService): JsonResponse
    {
        Gate::authorize('view', $order);

        $payment = $paymentService->createSnapTransaction($order);

        return response()->json([
            'status' => 'success',
            'snap_token' => $payment->snap_token,
            'redirect_url' => $payment->snap_redirect_url,
            'payment_number' => $payment->payment_number,
        ]);
    }

    /**
     * Handle incoming webhook notification from Midtrans.
     */
    public function handleNotification(Request $request, PaymentService $paymentService): JsonResponse
    {
        $payload = $request->all();

        $payment = $paymentService->processNotification($payload);

        return response()->json([
            'status' => 'success',
            'payment_status' => $payment->status->value,
            'order_status' => $payment->order->status->value,
        ]);
    }

    /**
     * Simulate local mock payment outcome for a pending order.
     */
    public function simulate(SimulatePaymentRequest $request, Order $order, PaymentService $paymentService): RedirectResponse
    {
        Gate::authorize('view', $order);

        try {
            $payment = $paymentService->simulate($order, $request->validated('result'));

            $message = match ($payment->status->value) {
                'success' => 'Pembayaran pesanan berhasil dikonfirmasi (Simulasi).',
                'failed' => 'Pembayaran ditandai gagal. Pesanan telah dibatalkan dan stok produk dipulihkan.',
                'expired' => 'Pembayaran telah kedaluwarsa. Pesanan dibatalkan dan stok produk dipulihkan.',
                default => 'Status pembayaran diperbarui.',
            };

            return redirect()->route('orders.show', $order)->with('success', $message);
        } catch (ValidationException $e) {
            $errorMessage = collect($e->errors())->flatten()->first() ?? 'Gagal memproses simulasi pembayaran.';

            return redirect()->route('orders.show', $order)->with('error', $errorMessage);
        }
    }
}
