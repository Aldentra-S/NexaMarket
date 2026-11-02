<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * Process transactional multi-vendor checkout for the authenticated buyer.
     *
     * @throws ValidationException
     */
    public function process(User $user, array $data = []): Order
    {
        $idempotencyKey = $data['idempotency_key'] ?? null;

        // Idempotency check: if an order with this key already exists for this buyer, return it
        if (! empty($idempotencyKey)) {
            $existingOrder = Order::where('user_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existingOrder) {
                return $existingOrder;
            }
        }

        return DB::transaction(function () use ($user, $data, $idempotencyKey) {
            $cart = $user->cart;

            if (! $cart || $cart->items()->count() === 0) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang belanja Anda kosong. Tambahkan produk sebelum melakukan checkout.',
                ]);
            }

            // Lock and fetch current cart items
            $cartItems = $cart->items()->with('product.shop')->get();

            // First pass: validate all products with lockForUpdate to ensure consistent stock & availability
            $lockedProducts = [];
            $totalAmount = 0.00;

            foreach ($cartItems as $cartItem) {
                $product = Product::where('id', $cartItem->product_id)
                    ->with('shop')
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu produk di keranjang Anda tidak lagi ditemukan.',
                    ]);
                }

                if ($product->status !== ProductStatus::PUBLISHED) {
                    throw ValidationException::withMessages([
                        'cart' => "Produk \"{$product->name}\" saat ini sedang tidak aktif atau diarsipkan.",
                    ]);
                }

                $shop = $product->shop;
                if (! $shop || $shop->status !== ShopStatus::APPROVED || $shop->isSuspended()) {
                    throw ValidationException::withMessages([
                        'cart' => "Toko \"{$shop?->name}\" saat ini sedang tidak beroperasi atau ditangguhkan.",
                    ]);
                }

                if ($product->stock < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok produk \"{$product->name}\" tidak mencukupi. Tersedia {$product->stock} unit, diminta {$cartItem->quantity} unit.",
                    ]);
                }

                // Calculate item subtotal using latest database price
                $itemSubtotal = (float) bcmul((string) $product->price, (string) $cartItem->quantity, 2);
                $totalAmount = (float) bcadd((string) $totalAmount, (string) $itemSubtotal, 2);

                $lockedProducts[$cartItem->id] = [
                    'product' => $product,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => (float) $product->price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Group locked items by shop for multi-vendor splitting
            $groupedByShop = [];
            foreach ($lockedProducts as $itemData) {
                $shopId = $itemData['product']->shop_id;
                $groupedByShop[$shopId][] = $itemData;
            }

            // Resolve shipping address (if provided, verify ownership; otherwise use default if available)
            $addressId = $data['address_id'] ?? null;
            $address = null;

            if (! empty($addressId)) {
                $address = Address::where('id', $addressId)
                    ->where('user_id', $user->id)
                    ->first();

                if (! $address) {
                    throw ValidationException::withMessages([
                        'address_id' => 'Alamat pengiriman yang dipilih tidak valid atau bukan milik Anda.',
                    ]);
                }
            } else {
                $address = $user->defaultAddress();
            }

            // Calculate multi-vendor shipping cost per shop based on shop configuration
            $totalShippingCost = 0.00;
            $shopShippingCosts = [];
            foreach ($groupedByShop as $shopId => $items) {
                $shop = $items[0]['product']->shop;
                $shopCost = (float) ($shop->shipping_cost ?? 0.00);
                $shopShippingCosts[$shopId] = $shopCost;
                $totalShippingCost = (float) bcadd((string) $totalShippingCost, (string) $shopCost, 2);
            }

            $grandTotal = (float) bcadd((string) $totalAmount, (string) $totalShippingCost, 2);

            // Create Parent Order
            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'status' => OrderStatus::PENDING,
                'total_amount' => $grandTotal,
                'idempotency_key' => $idempotencyKey,
                'notes' => $data['notes'] ?? null,
                'shipping_recipient_name' => $address?->recipient_name,
                'shipping_phone' => $address?->phone,
                'shipping_province' => $address?->province,
                'shipping_city' => $address?->city,
                'shipping_district' => $address?->district,
                'shipping_postal_code' => $address?->postal_code,
                'shipping_address' => $address ? ($address->address_line . ($address->village ? ', Kel. ' . $address->village : '')) : null,
                'shipping_delivery_notes' => $address?->delivery_notes,
                'shipping_cost_total' => $totalShippingCost,
            ]);

            $shopOrderIndex = 1;
            foreach ($groupedByShop as $shopId => $items) {
                $shop = $items[0]['product']->shop;
                $shopShippingCost = $shopShippingCosts[$shopId] ?? 0.00;

                $shopSubtotal = 0.00;
                foreach ($items as $item) {
                    $shopSubtotal = (float) bcadd((string) $shopSubtotal, (string) $item['subtotal'], 2);
                }

                $shopOrder = ShopOrder::create([
                    'order_id' => $order->id,
                    'shop_id' => $shop->id,
                    'shop_name' => $shop->name,
                    'order_number' => $orderNumber . '-S' . $shopOrderIndex++,
                    'status' => OrderStatus::PENDING,
                    'subtotal' => $shopSubtotal,
                    'shipping_cost' => $shopShippingCost,
                ]);

                foreach ($items as $item) {
                    $prod = $item['product'];

                    OrderItem::create([
                        'shop_order_id' => $shopOrder->id,
                        'product_id' => $prod->id,
                        'product_name' => $prod->name,
                        'product_slug' => $prod->slug,
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Atomically decrement stock
                    $prod->decrement('stock', $item['quantity']);
                }
            }

            // Clear the buyer's cart items after successful order creation
            $cart->items()->delete();

            return $order;
        });
    }
}
