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
        $newOrdersCount = $shop ? $shop->shopOrders()->whereIn('status', [OrderStatus::PAID->value, OrderStatus::PENDING->value])->count() : 0;
        $processingOrdersCount = $shop ? $shop->shopOrders()->whereIn('status', [OrderStatus::PAID->value, OrderStatus::PROCESSING->value])->count() : 0;
        $shippedOrdersCount = $shop ? $shop->shopOrders()->where('status', OrderStatus::SHIPPED->value)->count() : 0;
        $completedOrdersCount = $shop ? $shop->shopOrders()->where('status', OrderStatus::COMPLETED->value)->count() : 0;

        $totalRevenue = $shop
            ? (float) $shop->shopOrders()->whereIn('status', $paidStatuses)->sum('subtotal')
            : 0.0;

        // 7-day sales trend based on real database transactions
        $salesTrend = collect(range(6, 0))->map(function ($daysAgo) use ($shop, $paidStatuses) {
            $date = now()->subDays($daysAgo);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $dayRevenue = $shop
                ? (float) $shop->shopOrders()
                    ->whereIn('status', $paidStatuses)
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->sum('subtotal')
                : 0.0;

            $dayOrders = $shop
                ? $shop->shopOrders()
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count()
                : 0;

            return [
                'date' => $date->translatedFormat('d M'),
                'revenue' => $dayRevenue,
                'orders' => $dayOrders,
            ];
        });

        $totalSoldUnits = $shop
            ? (int) \App\Models\OrderItem::whereHas('shopOrder', function ($q) use ($shop, $paidStatuses) {
                $q->where('shop_id', $shop->id)->whereIn('status', $paidStatuses);
            })->sum('quantity')
            : 0;

        $recentOrders = $shop
            ? $shop->shopOrders()
                ->with(['order.user', 'items.product.primaryImage'])
                ->latest('id')
                ->take(6)
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
            'newOrdersCount' => $newOrdersCount,
            'processingOrdersCount' => $processingOrdersCount,
            'shippedOrdersCount' => $shippedOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'totalRevenue' => $totalRevenue,
            'salesTrend' => $salesTrend,
            'totalSoldUnits' => $totalSoldUnits,
            'recentOrders' => $recentOrders,
            'criticalStockList' => $criticalStockList,
        ]);
    }
}
