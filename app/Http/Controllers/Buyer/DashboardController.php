<?php

namespace App\Http\Controllers\Buyer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the buyer shopping dashboard.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $totalOrdersCount = $user->orders()->count();

        $pendingOrdersCount = $user->orders()
            ->where('status', OrderStatus::PENDING->value)
            ->count();

        $shippingOrdersCount = $user->orders()
            ->whereIn('status', [
                OrderStatus::PAID->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::SHIPPED->value,
            ])
            ->count();

        $completedOrdersCount = $user->orders()
            ->where('status', OrderStatus::COMPLETED->value)
            ->count();

        $recentOrders = $user->orders()
            ->with(['shopOrders.items'])
            ->latest('id')
            ->take(5)
            ->get();

        $cartCount = $user->cart?->totalQuantity() ?? 0;
        $shop = $user->shop;

        $featuredProducts = \App\Models\Product::where('status', \App\Enums\ProductStatus::PUBLISHED->value)
            ->with(['shop', 'category', 'primaryImage'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', [
            'totalOrdersCount' => $totalOrdersCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'shippingOrdersCount' => $shippingOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'recentOrders' => $recentOrders,
            'cartCount' => $cartCount,
            'shop' => $shop,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
