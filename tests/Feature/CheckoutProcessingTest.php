<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_attempting_checkout(): void
    {
        $response = $this->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_buyer_cannot_checkout_empty_cart(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseEmpty('orders');
    }

    public function test_buyer_can_successfully_checkout_valid_cart(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();

        $shop = Shop::factory()->approved()->create(['name' => 'Toko Mitra Berkah']);
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Kopi Arabika 250g',
            'price' => 75000.00,
            'stock' => 10,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $key = (string) Str::uuid();

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => $key,
            'notes' => 'Tolong packing aman dengan bubble wrap.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'status' => OrderStatus::PENDING->value,
            'total_amount' => 150000.00,
            'idempotency_key' => $key,
            'notes' => 'Tolong packing aman dengan bubble wrap.',
        ]);

        $order = Order::where('idempotency_key', $key)->first();
        $this->assertNotNull($order);
        $this->assertCount(1, $order->shopOrders);

        $shopOrder = $order->shopOrders->first();
        $this->assertSame($shop->id, $shopOrder->shop_id);
        $this->assertSame('Toko Mitra Berkah', $shopOrder->shop_name);
        $this->assertSame(150000.00, (float) $shopOrder->subtotal);

        $this->assertCount(1, $shopOrder->items);
        $item = $shopOrder->items->first();
        $this->assertSame('Kopi Arabika 250g', $item->product_name);
        $this->assertSame(75000.00, (float) $item->unit_price);
        $this->assertSame(2, $item->quantity);
        $this->assertSame(150000.00, (float) $item->subtotal);

        // Cart items must be cleared
        $this->assertDatabaseEmpty('cart_items');
    }

    public function test_checkout_splits_multi_vendor_items_into_distinct_shop_orders(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();

        $shopA = Shop::factory()->approved()->create(['name' => 'Toko Komputer']);
        $shopB = Shop::factory()->approved()->create(['name' => 'Toko Pakaian']);

        $productA = Product::factory()->published()->create([
            'shop_id' => $shopA->id,
            'name' => 'Flashdisk 64GB',
            'price' => 100000.00,
            'stock' => 10,
        ]);
        $productB1 = Product::factory()->published()->create([
            'shop_id' => $shopB->id,
            'name' => 'Kaos Polos Putih',
            'price' => 50000.00,
            'stock' => 20,
        ]);
        $productB2 = Product::factory()->published()->create([
            'shop_id' => $shopB->id,
            'name' => 'Kemeja Formal Pria',
            'price' => 150000.00,
            'stock' => 5,
        ]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $productA->id, 'quantity' => 1]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $productB1->id, 'quantity' => 2]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $productB2->id, 'quantity' => 1]);

        $key = (string) Str::uuid();

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => $key,
        ]);

        $response->assertRedirect();

        $order = Order::where('idempotency_key', $key)->first();
        $this->assertNotNull($order);
        // Total: (1 * 100000) + (2 * 50000) + (1 * 150000) = 350000
        $this->assertSame(350000.00, (float) $order->total_amount);

        // Multi-vendor child orders: exactly 2 distinct shop orders
        $this->assertCount(2, $order->shopOrders);

        $shopOrderA = $order->shopOrders->where('shop_id', $shopA->id)->first();
        $this->assertNotNull($shopOrderA);
        $this->assertSame(100000.00, (float) $shopOrderA->subtotal);
        $this->assertCount(1, $shopOrderA->items);

        $shopOrderB = $order->shopOrders->where('shop_id', $shopB->id)->first();
        $this->assertNotNull($shopOrderB);
        $this->assertSame(250000.00, (float) $shopOrderB->subtotal);
        $this->assertCount(2, $shopOrderB->items);
    }

    public function test_checkout_atomically_decrements_product_stock_once(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 10,
            'price' => 20000.00,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);

        $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Stock must be exactly 6 (10 - 4)
        $this->assertSame(6, $product->fresh()->stock);
    }

    public function test_checkout_fails_and_rolls_back_if_any_product_has_insufficient_stock(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();

        $product1 = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 10,
            'price' => 50000.00,
        ]);
        $product2 = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 2, // only 2 available
            'price' => 30000.00,
        ]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product1->id, 'quantity' => 2]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product2->id, 'quantity' => 5]); // requested 5 > 2

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Neither stock should change
        $this->assertSame(10, $product1->fresh()->stock);
        $this->assertSame(2, $product2->fresh()->stock);

        // No order should be created
        $this->assertDatabaseEmpty('orders');
        $this->assertDatabaseEmpty('shop_orders');
        $this->assertDatabaseEmpty('order_items');

        // Cart items must remain for buyer to adjust
        $this->assertCount(2, $cart->fresh()->items);
    }

    public function test_checkout_fails_and_rolls_back_if_any_product_is_draft_or_archived(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();

        $activeProduct = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 10,
        ]);
        $draftProduct = Product::factory()->draft()->create([
            'shop_id' => $shop->id,
            'stock' => 10,
        ]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $activeProduct->id, 'quantity' => 1]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $draftProduct->id, 'quantity' => 1]);

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Rollback verification
        $this->assertSame(10, $activeProduct->fresh()->stock);
        $this->assertDatabaseEmpty('orders');
        $this->assertCount(2, $cart->fresh()->items);
    }

    public function test_checkout_fails_and_rolls_back_if_any_shop_is_suspended_or_not_approved(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();

        $suspendedShop = Shop::factory()->suspended()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $suspendedShop->id,
            'stock' => 10,
        ]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseEmpty('orders');
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_checkout_uses_latest_database_price_ignoring_manipulated_client_input(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();

        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 200000.00, // Actual price in DB
            'stock' => 10,
        ]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        // Malicious client posts modified total and subtotal
        $key = (string) Str::uuid();
        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => $key,
            'total_amount' => 500.00,
            'unit_price' => 500.00,
        ]);

        $response->assertRedirect();

        $order = Order::where('idempotency_key', $key)->first();
        $this->assertNotNull($order);
        // Server calculates using DB price of 200000
        $this->assertSame(200000.00, (float) $order->total_amount);
        $this->assertSame(200000.00, (float) $order->items->first()->unit_price);
    }

    public function test_repeated_checkout_with_same_idempotency_key_prevents_duplicate_order_and_double_stock_decrement(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 50000.00,
            'stock' => 10,
        ]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]);

        $sharedKey = (string) Str::uuid();

        // First attempt
        $res1 = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => $sharedKey,
        ]);
        $res1->assertRedirect();

        $this->assertSame(8, $product->fresh()->stock);
        $this->assertDatabaseCount('orders', 1);

        // Second attempt with exact same key (simulating double click / network retry)
        $res2 = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => $sharedKey,
        ]);
        $res2->assertRedirect();

        // Still exactly 1 order, and stock has NOT decremented again
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_buyer_cannot_checkout_or_affect_another_buyers_cart(): void
    {
        $buyer1 = User::factory()->buyer()->create();
        $buyer2 = User::factory()->buyer()->create();

        $cart2 = $buyer2->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 50000.00,
            'stock' => 10,
        ]);

        // Only buyer2 has items in cart
        CartItem::create(['cart_id' => $cart2->id, 'product_id' => $product->id, 'quantity' => 2]);

        // Buyer1 tries to checkout
        $response = $this->actingAs($buyer1)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Buyer1's own cart is empty -> rejected
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Buyer2's cart items must remain completely intact
        $this->assertCount(1, $cart2->fresh()->items);
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertDatabaseEmpty('orders');
    }
}
