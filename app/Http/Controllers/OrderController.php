<?php

namespace App\Http\Controllers;

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
        $orders = $request->user()->orders()
            ->with(['shopOrders.items'])
            ->latest('id')
            ->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display the specified order details.
     */
    public function show(Request $request, Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['shopOrders.items.product', 'shopOrders.shop']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }
}
