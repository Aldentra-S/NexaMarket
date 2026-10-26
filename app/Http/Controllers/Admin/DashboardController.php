<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the comprehensive platform overview for administrators.
     */
    public function index(Request $request): View
    {
        $paidStatuses = [
            OrderStatus::PAID->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::COMPLETED->value,
        ];

        $totalUsers = User::count();
        $buyerCount = User::where('role', UserRole::BUYER->value)->count();
        $sellerCount = User::where('role', UserRole::SELLER->value)->count();
        $adminCount = User::where('role', UserRole::ADMIN->value)->count();

        $totalShops = Shop::count();
        $pendingShopsCount = Shop::where('status', ShopStatus::PENDING->value)->count();
        $approvedShopsCount = Shop::where('status', ShopStatus::APPROVED->value)->count();
        $suspendedShopsCount = Shop::where('status', ShopStatus::SUSPENDED->value)->count();

        $totalProducts = Product::count();
        $publishedProductsCount = Product::where('status', ProductStatus::PUBLISHED->value)->count();

        $totalOrders = Order::count();
        $paidOrdersCount = Order::whereIn('status', $paidStatuses)->count();
        $totalGmv = (float) Order::whereIn('status', $paidStatuses)->sum('total_amount');

        $recentPendingShops = Shop::where('status', ShopStatus::PENDING->value)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::with(['user', 'shopOrders'])
            ->latest()
            ->take(6)
            ->get();

        // 6-month transaction and GMV trend for performance graph
        $monthlyPerformance = collect(range(5, 0))->map(function ($monthsAgo) use ($paidStatuses) {
            $monthDate = now()->subMonths($monthsAgo);
            $start = $monthDate->copy()->startOfMonth();
            $end = $monthDate->copy()->endOfMonth();

            $ordersCount = Order::whereBetween('created_at', [$start, $end])->count();
            $gmvAmount = (float) Order::whereIn('status', $paidStatuses)
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount');

            return [
                'month' => $monthDate->translatedFormat('M'),
                'orders' => $ordersCount,
                'gmv' => $gmvAmount,
            ];
        });

        // Unified recent activity stream from real database records
        $activities = collect();

        foreach ($recentPendingShops as $shop) {
            $activities->push([
                'type' => 'shop',
                'title' => 'Pengajuan toko baru: ' . $shop->name,
                'user_name' => $shop->user->name ?? 'User',
                'user_role' => $shop->user->role->value ?? 'buyer',
                'created_at' => $shop->created_at,
                'status' => 'Menunggu',
                'status_badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                'link' => route('admin.shops.show', $shop),
            ]);
        }

        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'title' => 'Pesanan baru: #' . $order->order_number,
                'user_name' => $order->user->name ?? 'Pembeli',
                'user_role' => 'buyer',
                'created_at' => $order->created_at,
                'status' => $order->status->label(),
                'status_badge' => $order->status->badgeColor(),
                'link' => route('admin.orders.show', $order),
            ]);
        }

        $recentActivities = $activities->sortByDesc('created_at')->values()->take(6);

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'buyerCount' => $buyerCount,
            'sellerCount' => $sellerCount,
            'adminCount' => $adminCount,
            'totalShops' => $totalShops,
            'pendingShopsCount' => $pendingShopsCount,
            'approvedShopsCount' => $approvedShopsCount,
            'suspendedShopsCount' => $suspendedShopsCount,
            'totalProducts' => $totalProducts,
            'publishedProductsCount' => $publishedProductsCount,
            'totalOrders' => $totalOrders,
            'paidOrdersCount' => $paidOrdersCount,
            'totalGmv' => $totalGmv,
            'recentPendingShops' => $recentPendingShops,
            'recentOrders' => $recentOrders,
            'monthlyPerformance' => $monthlyPerformance,
            'recentActivities' => $recentActivities,
        ]);
    }
}
