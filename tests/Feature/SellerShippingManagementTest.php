<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerShippingManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderForShop(Shop $shop, OrderStatus $status = OrderStatus::PROCESSING, float $shippingCost = 15000.00): array
    {
        $buyer = User::factory()->buyer()->create();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-' . uniqid(),
            'status' => $status,
            'total_amount' => 115000.00,
            'shipping_cost_total' => $shippingCost,
            'shipping_recipient_name' => 'Budi Sudarsono',
            'shipping_phone' => '081234567890',
            'shipping_province' => 'Jawa Barat',
            'shipping_city' => 'Bandung',
            'shipping_district' => 'Coblong',
            'shipping_postal_code' => '40132',
            'shipping_address' => 'Jl. Cisitu Indah No. 5',
            'shipping_delivery_notes' => 'Pagar putih',
            'paid_at' => now(),
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => $order->order_number . '-S1',
            'status' => $status,
            'subtotal' => 100000.00,
            'shipping_cost' => $shippingCost,
        ]);

        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 100000.00,
        ]);

        $item = OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => 100000.00,
            'quantity' => 1,
            'subtotal' => 100000.00,
        ]);

        return [$order, $shopOrder, $item, $buyer];
    }

    public function test_seller_can_view_shipping_details_in_order_show(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        [$order, $shopOrder] = $this->createOrderForShop($shop, OrderStatus::PROCESSING, 15000.00);

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertSee('Budi Sudarsono');
        $response->assertSee('081234567890');
        $response->assertSee('Jl. Cisitu Indah No. 5');
        $response->assertSee('Bandung');
        $response->assertSee('Pagar putih');
        $response->assertSee('Rp 15.000');
    }

    public function test_seller_can_mark_order_as_shipped_with_courier_and_tracking_number(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        [$order, $shopOrder] = $this->createOrderForShop($shop, OrderStatus::PROCESSING);

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'shipped',
            'courier' => 'JNE',
            'tracking_number' => 'JNE123456789',
        ]);

        $response->assertRedirect(route('seller.orders.show', $shopOrder));
        $response->assertSessionHas('success');

        $shopOrder->refresh();
        $this->assertSame(OrderStatus::SHIPPED, $shopOrder->status);
        $this->assertEquals('JNE', $shopOrder->courier);
        $this->assertEquals('JNE123456789', $shopOrder->tracking_number);
        $this->assertNotNull($shopOrder->shipped_at);
        $this->assertTrue($shopOrder->isShipped());
    }

    public function test_seller_must_provide_courier_and_tracking_number_when_shipping(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        [$order, $shopOrder] = $this->createOrderForShop($shop, OrderStatus::PROCESSING);

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'shipped',
            'courier' => '',
            'tracking_number' => '',
        ]);

        $response->assertSessionHasErrors(['courier', 'tracking_number']);
        $this->assertSame(OrderStatus::PROCESSING, $shopOrder->fresh()->status);
    }

    public function test_seller_cannot_manage_another_shops_order(): void
    {
        $sellerA = User::factory()->seller()->create();
        $shopA = Shop::factory()->approved()->create(['user_id' => $sellerA->id]);

        $sellerB = User::factory()->seller()->create();
        $shopB = Shop::factory()->approved()->create(['user_id' => $sellerB->id]);

        [$order, $shopOrderB] = $this->createOrderForShop($shopB, OrderStatus::PROCESSING);

        // Seller A tries to view Seller B's order
        $this->actingAs($sellerA)->get(route('seller.orders.show', $shopOrderB))
            ->assertForbidden();

        // Seller A tries to ship Seller B's order
        $this->actingAs($sellerA)->patch(route('seller.orders.status.update', $shopOrderB), [
            'status' => 'shipped',
            'courier' => 'JNE',
            'tracking_number' => 'JNE9999999',
        ])->assertForbidden();
    }

    public function test_seller_can_complete_shipped_order(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        [$order, $shopOrder] = $this->createOrderForShop($shop, OrderStatus::PROCESSING);

        $shopOrder->update([
            'status' => OrderStatus::SHIPPED,
            'courier' => 'J&T Express',
            'tracking_number' => 'JT888888',
            'shipped_at' => now(),
        ]);

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('seller.orders.show', $shopOrder));
        $this->assertSame(OrderStatus::COMPLETED, $shopOrder->fresh()->status);
        $this->assertSame(OrderStatus::COMPLETED, $order->fresh()->status);
    }
}

