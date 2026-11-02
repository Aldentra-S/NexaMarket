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

class BuyerOrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderForBuyer(User $buyer, OrderStatus $status = OrderStatus::SHIPPED): array
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-' . uniqid(),
            'status' => $status,
            'total_amount' => 120000.00,
            'shipping_cost_total' => 20000.00,
            'shipping_recipient_name' => 'Ahmad Dahlan',
            'shipping_phone' => '085678901234',
            'shipping_province' => 'DI Yogyakarta',
            'shipping_city' => 'Yogyakarta',
            'shipping_district' => 'Kraton',
            'shipping_postal_code' => '55131',
            'shipping_address' => 'Jl. Alun-Alun Utara No. 1',
            'shipping_delivery_notes' => 'Pagar kayu cokelat',
            'paid_at' => now(),
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => $order->order_number . '-S1',
            'status' => $status,
            'subtotal' => 100000.00,
            'shipping_cost' => 20000.00,
            'courier' => 'SiCepat',
            'tracking_number' => 'SC1234567890',
            'shipped_at' => now(),
        ]);

        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 100000.00,
        ]);

        OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => 100000.00,
            'quantity' => 1,
            'subtotal' => 100000.00,
        ]);

        return [$order, $shopOrder];
    }

    public function test_buyer_can_view_shipping_tracking_info_and_recipient_details(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $shopOrder] = $this->createOrderForBuyer($buyer);

        $response = $this->actingAs($buyer)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertSee('Informasi Pengiriman');
        $response->assertSee('Ahmad Dahlan');
        $response->assertSee('085678901234');
        $response->assertSee('Jl. Alun-Alun Utara No. 1');
        $response->assertSee('Kec. Kraton');
        $response->assertSee('Yogyakarta');
        $response->assertSee('55131');
        $response->assertSee('Pagar kayu cokelat');
        $response->assertSee('SiCepat');
        $response->assertSee('SC1234567890');
        $response->assertSee('Rp 20.000');
    }

    public function test_buyer_cannot_view_another_buyers_order(): void
    {
        $buyerA = User::factory()->buyer()->create();
        $buyerB = User::factory()->buyer()->create();
        [$orderB] = $this->createOrderForBuyer($buyerB);

        $this->actingAs($buyerA)->get(route('orders.show', $orderB))
            ->assertForbidden();
    }
}
