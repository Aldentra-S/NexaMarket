<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerDashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_dashboard_calculates_real_revenue_and_stock_metrics(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        $buyer = User::factory()->buyer()->create();

        // Create product with low stock (3 units)
        $productLow = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Kopi Robusta Asli',
            'stock' => 3,
            'price' => 50000,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create an order for this shop with paid shop_order
        $order = Order::factory()->create([
            'user_id' => $buyer->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 100000,
        ]);

        $shopOrder = ShopOrder::factory()->create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'status' => OrderStatus::PAID,
            'subtotal' => 100000,
        ]);

        OrderItem::factory()->create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $productLow->id,
            'quantity' => 2,
            'unit_price' => 50000,
            'subtotal' => 100000,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSee('Seller Center');
        $response->assertSee('Total Pendapatan Terverifikasi');
        $response->assertSee('Rp 100.000');
        $response->assertSee('Kopi Robusta Asli');
        $response->assertSee('3 unit');
    }

    public function test_seller_sidebar_does_not_contain_side_tab_border_l_4_tell(): void
    {
        $seller = User::factory()->seller()->create();
        Shop::factory()->approved()->create(['user_id' => $seller->id]);

        $response = $this->actingAs($seller)->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertDontSee('border-l-4');
    }

    public function test_seller_dashboard_recent_orders_renders_product_thumbnail_cleanly(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        $buyer = User::factory()->buyer()->create();

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Meja Belajar Ergonomis',
            'stock' => 10,
            'price' => 750000,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $order = Order::factory()->create([
            'user_id' => $buyer->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 750000,
        ]);

        $shopOrder = ShopOrder::factory()->create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'order_number' => 'SO-TEST-IMG-001',
            'status' => OrderStatus::PAID,
            'subtotal' => 750000,
        ]);

        OrderItem::factory()->create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => 'Meja Belajar Ergonomis',
            'quantity' => 1,
            'unit_price' => 750000,
            'subtotal' => 750000,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSee('SO-TEST-IMG-001');
        $response->assertSee('Meja Belajar Ergonomis');
        $response->assertSee($product->thumbnail_url);
    }
}
