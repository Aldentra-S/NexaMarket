<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use App\Services\Gateways\MidtransPaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutShippingTest extends TestCase
{
    use RefreshDatabase;

    private function setupMultiVendorCart(User $buyer, float $shippingCostShopA = 10000.00, float $shippingCostShopB = 15000.00): array
    {
        $sellerA = User::factory()->seller()->create();
        $shopA = Shop::factory()->approved()->create([
            'user_id' => $sellerA->id,
            'shipping_cost' => $shippingCostShopA,
        ]);
        $productA = Product::factory()->published()->create([
            'shop_id' => $shopA->id,
            'price' => 50000.00,
            'stock' => 10,
        ]);

        $sellerB = User::factory()->seller()->create();
        $shopB = Shop::factory()->approved()->create([
            'user_id' => $sellerB->id,
            'shipping_cost' => $shippingCostShopB,
        ]);
        $productB = Product::factory()->published()->create([
            'shop_id' => $shopB->id,
            'price' => 75000.00,
            'stock' => 10,
        ]);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $productA->id,
            'quantity' => 2, // 100,000
        ]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $productB->id,
            'quantity' => 1, // 75,000
        ]);

        return [$shopA, $productA, $shopB, $productB];
    }

    public function test_checkout_saves_shipping_address_snapshot_and_calculates_total_with_shipping(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$shopA, $productA, $shopB, $productB] = $this->setupMultiVendorCart($buyer, 10000.00, 15000.00);

        $address = Address::factory()->create([
            'user_id' => $buyer->id,
            'recipient_name' => 'Siti Nurhaliza',
            'phone' => '081234567890',
            'province' => 'Jawa Timur',
            'city' => 'Surabaya',
            'district' => 'Wonokromo',
            'village' => null,
            'postal_code' => '60241',
            'address_line' => 'Jl. Diponegoro No. 45',
            'delivery_notes' => 'Titip di satpam',
            'is_default' => true,
        ]);

        $idempotencyKey = (string) Str::uuid();

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => $idempotencyKey,
            'address_id' => $address->id,
            'notes' => 'Mohon dikemas cepat ya',
        ]);

        $response->assertRedirect();

        /** @var Order $order */
        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);

        // Subtotal = (2 * 50000) + (1 * 75000) = 175,000
        // Shipping = 10,000 + 15,000 = 25,000
        // Grand Total = 200,000
        $this->assertEquals(25000.00, (float) $order->shipping_cost_total);
        $this->assertEquals(200000.00, (float) $order->total_amount);
        $this->assertEquals(175000.00, (float) $order->itemsSubtotal());

        // Verify snapshot values
        $this->assertEquals('Siti Nurhaliza', $order->shipping_recipient_name);
        $this->assertEquals('081234567890', $order->shipping_phone);
        $this->assertEquals('Jawa Timur', $order->shipping_province);
        $this->assertEquals('Surabaya', $order->shipping_city);
        $this->assertEquals('Wonokromo', $order->shipping_district);
        $this->assertEquals('60241', $order->shipping_postal_code);
        $this->assertEquals('Jl. Diponegoro No. 45', $order->shipping_address);
        $this->assertEquals('Titip di satpam', $order->shipping_delivery_notes);

        // Verify child shop orders
        $shopOrders = $order->shopOrders;
        $this->assertCount(2, $shopOrders);

        $orderA = $shopOrders->firstWhere('shop_id', $shopA->id);
        $this->assertEquals(10000.00, (float) $orderA->shipping_cost);
        $this->assertEquals(100000.00, (float) $orderA->subtotal);
        $this->assertEquals(110000.00, (float) $orderA->total());

        $orderB = $shopOrders->firstWhere('shop_id', $shopB->id);
        $this->assertEquals(15000.00, (float) $orderB->shipping_cost);
        $this->assertEquals(75000.00, (float) $orderB->subtotal);
        $this->assertEquals(90000.00, (float) $orderB->total());
    }

    public function test_address_deletion_does_not_mutate_existing_order_shipping_snapshot(): void
    {
        $buyer = User::factory()->buyer()->create();
        $this->setupMultiVendorCart($buyer, 10000.00, 10000.00);

        $address = Address::factory()->create([
            'user_id' => $buyer->id,
            'recipient_name' => 'Snapshot Recipient',
            'address_line' => 'Alamat Asli Snapshot',
            'village' => null,
            'is_default' => true,
        ]);

        $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
            'address_id' => $address->id,
        ]);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertEquals('Snapshot Recipient', $order->shipping_recipient_name);

        // Delete address from address book
        $address->delete();

        // Order snapshot must remain intact
        $order->refresh();
        $this->assertEquals('Snapshot Recipient', $order->shipping_recipient_name);
        $this->assertEquals('Alamat Asli Snapshot', $order->shipping_address);
        $this->assertTrue($order->hasShippingAddress());
    }

    public function test_checkout_rejects_another_users_address_id(): void
    {
        $buyer = User::factory()->buyer()->create();
        $otherBuyer = User::factory()->buyer()->create();
        $this->setupMultiVendorCart($buyer);

        $otherAddress = Address::factory()->create(['user_id' => $otherBuyer->id]);

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
            'address_id' => $otherAddress->id,
        ]);

        $response->assertSessionHasErrors(['address_id']);
        $this->assertEquals(0, Order::count());
    }

    public function test_checkout_falls_back_to_default_address_if_not_specified(): void
    {
        $buyer = User::factory()->buyer()->create();
        $this->setupMultiVendorCart($buyer);

        $defaultAddress = Address::factory()->create([
            'user_id' => $buyer->id,
            'recipient_name' => 'Default Recipient',
            'is_default' => true,
        ]);

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $response->assertRedirect();
        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertEquals('Default Recipient', $order->shipping_recipient_name);
    }
}
