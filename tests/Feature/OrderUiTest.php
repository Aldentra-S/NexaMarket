<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_renders_grouped_vendor_items_and_totals(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();

        $shop1 = Shop::factory()->approved()->create(['name' => 'Toko Buku Cerdas']);
        $shop2 = Shop::factory()->approved()->create(['name' => 'Toko Kopi Sejahtera']);

        $product1 = Product::factory()->published()->create([
            'shop_id' => $shop1->id,
            'name' => 'Ensiklopedia Sains Modern',
            'price' => 150000.00,
            'stock' => 10,
        ]);
        $product2 = Product::factory()->published()->create([
            'shop_id' => $shop2->id,
            'name' => 'Kopi Robusta Lampung 500g',
            'price' => 80000.00,
            'stock' => 15,
        ]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product1->id, 'quantity' => 1]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product2->id, 'quantity' => 2]);

        $response = $this->actingAs($buyer)->get(route('checkout.create'));

        $response->assertOk();
        $response->assertViewIs('checkout.index');
        $response->assertSee('Toko Buku Cerdas');
        $response->assertSee('Toko Kopi Sejahtera');
        $response->assertSee('Ensiklopedia Sains Modern');
        $response->assertSee('Kopi Robusta Lampung 500g');
        $response->assertSee('Rp 150.000');
        $response->assertSee('Rp 160.000'); // 2 x 80000
        $response->assertSee('Rp 310.000'); // total
        $response->assertSee(route('checkout.store'));
    }

    public function test_checkout_page_redirects_to_cart_if_cart_is_empty(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->get(route('checkout.create'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_cart_page_enables_link_to_checkout_when_items_exist(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id, 'stock' => 5]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($buyer)->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee(route('checkout.create'));
        $response->assertSee('Lanjut ke Checkout');
    }

    public function test_buyer_can_view_order_confirmation_and_details(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create(['name' => 'Toko Aksesoris']);
        $product = Product::factory()->published()->create(['shop_id' => $shop->id]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-20261024-TEST99',
            'status' => OrderStatus::PENDING,
            'total_amount' => 120000.00,
            'notes' => 'Harap kirim saat jam kerja.',
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-20261024-TEST99-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => 120000.00,
        ]);

        OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => 'Kabel Type-C Braided',
            'product_slug' => 'kabel-type-c-braided',
            'unit_price' => 60000.00,
            'quantity' => 2,
            'subtotal' => 120000.00,
        ]);

        $response = $this->actingAs($buyer)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertViewIs('orders.show');
        $response->assertSee('ORD-20261024-TEST99');
        $response->assertSee('Toko Aksesoris');
        $response->assertSee('Kabel Type-C Braided');
        $response->assertSee('Rp 120.000');
        $response->assertSee('Menunggu Pembayaran');
        $response->assertSee('Harap kirim saat jam kerja.');
    }

    public function test_buyer_cannot_view_another_buyers_order_details(): void
    {
        $buyer1 = User::factory()->buyer()->create();
        $buyer2 = User::factory()->buyer()->create();

        $orderBuyer2 = Order::create([
            'user_id' => $buyer2->id,
            'order_number' => 'ORD-PRIVATE-001',
            'status' => OrderStatus::PENDING,
            'total_amount' => 50000.00,
        ]);

        $response = $this->actingAs($buyer1)->get(route('orders.show', $orderBuyer2));

        $response->assertForbidden();
    }

    public function test_buyer_can_view_order_history_list(): void
    {
        $buyer = User::factory()->buyer()->create();

        $order1 = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-HIST-01',
            'status' => OrderStatus::PAID,
            'total_amount' => 150000.00,
        ]);

        $order2 = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-HIST-02',
            'status' => OrderStatus::PENDING,
            'total_amount' => 275000.00,
        ]);

        $response = $this->actingAs($buyer)->get(route('orders.index'));

        $response->assertOk();
        $response->assertViewIs('orders.index');
        $response->assertSee('ORD-HIST-01');
        $response->assertSee('ORD-HIST-02');
        $response->assertSee('Rp 150.000');
        $response->assertSee('Rp 275.000');
        $response->assertSee(route('orders.show', $order1));
        $response->assertSee(route('orders.show', $order2));
    }

    public function test_order_history_displays_empty_state_when_buyer_has_no_orders(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee('Belum Ada Pesanan');
        $response->assertSee(route('products.index'));
    }
}
