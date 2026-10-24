<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_have_parent_orders(): void
    {
        $buyer = User::factory()->buyer()->create();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-TEST-001',
            'status' => OrderStatus::PENDING,
            'total_amount' => 250000.00,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_number' => 'ORD-TEST-001',
            'user_id' => $buyer->id,
            'status' => OrderStatus::PENDING->value,
            'total_amount' => 250000.00,
        ]);

        $this->assertTrue($buyer->orders->first()->is($order));
        $this->assertTrue($order->user->is($buyer));
        $this->assertSame(OrderStatus::PENDING, $order->status);
    }

    public function test_parent_order_has_child_shop_orders(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop1 = Shop::factory()->approved()->create(['name' => 'Toko Gadget']);
        $shop2 = Shop::factory()->approved()->create(['name' => 'Toko Fashion']);

        $parentOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-PARENT-100',
            'status' => OrderStatus::PENDING,
            'total_amount' => 500000.00,
        ]);

        $child1 = ShopOrder::create([
            'order_id' => $parentOrder->id,
            'shop_id' => $shop1->id,
            'shop_name' => $shop1->name,
            'order_number' => 'ORD-PARENT-100-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => 300000.00,
        ]);

        $child2 = ShopOrder::create([
            'order_id' => $parentOrder->id,
            'shop_id' => $shop2->id,
            'shop_name' => $shop2->name,
            'order_number' => 'ORD-PARENT-100-S2',
            'status' => OrderStatus::PENDING,
            'subtotal' => 200000.00,
        ]);

        $this->assertCount(2, $parentOrder->shopOrders);
        $this->assertTrue($child1->order->is($parentOrder));
        $this->assertTrue($child1->shop->is($shop1));
        $this->assertSame('Toko Gadget', $child1->shop_name);
        $this->assertSame(300000.00, (float) $child1->subtotal);
    }

    public function test_child_shop_order_has_order_items_with_historical_snapshots(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create(['name' => 'Toko Alat Tulis']);
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Buku Tulis Premium 100 Lembar',
            'price' => 25000.00,
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-HIST-001',
            'status' => OrderStatus::PENDING,
            'total_amount' => 50000.00,
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-HIST-001-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => 50000.00,
        ]);

        $item = OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => $product->price,
            'quantity' => 2,
            'subtotal' => 50000.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => 'Buku Tulis Premium 100 Lembar',
            'quantity' => 2,
            'unit_price' => 25000.00,
            'subtotal' => 50000.00,
        ]);

        $this->assertTrue($shopOrder->items->first()->is($item));
        $this->assertSame(50000.00, (float) $item->subtotal);

        // Verify snapshot integrity: If product price or name changes in future, historical order item stays intact
        $product->update([
            'name' => 'Buku Tulis Super Murah',
            'price' => 10000.00,
        ]);

        $item->refresh();
        $this->assertSame('Buku Tulis Premium 100 Lembar', $item->product_name);
        $this->assertSame(25000.00, (float) $item->unit_price);
        $this->assertSame(50000.00, (float) $item->subtotal);
    }

    public function test_deleting_product_preserves_order_item_historical_record(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Mouse Gaming RGB',
            'price' => 150000.00,
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-PRESERVE-001',
            'status' => OrderStatus::PAID,
            'total_amount' => 150000.00,
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-PRESERVE-001-S1',
            'status' => OrderStatus::PAID,
            'subtotal' => 150000.00,
        ]);

        $item = OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => $product->price,
            'quantity' => 1,
            'subtotal' => 150000.00,
        ]);

        // Delete the product
        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);

        // Item in order history remains with null product_id but preserved snapshot details
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'product_id' => null,
            'product_name' => 'Mouse Gaming RGB',
            'subtotal' => 150000.00,
        ]);

        $item->refresh();
        $this->assertNull($item->product_id);
        $this->assertSame('Mouse Gaming RGB', $item->product_name);
    }

    public function test_deleting_shop_preserves_shop_order_historical_record(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create(['name' => 'Toko Elektronik Makmur']);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-SHOP-DEL-001',
            'status' => OrderStatus::PAID,
            'total_amount' => 100000.00,
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-SHOP-DEL-001-S1',
            'status' => OrderStatus::PAID,
            'subtotal' => 100000.00,
        ]);

        // Delete the shop
        $shop->delete();

        $this->assertDatabaseMissing('shops', ['id' => $shop->id]);

        // Shop order record remains with null shop_id and preserved shop_name
        $this->assertDatabaseHas('shop_orders', [
            'id' => $shopOrder->id,
            'shop_id' => null,
            'shop_name' => 'Toko Elektronik Makmur',
        ]);
    }

    public function test_existing_cart_relations_remain_intact_and_unaffected(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 50000.00,
            'stock' => 10,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
        $this->assertSame(100000.00, $cart->subtotal());
    }
}
