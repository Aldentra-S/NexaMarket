<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of all platform orders across all vendors.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $query = Order::with(['user', 'shopOrders.shop'])->latest();

        if ($statusFilter && in_array($statusFilter, OrderStatus::values(), true)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'ilike', "%{$search}%")
                         ->orWhere('email', 'ilike', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'currentStatus' => $statusFilter,
            'search' => $search,
            'totalCount' => Order::count(),
            'pendingCount' => Order::where('status', OrderStatus::PENDING->value)->count(),
            'paidCount' => Order::where('status', OrderStatus::PAID->value)->count(),
            'completedCount' => Order::where('status', OrderStatus::COMPLETED->value)->count(),
            'cancelledCount' => Order::where('status', OrderStatus::CANCELLED->value)->count(),
        ]);
    }

    /**
     * Display the full details of a specific customer order.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'shopOrders.shop', 'shopOrders.items.product', 'payments']);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }
}
