<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\UpdateShopSettingsRequest;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopSettingsController extends Controller
{
    /**
     * Display the seller shop settings screen.
     */
    public function edit(Request $request): View
    {
        $shop = $request->user()->shop;
        Gate::authorize('update', $shop);

        return view('seller.shop.settings', [
            'shop' => $shop,
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the seller shop details.
     */
    public function update(UpdateShopSettingsRequest $request): RedirectResponse
    {
        $shop = $request->user()->shop;
        Gate::authorize('update', $shop);

        $name = $request->validated('name');

        if ($shop->name !== $name) {
            $baseSlug = Str::slug($name);
            $slug = $baseSlug;
            $count = 1;

            while (Shop::where('slug', $slug)->where('id', '!=', $shop->id)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }

            $shop->slug = $slug;
        }

        $shop->name = $name;
        $shop->description = $request->validated('description');
        $shop->phone = $request->validated('phone');
        $shop->address = $request->validated('address');
        $shop->save();

        return redirect()->route('seller.shop.settings')
            ->with('status', 'Informasi dan profil toko berhasil diperbarui.')
            ->with('success', 'Informasi dan profil toko berhasil diperbarui.');
    }
}
