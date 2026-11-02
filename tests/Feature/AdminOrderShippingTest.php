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

class AdminOrderShippingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_shipping_details_and_tracking_in_order_show(): void
    {
        $admin = User::factory()->admin()->create();
        $buyer = User::factory()->buyer()->create();
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-' . uniqid(),
            'status' => OrderStatus::SHIPPED,
            'total_amount' => 125000.00,
            'shipping_cost_total' => 25000.00,
            'shipping_recipient_name' => 'Raden Saleh',
            'shipping_phone' => '081122334455',
            'shipping_province' => 'DKI Jakarta',
            'shipping_city' => 'Jakarta Pusat',
            'shipping_district' => 'Menteng',
            'shipping_postal_code' => '10310',
            'shipping_address' => 'Jl. Cikini Raya No. 73',
            'shipping_delivery_notes' => 'Dekat bioskop',
            'paid_at' => now(),
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => $order->order_number . '-S1',
            'status' => OrderStatus::SHIPPED,
            'subtotal' => 100000.00,
            'shipping_cost' => 25000.00,
            'courier' => 'Pos Indonesia',
            'tracking_number' => 'POS987654321',
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

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('Raden Saleh');
        $response->assertSee('081122334455');
        $response->assertSee('Jl. Cikini Raya No. 73');
        $response->assertSee('Pos Indonesia');
        $response->assertSee('POS987654321');
        $response->assertSee('Rp 25.000');
    }

    public function test_buyer_cannot_access_admin_order_show(): void
    {
        $buyer = User::factory()->buyer()->create();
        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-' . uniqid(),
            'status' => OrderStatus::PAID,
            'total_amount' => 50000.00,
            'paid_at' => now(),
        ]);

        $this->actingAs($buyer)->get(route('admin.orders.show', $order))
            ->assertForbidden();
    }
}

