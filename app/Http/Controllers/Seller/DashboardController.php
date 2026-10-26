<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the seller dashboard with comprehensive operational & financial summary.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $shop = $user->shop;

        $totalProducts = $shop ? $shop->products()->count() : 0;
        $publishedProducts = $shop ? $shop->products()->where('status', ProductStatus::PUBLISHED->value)->count() : 0;
        $draftProducts = $shop ? $shop->products()->where('status', ProductStatus::DRAFT->value)->count() : 0;
        $lowStockProducts = $shop ? $shop->products()->where('stock', '<=', 5)->count() : 0;

        $paidStatuses = [
            OrderStatus::PAID->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::COMPLETED->value,
        ];

        $totalOrders = $shop ? $shop->shopOrders()->count() : 0;
        $processingOrdersCount = $shop ? $shop->shopOrders()->whereIn('status', [OrderStatus::PAID->value, OrderStatus::PROCESSING->value])->count() : 0;
        $completedOrdersCount = $shop ? $shop->shopOrders()->where('status', OrderStatus::COMPLETED->value)->count() : 0;

        $totalRevenue = $shop
            ? (float) $shop->shopOrders()->whereIn('status', $paidStatuses)->sum('subtotal')
            : 0.0;

        $totalSoldUnits = $shop
            ? (int) \App\Models\OrderItem::whereHas('shopOrder', function ($q) use ($shop, $paidStatuses) {
                $q->where('shop_id', $shop->id)->whereIn('status', $paidStatuses);
            })->sum('quantity')
            : 0;

        $recentOrders = $shop
            ? $shop->shopOrders()
                ->with(['order.user', 'items'])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $criticalStockList = $shop
            ? $shop->products()
                ->with('primaryImage')
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->take(5)
                ->get()
            : collect();

        return view('seller.dashboard', [
            'shop' => $shop,
            'user' => $user,
            'totalProducts' => $totalProducts,
            'publishedProducts' => $publishedProducts,
            'draftProducts' => $draftProducts,
            'lowStockProducts' => $lowStockProducts,
            'totalOrders' => $totalOrders,
            'processingOrdersCount' => $processingOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'totalRevenue' => $totalRevenue,
            'totalSoldUnits' => $totalSoldUnits,
            'recentOrders' => $recentOrders,
            'criticalStockList' => $criticalStockList,
        ]);
    }
}
