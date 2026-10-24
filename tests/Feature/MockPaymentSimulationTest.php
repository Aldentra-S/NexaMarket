<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MockPaymentSimulationTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingOrderWithProduct(User $buyer, int $stock = 10, int $quantity = 2): array
    {
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 50000.00,
            'stock' => $stock - $quantity, // simulates stock was already decremented at checkout
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-PAY-TEST-' . uniqid(),
            'status' => OrderStatus::PENDING,
            'total_amount' => 100000.00,
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => $order->order_number . '-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => 100000.00,
        ]);

        $item = OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => $product->price,
            'quantity' => $quantity,
            'subtotal' => 100000.00,
        ]);

        return [$order, $product, $shopOrder, $item];
    }

    public function test_mock_payment_success_transitions_order_and_shop_orders_to_paid(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product, $shopOrder] = $this->createPendingOrderWithProduct($buyer, stock: 10, quantity: 2);

        $response = $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), [
            'result' => 'success',
        ]);

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('success');

        $order->refresh();
        $shopOrder->refresh();

        $this->assertSame(OrderStatus::PAID, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(OrderStatus::PAID, $shopOrder->status);

        // Stock remains decremented (8)
        $this->assertSame(8, $product->fresh()->stock);

        // Payment record is created
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => PaymentStatus::SUCCESS->value,
            'amount' => 100000.00,
        ]);
    }

    public function test_mock_payment_failed_cancels_order_and_restores_product_stock_atomically(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product, $shopOrder] = $this->createPendingOrderWithProduct($buyer, stock: 10, quantity: 2);

        $response = $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), [
            'result' => 'failed',
        ]);

        $response->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $shopOrder->refresh();

        $this->assertSame(OrderStatus::CANCELLED, $order->status);
        $this->assertNotNull($order->cancelled_at);
        $this->assertSame(OrderStatus::CANCELLED, $shopOrder->status);

        // Stock restored back to 10 (8 + 2)
        $this->assertSame(10, $product->fresh()->stock);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => PaymentStatus::FAILED->value,
        ]);
    }

    public function test_mock_payment_expired_expires_order_and_restores_product_stock_atomically(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product, $shopOrder] = $this->createPendingOrderWithProduct($buyer, stock: 10, quantity: 3);

        $response = $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), [
            'result' => 'expired',
        ]);

        $response->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $shopOrder->refresh();

        $this->assertSame(OrderStatus::EXPIRED, $order->status);
        $this->assertSame(OrderStatus::EXPIRED, $shopOrder->status);

        // Stock restored from 7 to 10
        $this->assertSame(10, $product->fresh()->stock);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => PaymentStatus::EXPIRED->value,
        ]);
    }

    public function test_buyer_cannot_simulate_payment_for_another_buyers_order(): void
    {
        $buyer1 = User::factory()->buyer()->create();
        $buyer2 = User::factory()->buyer()->create();
        [$orderBuyer2] = $this->createPendingOrderWithProduct($buyer2);

        $response = $this->actingAs($buyer1)->post(route('orders.payment.simulate', $orderBuyer2), [
            'result' => 'success',
        ]);

        $response->assertForbidden();
    }

    public function test_already_paid_order_cannot_be_reprocessed(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product] = $this->createPendingOrderWithProduct($buyer, stock: 10, quantity: 2);

        // First payment succeeds
        $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), ['result' => 'success']);
        $this->assertSame(OrderStatus::PAID, $order->fresh()->status);
        $this->assertSame(8, $product->fresh()->stock);

        // Attempt second payment
        $response = $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), ['result' => 'success']);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Stock must still be 8
        $this->assertSame(8, $product->fresh()->stock);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_cancelled_or_expired_order_cannot_be_paid_subsequently(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product] = $this->createPendingOrderWithProduct($buyer, stock: 10, quantity: 2);

        // Cancel first
        $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), ['result' => 'failed']);
        $this->assertSame(10, $product->fresh()->stock);

        // Try to pay afterwards
        $response = $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), ['result' => 'success']);
        $response->assertSessionHas('error');

        // Order remains cancelled, stock remains 10 (not decremented again)
        $this->assertSame(OrderStatus::CANCELLED, $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_mock_payment_simulation_is_forbidden_in_production_environment(): void
    {
        config(['app.env' => 'production']);

        $buyer = User::factory()->buyer()->create();
        [$order] = $this->createPendingOrderWithProduct($buyer);

        $response = $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), [
            'result' => 'success',
        ]);

        $response->assertForbidden();
        $this->assertSame(OrderStatus::PENDING, $order->fresh()->status);
    }

    public function test_mock_payment_simulation_is_forbidden_when_midtrans_is_production(): void
    {
        config(['services.midtrans.is_production' => true]);

        $buyer = User::factory()->buyer()->create();
        [$order] = $this->createPendingOrderWithProduct($buyer);

        $response = $this->actingAs($buyer)->post(route('orders.payment.simulate', $order), [
            'result' => 'success',
        ]);

        $response->assertForbidden();
        $this->assertSame(OrderStatus::PENDING, $order->fresh()->status);
    }
}
