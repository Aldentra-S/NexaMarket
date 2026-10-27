<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the buyer's orders.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $query = $request->user()->orders()->with(['shopOrders.items']);

        if ($statusFilter) {
            if ($statusFilter === 'pending') {
                $query->where('status', OrderStatus::PENDING->value);
            } elseif (in_array($statusFilter, ['shipping', 'processing', 'in_process', 'in_transit'], true)) {
                $query->whereIn('status', [
                    OrderStatus::PAID->value,
                    OrderStatus::PROCESSING->value,
                    OrderStatus::SHIPPED->value,
                ]);
            } elseif ($statusFilter === 'completed') {
                $query->where('status', OrderStatus::COMPLETED->value);
            } elseif ($statusFilter === 'cancelled') {
                $query->whereIn('status', [
                    OrderStatus::CANCELLED->value,
                    OrderStatus::EXPIRED->value,
                ]);
            } elseif (in_array($statusFilter, OrderStatus::values(), true)) {
                $query->where('status', $statusFilter);
            }
        }

        $orders = $query->latest('id')->paginate(10)->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'currentStatus' => $statusFilter,
        ]);
    }

    /**
     * Display the specified order details.
     */
    public function show(Request $request, Order $order, \App\Services\PaymentService $paymentService): View
    {
        Gate::authorize('view', $order);

        // If order is pending and user returns from gateway with parameters or status check is requested,
        // attempt authoritative reconciliation via gateway API.
        if ($order->isPending() && ($request->has('order_id') || $request->has('transaction_status') || $request->has('status_code') || $request->query('status') === 'verifying')) {
            $paymentService->reconcileStatusFromGateway($order);
            $order->refresh();
        }

        $order->load(['shopOrders.items.product', 'shopOrders.shop', 'payments']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }
}
