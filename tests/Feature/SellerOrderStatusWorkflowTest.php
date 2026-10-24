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
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SellerOrderStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function createSellerWithShop(): array
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);

        return [$seller, $shop];
    }

    private function createPaidOrderWithShop(Shop $shop, float $subtotal = 100000.00): array
    {
        $buyer = User::factory()->buyer()->create();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-' . uniqid(),
            'status' => OrderStatus::PAID,
            'total_amount' => $subtotal,
            'paid_at' => now(),
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => $order->order_number . '-S1',
            'status' => OrderStatus::PAID,
            'subtotal' => $subtotal,
        ]);

        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => $subtotal,
        ]);

        $item = OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => $subtotal,
            'quantity' => 1,
            'subtotal' => $subtotal,
        ]);

        return [$order, $shopOrder, $item, $buyer];
    }

    public function test_seller_can_transition_shop_order_from_paid_to_processing(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        [$order, $shopOrder] = $this->createPaidOrderWithShop($shop);

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'processing',
        ]);

        $response->assertRedirect(route('seller.orders.show', $shopOrder));
        $response->assertSessionHas('success');
        $this->assertSame(OrderStatus::PROCESSING, $shopOrder->fresh()->status);
    }

    public function test_seller_can_transition_shop_order_from_processing_to_shipped(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        [$order, $shopOrder] = $this->createPaidOrderWithShop($shop);

        $shopOrder->update(['status' => OrderStatus::PROCESSING]);

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'shipped',
        ]);

        $response->assertRedirect(route('seller.orders.show', $shopOrder));
        $this->assertSame(OrderStatus::SHIPPED, $shopOrder->fresh()->status);
    }

    public function test_seller_can_transition_shop_order_from_shipped_to_completed(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        [$order, $shopOrder] = $this->createPaidOrderWithShop($shop);

        $shopOrder->update(['status' => OrderStatus::SHIPPED]);

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('seller.orders.show', $shopOrder));
        $this->assertSame(OrderStatus::COMPLETED, $shopOrder->fresh()->status);
    }

    public function test_cannot_process_unpaid_order(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $buyer = User::factory()->buyer()->create();

        $unpaidOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-UNPAID',
            'status' => OrderStatus::PENDING,
            'total_amount' => 50000.00,
        ]);

        $unpaidShopOrder = ShopOrder::create([
            'order_id' => $unpaidOrder->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-UNPAID-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => 50000.00,
        ]);

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $unpaidShopOrder), [
            'status' => 'processing',
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertSame(OrderStatus::PENDING, $unpaidShopOrder->fresh()->status);
    }

    public function test_illegal_status_transitions_are_rejected(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        [$order, $shopOrder] = $this->createPaidOrderWithShop($shop);

        // 1. Cannot jump from paid directly to completed
        $responseJump = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'completed',
        ]);
        $responseJump->assertSessionHasErrors(['status']);
        $this->assertSame(OrderStatus::PAID, $shopOrder->fresh()->status);

        // 2. Cannot regress from processing back to paid
        $shopOrder->update(['status' => OrderStatus::PROCESSING]);
        $responseRegress = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'paid',
        ]);
        $responseRegress->assertSessionHasErrors(['status']);
        $this->assertSame(OrderStatus::PROCESSING, $shopOrder->fresh()->status);

        // 3. Cannot regress from shipped back to processing
        $shopOrder->update(['status' => OrderStatus::SHIPPED]);
        $responseShippedBack = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'processing',
        ]);
        $responseShippedBack->assertSessionHasErrors(['status']);
        $this->assertSame(OrderStatus::SHIPPED, $shopOrder->fresh()->status);

        // 4. Cannot modify a completed order
        $shopOrder->update(['status' => OrderStatus::COMPLETED]);
        $responseModifyCompleted = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'shipped',
        ]);
        $responseModifyCompleted->assertSessionHasErrors(['status']);
        $this->assertSame(OrderStatus::COMPLETED, $shopOrder->fresh()->status);
    }

    public function test_seller_cannot_mark_order_as_paid_via_fulfillment(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        $buyer = User::factory()->buyer()->create();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-PENDING-ATTEMPT',
            'status' => OrderStatus::PENDING,
            'total_amount' => 50000.00,
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-PENDING-ATTEMPT-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => 50000.00,
        ]);

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), [
            'status' => 'paid',
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertSame(OrderStatus::PENDING, $shopOrder->fresh()->status);
    }

    public function test_seller_cannot_update_order_of_another_shop(): void
    {
        [$sellerA, $shopA] = $this->createSellerWithShop();
        [$sellerB, $shopB] = $this->createSellerWithShop();
        [$order, $shopOrderB] = $this->createPaidOrderWithShop($shopB);

        $response = $this->actingAs($sellerA)->patch(route('seller.orders.status.update', $shopOrderB), [
            'status' => 'processing',
        ]);

        $response->assertForbidden();
        $this->assertSame(OrderStatus::PAID, $shopOrderB->fresh()->status);
    }

    public function test_seller_cannot_alter_order_totals_prices_items_or_buyer_info_during_fulfillment(): void
    {
        [$seller, $shop] = $this->createSellerWithShop();
        [$order, $shopOrder, $item, $buyer] = $this->createPaidOrderWithShop($shop, 100000.00);

        $maliciousPayload = [
            'status' => 'processing',
            'subtotal' => 10.00,
            'total_amount' => 10.00,
            'order_number' => 'HACKED-NUMBER',
            'user_id' => 99999,
            'unit_price' => 5.00,
        ];

        $response = $this->actingAs($seller)->patch(route('seller.orders.status.update', $shopOrder), $maliciousPayload);

        $response->assertRedirect(route('seller.orders.show', $shopOrder));
        
        $freshShopOrder = $shopOrder->fresh();
        $freshOrder = $order->fresh();
        $freshItem = $item->fresh();

        $this->assertSame(OrderStatus::PROCESSING, $freshShopOrder->status);
        $this->assertEquals(100000.00, (float) $freshShopOrder->subtotal);
        $this->assertEquals(100000.00, (float) $freshOrder->total_amount);
        $this->assertEquals(100000.00, (float) $freshItem->unit_price);
        $this->assertEquals(100000.00, (float) $freshItem->subtotal);
        $this->assertSame($order->order_number, $freshOrder->order_number);
        $this->assertSame($buyer->id, $freshOrder->user_id);
    }

    public function test_parent_order_does_not_become_completed_until_all_child_shop_orders_are_completed(): void
    {
        [$sellerA, $shopA] = $this->createSellerWithShop();
        [$sellerB, $shopB] = $this->createSellerWithShop();
        $buyer = User::factory()->buyer()->create();

        // Multi-vendor parent order
        $parentOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-MV-TEST',
            'status' => OrderStatus::PAID,
            'total_amount' => 250000.00,
            'paid_at' => now(),
        ]);

        $shopOrderA = ShopOrder::create([
            'order_id' => $parentOrder->id,
            'shop_id' => $shopA->id,
            'shop_name' => $shopA->name,
            'order_number' => 'ORD-MV-TEST-SA',
            'status' => OrderStatus::SHIPPED,
            'subtotal' => 150000.00,
        ]);

        $shopOrderB = ShopOrder::create([
            'order_id' => $parentOrder->id,
            'shop_id' => $shopB->id,
            'shop_name' => $shopB->name,
            'order_number' => 'ORD-MV-TEST-SB',
            'status' => OrderStatus::PAID,
            'subtotal' => 100000.00,
        ]);

        // Seller A completes their shop order
        $responseA = $this->actingAs($sellerA)->patch(route('seller.orders.status.update', $shopOrderA), [
            'status' => 'completed',
        ]);
        $responseA->assertRedirect(route('seller.orders.show', $shopOrderA));

        // ShopOrder A is completed, but ShopOrder B is still PAID
        $this->assertSame(OrderStatus::COMPLETED, $shopOrderA->fresh()->status);
        $this->assertSame(OrderStatus::PAID, $shopOrderB->fresh()->status);

        // Parent Order MUST NOT be completed yet
        $this->assertNotSame(OrderStatus::COMPLETED, $parentOrder->fresh()->status);
        $this->assertSame(OrderStatus::PAID, $parentOrder->fresh()->status);

        // Now Shop B advances their order: PAID -> PROCESSING -> SHIPPED -> COMPLETED
        $shopOrderB->update(['status' => OrderStatus::SHIPPED]);

        $responseB = $this->actingAs($sellerB)->patch(route('seller.orders.status.update', $shopOrderB), [
            'status' => 'completed',
        ]);
        $responseB->assertRedirect(route('seller.orders.show', $shopOrderB));

        // Both child orders are completed
        $this->assertSame(OrderStatus::COMPLETED, $shopOrderA->fresh()->status);
        $this->assertSame(OrderStatus::COMPLETED, $shopOrderB->fresh()->status);

        // Now parent Order MUST automatically become COMPLETED!
        $this->assertSame(OrderStatus::COMPLETED, $parentOrder->fresh()->status);
    }
}
