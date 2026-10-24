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

        return view('checkout.index', [
            'cart' => $cart,
            'items' => $items,
            'groupedItems' => $groupedItems,
            'hasUnavailableItems' => $hasUnavailableItems,
            'subtotal' => $cart->subtotal(),
            'totalQuantity' => $cart->totalQuantity(),
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
