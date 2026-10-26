<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display product reviews and customer feedback overview for the seller's shop.
     */
    public function index(Request $request): View
    {
        $shop = $request->user()->shop;

        $products = $shop->products()
            ->with(['primaryImage', 'category'])
            ->withCount('orderItems')
            ->latest()
            ->paginate(10);

        return view('seller.reviews.index', [
            'shop' => $shop,
            'products' => $products,
        ]);
    }
}
