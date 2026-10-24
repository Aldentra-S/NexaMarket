<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_have_a_persisted_cart(): void
    {
        $user = User::factory()->buyer()->create();

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->cart->is($cart));
        $this->assertTrue($cart->user->is($user));
    }

    public function test_user_can_only_have_one_cart(): void
    {
        $user = User::factory()->buyer()->create();

        Cart::create(['user_id' => $user->id]);

        $this->expectException(QueryException::class);
        Cart::create(['user_id' => $user->id]);
    }

    public function test_product_can_be_associated_as_cart_item(): void
    {
        $user = User::factory()->buyer()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 150000,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertTrue($cart->items->first()->is($cartItem));
        $this->assertTrue($cartItem->product->is($product));
        $this->assertSame(300000.0, (float) $cartItem->subtotal());
    }

    public function test_same_product_cannot_be_duplicated_in_the_same_cart(): void
    {
        $user = User::factory()->buyer()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->expectException(QueryException::class);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_different_carts_can_have_the_same_product(): void
    {
        $user1 = User::factory()->buyer()->create();
        $cart1 = Cart::create(['user_id' => $user1->id]);

        $user2 = User::factory()->buyer()->create();
        $cart2 = Cart::create(['user_id' => $user2->id]);

        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id]);

        $item1 = CartItem::create([
            'cart_id' => $cart1->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $item2 = CartItem::create([
            'cart_id' => $cart2->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);

        $this->assertDatabaseHas('cart_items', ['id' => $item1->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $item2->id]);
    }

    public function test_deleting_cart_cascades_and_deletes_its_cart_items(): void
    {
        $user = User::factory()->buyer()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $cart->delete();

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
        // Product itself is NOT deleted
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_deleting_product_cascades_and_removes_it_from_all_carts(): void
    {
        $user = User::factory()->buyer()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
        // Cart itself remains
        $this->assertDatabaseHas('carts', ['id' => $cart->id]);
    }

    public function test_deleting_user_cascades_and_removes_their_cart_and_items(): void
    {
        $user = User::factory()->buyer()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_cart_calculates_total_quantity_and_subtotal_accurately(): void
    {
        $user = User::factory()->buyer()->create();
        $cart = $user->getOrCreateCart();

        $shop = Shop::factory()->approved()->create();
        $product1 = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 50000.50,
        ]);
        $product2 = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 25000.00,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 3,
        ]);

        $cart->load('items.product');

        $this->assertSame(5, $cart->totalQuantity());
        $this->assertSame(175001.0, $cart->subtotal());
    }

    public function test_cart_item_reports_availability_status_accurately(): void
    {
        $user = User::factory()->buyer()->create();
        $cart = $user->getOrCreateCart();

        $shop = Shop::factory()->approved()->create();
        $activeProduct = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 5,
        ]);
        $draftProduct = Product::factory()->draft()->create([
            'shop_id' => $shop->id,
            'stock' => 10,
        ]);

        $item1 = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $activeProduct->id,
            'quantity' => 3,
        ]);

        $item2 = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $draftProduct->id,
            'quantity' => 1,
        ]);

        $this->assertTrue($item1->isAvailable());
        $this->assertFalse($item2->isAvailable());

        // When quantity exceeds available stock
        $item1->quantity = 10;
        $this->assertFalse($item1->isAvailable());
    }
}

