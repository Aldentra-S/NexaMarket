<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the seller dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $shop = $user->shop;

        $totalProducts = $shop ? $shop->products()->count() : 0;
        $publishedProducts = $shop ? $shop->products()->where('status', ProductStatus::PUBLISHED->value)->count() : 0;
        $draftProducts = $shop ? $shop->products()->where('status', ProductStatus::DRAFT->value)->count() : 0;

        return view('seller.dashboard', [
            'shop' => $shop,
            'user' => $user,
            'totalProducts' => $totalProducts,
            'publishedProducts' => $publishedProducts,
            'draftProducts' => $draftProducts,
        ]);
    }
}
