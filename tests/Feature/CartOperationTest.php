<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartOperationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_cart(): void
    {
        $response = $this->get(route('cart.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('cart.items.store'), ['product_id' => 1, 'quantity' => 1]);
        $response->assertRedirect(route('login'));
    }

    public function test_buyer_can_view_cart_page(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->get(route('cart.index'));

        $response->assertOk();
        $response->assertViewIs('cart.index');
    }

    public function test_buyer_can_add_valid_product_to_cart(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 50000,
            'stock' => 10,
        ]);

        $response = $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_same_product_merges_quantity(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 50000,
            'stock' => 10,
        ]);

        $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $cart = $buyer->refresh()->cart;
        $this->assertCount(1, $cart->items);
        $this->assertSame(5, $cart->items->first()->quantity);
    }

    public function test_adding_quantity_exceeding_stock_is_rejected(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 5,
        ]);

        // Attempt to add 6 items when stock is 5
        $response = $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 6,
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id,
        ]);
    }

    public function test_merging_quantity_exceeding_stock_is_rejected(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 5,
        ]);

        // Add 3 first
        $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        // Attempt to add 3 more (total 6 > stock 5)
        $response = $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertSessionHasErrors('quantity');

        // Existing quantity should remain 3
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_draft_or_archived_products_are_rejected(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create();
        $draftProduct = Product::factory()->draft()->create([
            'shop_id' => $shop->id,
            'stock' => 10,
        ]);
        $archivedProduct = Product::factory()->archived()->create([
            'shop_id' => $shop->id,
            'stock' => 10,
        ]);

        $response = $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $draftProduct->id,
            'quantity' => 1,
        ]);
        $response->assertSessionHasErrors('product_id');

        $response = $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $archivedProduct->id,
            'quantity' => 1,
        ]);
        $response->assertSessionHasErrors('product_id');

        $this->assertDatabaseEmpty('cart_items');
    }

    public function test_products_from_non_approved_or_suspended_shops_are_rejected(): void
    {
        $buyer = User::factory()->buyer()->create();

        // Pending shop
        $pendingShop = Shop::factory()->pending()->create();
        $p1 = Product::factory()->published()->create(['shop_id' => $pendingShop->id, 'stock' => 10]);

        $res1 = $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $p1->id,
            'quantity' => 1,
        ]);
        $res1->assertSessionHasErrors('product_id');

        // Suspended shop
        $suspendedShop = Shop::factory()->suspended()->create();
        $p2 = Product::factory()->published()->create(['shop_id' => $suspendedShop->id, 'stock' => 10]);

        $res2 = $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $p2->id,
            'quantity' => 1,
        ]);
        $res2->assertSessionHasErrors('product_id');

        $this->assertDatabaseEmpty('cart_items');
    }

    public function test_client_cannot_manipulate_price_or_subtotal(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => 100000,
            'stock' => 10,
        ]);

        // Attempt to pass malicious price
        $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 1,
            'subtotal' => 2,
        ]);

        $cart = $buyer->refresh()->cart;
        $cartItem = $cart->items->first();

        $this->assertSame(200000.0, $cartItem->subtotal());
        $this->assertSame(200000.0, $cart->subtotal());

    }

    public function test_buyer_can_update_cart_item_quantity(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 10,
        ]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($buyer)->patch(route('cart.items.update', $item), [
            'quantity' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 5,
        ]);
    }

    public function test_updating_cart_item_quantity_beyond_stock_is_rejected(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 5,
        ]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($buyer)->patch(route('cart.items.update', $item), [
            'quantity' => 10,
        ]);

        $response->assertSessionHasErrors('quantity');

        // Quantity in database should remain unchanged
        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 2,
        ]);
    }

    public function test_updating_quantity_to_zero_or_negative_is_rejected(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id, 'stock' => 5]);
        $item = CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]);

        $res0 = $this->actingAs($buyer)->patch(route('cart.items.update', $item), ['quantity' => 0]);
        $res0->assertSessionHasErrors('quantity');

        $resNeg = $this->actingAs($buyer)->patch(route('cart.items.update', $item), ['quantity' => -1]);
        $resNeg->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 2]);
    }

    public function test_buyer_cannot_update_or_delete_another_users_cart_item(): void
    {
        $buyer1 = User::factory()->buyer()->create();
        $buyer2 = User::factory()->buyer()->create();

        $cart2 = $buyer2->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id, 'stock' => 5]);

        $itemBuyer2 = CartItem::create([
            'cart_id' => $cart2->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Buyer 1 tries to update Buyer 2's item
        $response = $this->actingAs($buyer1)->patch(route('cart.items.update', $itemBuyer2), [
            'quantity' => 4,
        ]);
        $response->assertForbidden();

        // Buyer 1 tries to delete Buyer 2's item
        $response = $this->actingAs($buyer1)->delete(route('cart.items.destroy', $itemBuyer2));
        $response->assertForbidden();

        $this->assertDatabaseHas('cart_items', [
            'id' => $itemBuyer2->id,
            'quantity' => 2,
        ]);
    }

    public function test_buyer_can_delete_cart_item_without_deleting_product(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create(['shop_id' => $shop->id, 'stock' => 5]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($buyer)->delete(route('cart.items.destroy', $item));

        $response->assertRedirect();
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_adding_or_updating_cart_does_not_alter_database_product_stock(): void
    {
        $buyer = User::factory()->buyer()->create();
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'stock' => 15,
        ]);

        $this->actingAs($buyer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        // Stock in products table must strictly remain 15
        $this->assertSame(15, $product->fresh()->stock);

        $item = CartItem::where('product_id', $product->id)->first();
        $this->actingAs($buyer)->patch(route('cart.items.update', $item), [
            'quantity' => 10,
        ]);

        // Still 15
        $this->assertSame(15, $product->fresh()->stock);
    }
}
