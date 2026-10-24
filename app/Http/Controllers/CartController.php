<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\CartItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the authenticated buyer's cart.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $cart = $user->getOrCreateCart();

        $items = $cart->items()
            ->with(['product.shop', 'product.primaryImage', 'product.images', 'product.category'])
            ->latest('id')
            ->get();

        $hasUnavailableItems = $items->contains(fn (CartItem $item) => ! $item->isAvailable());

        return view('cart.index', [
            'cart' => $cart,
            'items' => $items,
            'hasUnavailableItems' => $hasUnavailableItems,
            'subtotal' => $cart->subtotal(),
            'totalQuantity' => $cart->totalQuantity(),
        ]);
    }

    /**
     * Store a product into the buyer's cart or increment its quantity.
     */
    public function store(AddToCartRequest $request): RedirectResponse
    {
        $user = $request->user();
        $productId = (int) $request->validated('product_id');
        $quantity = (int) $request->validated('quantity');

        DB::transaction(function () use ($user, $productId, $quantity) {
            $cart = $user->getOrCreateCart();

            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $quantity);
            } else {
                $cart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }
        });

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang belanja.');
    }

    /**
     * Update the quantity of a specific cart item.
     */
    public function update(UpdateCartItemRequest $request, CartItem $item): RedirectResponse
    {
        Gate::authorize('update', $item);

        $quantity = (int) $request->validated('quantity');

        $item->update([
            'quantity' => $quantity,
        ]);

        return back()->with('success', 'Jumlah produk di keranjang berhasil diperbarui.');
    }

    /**
     * Remove the specified item from the buyer's cart.
     */
    public function destroy(Request $request, CartItem $item): RedirectResponse
    {
        Gate::authorize('delete', $item);

        $item->delete();

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}
