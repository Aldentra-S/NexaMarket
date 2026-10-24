<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders belonging to the seller's shop.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ShopOrder::class);

        $shop = $request->user()->shop;
        $statusFilter = $request->query('status');

        $query = $shop->shopOrders()
            ->with([
                'order.user',
                'items.product.primaryImage',
            ])
            ->latest();

        if ($statusFilter && in_array($statusFilter, OrderStatus::values(), true)) {
            $query->where('status', $statusFilter);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('seller.orders.index', [
            'shop' => $shop,
            'orders' => $orders,
            'currentStatus' => $statusFilter,
        ]);
    }

    /**
     * Display the specified shop order details for fulfillment.
     */
    public function show(Request $request, ShopOrder $shopOrder): View
    {
        Gate::authorize('view', $shopOrder);

        $shop = $request->user()->shop;

        $shopOrder->loadMissing([
            'order.user',
            'order.latestPayment',
            'items.product.primaryImage',
        ]);

        return view('seller.orders.show', [
            'shop' => $shop,
            'shopOrder' => $shopOrder,
        ]);
    }

    /**
     * Update the fulfillment status of the specified shop order.
     */
    public function updateStatus(
        \App\Http\Requests\Seller\UpdateOrderStatusRequest $request,
        ShopOrder $shopOrder,
        \App\Services\FulfillmentService $fulfillmentService
    ): \Illuminate\Http\RedirectResponse {
        Gate::authorize('update', $shopOrder);

        $targetStatus = OrderStatus::from($request->validated('status'));

        $fulfillmentService->updateStatus($shopOrder, $targetStatus);

        return redirect()->route('seller.orders.show', $shopOrder)
            ->with('success', "Status pesanan {$shopOrder->order_number} berhasil diperbarui menjadi {$targetStatus->label()}.");
    }
}
