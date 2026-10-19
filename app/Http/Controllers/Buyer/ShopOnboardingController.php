<?php

namespace App\Http\Controllers\Buyer;

use App\Enums\ShopStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\ApplyShopRequest;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopOnboardingController extends Controller
{
    /**
     * Display the shop application screen or current application status.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $shop = $user->shop;

        // If shop is already approved, redirect to seller dashboard
        if ($shop && $shop->isApproved()) {
            return redirect()->route('seller.dashboard');
        }

        return view('buyer.shop.apply', [
            'shop' => $shop,
        ]);
    }

    /**
     * Store a newly submitted shop application.
     */
    public function store(ApplyShopRequest $request): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($request, $user) {
            $baseSlug = Str::slug($request->validated('name'));
            $slug = $baseSlug;
            $count = 1;

            // Ensure unique slug
            while (Shop::where('slug', $slug)->where('user_id', '!=', $user->id)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }

            if ($user->shop) {
                // Re-apply on previously rejected shop
                $shop = $user->shop;
                $shop->fill([
                    'name' => $request->validated('name'),
                    'slug' => $slug,
                    'description' => $request->validated('description'),
                    'phone' => $request->validated('phone'),
                    'address' => $request->validated('address'),
                ]);
                $shop->status = ShopStatus::PENDING;
                $shop->rejection_reason = null;
                $shop->approved_at = null;
                $shop->save();
            } else {
                // Create new shop application
                $shop = new Shop([
                    'name' => $request->validated('name'),
                    'slug' => $slug,
                    'description' => $request->validated('description'),
                    'phone' => $request->validated('phone'),
                    'address' => $request->validated('address'),
                ]);
                $shop->user_id = $user->id;
                $shop->status = ShopStatus::PENDING;
                $shop->save();
            }
        });

        return redirect()->route('buyer.shop.apply')->with('status', 'Pengajuan toko Anda berhasil dikirim dan saat ini sedang ditinjau oleh Admin.');
    }
}
