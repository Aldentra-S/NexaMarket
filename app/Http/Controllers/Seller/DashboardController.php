<?php

namespace App\Http\Controllers\Seller;

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

        return view('seller.dashboard', [
            'shop' => $shop,
            'user' => $user,
        ]);
    }
}
