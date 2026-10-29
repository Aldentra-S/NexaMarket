<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerOrderUiTest extends TestCase
{
    use RefreshDatabase;

    private function createSellerWithShop(): array
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);

        return [$seller, $shop];
    }

    private function createOrderForShop(Shop $shop, OrderStatus $status): ShopOrder
    {
        $buyer = User::factory()->buyer()->create();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-' . uniqid(),
            'status' => $status,
            'total_amount' => 75000.00,
            'paid_at' => $status !== OrderStatus::PENDING ? now() : null,
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => $order->order_number . '-S1',
            'status' => $status,
            'subtotal' => 75000.00,
        ]);

        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 75000.00,
        ]);

        OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => 75000.00,
            'quantity' => 1,
            'subtotal' => 75000.00,
        ]);

        return $shopOrder;
    }

    public function test_seller_navigation_includes_orders_link(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();

        $response = $this->actingAs($seller)->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSee(route('seller.orders.index'));
        $response->assertSee('Pesanan Masuk');
    }

    public function test_action_button_on_paid_order_shows_only_process_order(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $shopOrder = $this->createOrderForShop($shop, OrderStatus::PAID);

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertSee('Mulai Proses Pesanan');
        $response->assertSee('value="processing"', false);
        $response->assertDontSee('Tandai Telah Dikirim');
        $response->assertDontSee('Selesaikan Pesanan');
    }

    public function test_action_button_on_processing_order_shows_only_ship_order(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $shopOrder = $this->createOrderForShop($shop, OrderStatus::PROCESSING);

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertSee('Tandai Telah Dikirim');
        $response->assertSee('value="shipped"', false);
        $response->assertDontSee('Mulai Proses Pesanan');
        $response->assertDontSee('Selesaikan Pesanan');
    }

    public function test_action_button_on_shipped_order_shows_only_complete_order(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $shopOrder = $this->createOrderForShop($shop, OrderStatus::SHIPPED);

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertSee('Selesaikan Pesanan');
        $response->assertSee('value="completed"', false);
        $response->assertDontSee('Mulai Proses Pesanan');
        $response->assertDontSee('Tandai Telah Dikirim');
    }

    public function test_completed_order_shows_no_active_action_buttons(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $shopOrder = $this->createOrderForShop($shop, OrderStatus::COMPLETED);

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertSee('Pesanan Telah Selesai');
        $response->assertDontSee('Mulai Proses Pesanan');
        $response->assertDontSee('Tandai Telah Dikirim');
        $response->assertDontSee('Selesaikan Pesanan');
    }

    public function test_unpaid_order_shows_warning_and_no_action_buttons(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $shopOrder = $this->createOrderForShop($shop, OrderStatus::PENDING);

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertSee('Pesanan Belum Siap Diproses');
        $response->assertDontSee('Mulai Proses Pesanan');
        $response->assertDontSee('Tandai Telah Dikirim');
        $response->assertDontSee('Selesaikan Pesanan');
    }

    public function test_order_index_filters_and_empty_state(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $this->createOrderForShop($shop, OrderStatus::PAID);

        // Filter by completed (none exist yet) -> empty state
        $responseEmpty = $this->actingAs($seller)->get(route('seller.orders.index', ['status' => 'completed']));
        $responseEmpty->assertOk();
        $responseEmpty->assertSee('Belum Ada Pesanan');

        // Filter by paid (1 exists)
        $responsePaid = $this->actingAs($seller)->get(route('seller.orders.index', ['status' => 'paid']));
        $responsePaid->assertOk();
        $responsePaid->assertDontSee('Belum Ada Pesanan');
        $responsePaid->assertSee('Perlu Diproses');
    }

    public function test_order_show_renders_product_thumbnail_correctly(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $shopOrder = $this->createOrderForShop($shop, OrderStatus::PAID);
        $item = $shopOrder->items->first();

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertSee($item->product->thumbnail_url, false);
        $response->assertSee($item->product_name);
    }
}
