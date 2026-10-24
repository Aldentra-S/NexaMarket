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

class SellerOrderAccessTest extends TestCase
{
    use RefreshDatabase;

    private function createSellerWithShop(): array
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);

        return [$seller, $shop];
    }

    public function test_guest_and_buyer_cannot_access_seller_orders(): void
    {
        $buyer = User::factory()->buyer()->create();

        $resGuest = $this->get(route('seller.orders.index'));
        $resGuest->assertRedirect(route('login'));

        $resBuyer = $this->actingAs($buyer)->get(route('seller.orders.index'));
        $resBuyer->assertForbidden();
    }

    public function test_seller_can_view_orders_belonging_to_their_shop(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $buyer = User::factory()->buyer()->create();

        $parentOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-20261024-TEST01',
            'status' => OrderStatus::PAID,
            'total_amount' => 150000.00,
            'paid_at' => now(),
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $parentOrder->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-20261024-TEST01-S1',
            'status' => OrderStatus::PAID,
            'subtotal' => 150000.00,
        ]);

        $product = Product::factory()->published()->create(['shop_id' => $shop->id]);
        OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => 'Kemeja Katun Polos',
            'product_slug' => 'kemeja-katun-polos',
            'unit_price' => 75000.00,
            'quantity' => 2,
            'subtotal' => 150000.00,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.orders.index'));

        $response->assertOk();
        $response->assertViewIs('seller.orders.index');
        $response->assertSee('ORD-20261024-TEST01-S1');
        $response->assertSee('Kemeja Katun Polos');
        $response->assertSee('Rp 150.000');
        $response->assertSee(route('seller.orders.show', $shopOrder));
    }

    public function test_seller_cannot_see_or_access_orders_belonging_to_another_shop(): void
    {
        [$sellerA, $shopA] = $this->createSellerWithShop();
        [$sellerB, $shopB] = $this->createSellerWithShop();
        $buyer = User::factory()->buyer()->create();

        $parentOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-DIFF-01',
            'status' => OrderStatus::PAID,
            'total_amount' => 200000.00,
        ]);

        // ShopOrder belongs to Shop B
        $shopOrderB = ShopOrder::create([
            'order_id' => $parentOrder->id,
            'shop_id' => $shopB->id,
            'shop_name' => $shopB->name,
            'order_number' => 'ORD-DIFF-01-SB',
            'status' => OrderStatus::PAID,
            'subtotal' => 200000.00,
        ]);

        // Seller A lists their orders -> must NOT see Shop B's order
        $responseList = $this->actingAs($sellerA)->get(route('seller.orders.index'));
        $responseList->assertOk();
        $responseList->assertDontSee('ORD-DIFF-01-SB');

        // Seller A tries to directly view Shop B's order by ID in URL -> must be 403 Forbidden
        $responseShow = $this->actingAs($sellerA)->get(route('seller.orders.show', $shopOrderB));
        $responseShow->assertForbidden();
    }

    public function test_seller_can_view_order_details_with_buyer_info_and_snapshot_prices(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $buyer = User::factory()->buyer()->create(['name' => 'Budi Pembeli Santosa']);

        $parentOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-SHOW-01',
            'status' => OrderStatus::PAID,
            'total_amount' => 120000.00,
            'paid_at' => now(),
            'notes' => 'Tolong kirim sebelum jam 3 sore.',
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $parentOrder->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-SHOW-01-S1',
            'status' => OrderStatus::PAID,
            'subtotal' => 120000.00,
        ]);

        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Kopi Robusta 250g',
            'price' => 60000.00,
        ]);

        OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => 'Kopi Robusta 250g',
            'product_slug' => 'kopi-robusta-250g',
            'unit_price' => 60000.00,
            'quantity' => 2,
            'subtotal' => 120000.00,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertViewIs('seller.orders.show');
        $response->assertSee('ORD-SHOW-01-S1');
        $response->assertSee('Budi Pembeli Santosa');
        $response->assertSee('Kopi Robusta 250g');
        $response->assertSee('Rp 60.000');
        $response->assertSee('Rp 120.000');
        $response->assertSee('Tolong kirim sebelum jam 3 sore.');
    }

    public function test_unpaid_order_is_clearly_indicated_as_not_ready_for_processing(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $buyer = User::factory()->buyer()->create();

        $unpaidParentOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-UNPAID-01',
            'status' => OrderStatus::PENDING,
            'total_amount' => 100000.00,
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $unpaidParentOrder->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-UNPAID-01-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => 100000.00,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.orders.show', $shopOrder));

        $response->assertOk();
        $response->assertSee('Menunggu Pembayaran');
        $response->assertSee('Pesanan ini belum dibayar oleh pembeli dan belum dapat diproses.');
    }
}
