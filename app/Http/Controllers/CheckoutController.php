<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\ProcessCheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display the checkout review page.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $cart = $request->user()->getOrCreateCart();
        $items = $cart->items()
            ->with(['product.shop', 'product.primaryImage', 'product.images'])
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        // Group items by shop for multi-vendor display
        $groupedItems = $items->groupBy(fn ($item) => $item->product->shop_id);

        $hasUnavailableItems = $items->contains(fn ($item) => ! $item->isAvailable());

        $addresses = $request->user()->addresses()->latest('is_default')->latest('id')->get();
        $selectedAddressId = (int) $request->query('address_id', old('address_id', 0));
        $selectedAddress = $selectedAddressId > 0
            ? $addresses->firstWhere('id', $selectedAddressId)
            : ($addresses->firstWhere('is_default', true) ?? $addresses->first());

        $shippingCostsByShop = [];
        foreach ($groupedItems as $shopId => $shopItems) {
            $shop = $shopItems->first()->product->shop;
            $shippingCostsByShop[$shopId] = (float) ($shop->shipping_cost ?? 0.00);
        }

        $totalShippingCost = array_sum($shippingCostsByShop);
        $subtotal = (float) $cart->subtotal();
        $grandTotal = $subtotal + $totalShippingCost;

        return view('checkout.index', [
            'cart' => $cart,
            'items' => $items,
            'groupedItems' => $groupedItems,
            'hasUnavailableItems' => $hasUnavailableItems,
            'subtotal' => $subtotal,
            'totalQuantity' => $cart->totalQuantity(),
            'addresses' => $addresses,
            'selectedAddress' => $selectedAddress,
            'shippingCostsByShop' => $shippingCostsByShop,
            'totalShippingCost' => $totalShippingCost,
            'grandTotal' => $grandTotal,
        ]);
    }

    /**
     * Store and process the checkout into parent and child orders.
     */
    public function store(ProcessCheckoutRequest $request, CheckoutService $checkoutService): RedirectResponse
    {
        try {
            $order = $checkoutService->process($request->user(), $request->validated());

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.');
        } catch (ValidationException $e) {
            $errorMessage = collect($e->errors())->flatten()->first() ?? 'Gagal memproses pesanan.';

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }
}
