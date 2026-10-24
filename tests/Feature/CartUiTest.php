<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_cart_displays_informative_empty_state_and_catalog_link(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee('Keranjang belanja Anda masih kosong');
        $response->assertSee(route('products.index'));
        $response->assertSee('Mulai Berbelanja');
    }

    public function test_cart_displays_items_shop_prices_and_summary(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();

        $shop = Shop::factory()->approved()->create(['name' => 'Toko Gadget Nusantara']);
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Keyboard Mekanikal Pro',
            'price' => 750000,
            'stock' => 10,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($buyer)->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee('Keyboard Mekanikal Pro');
        $response->assertSee('Toko Gadget Nusantara');
        $response->assertSee('Rp 750.000');
        $response->assertSee('Rp 1.500.000'); // Subtotal
        $response->assertSee('2 barang');
        $response->assertSee('Lanjut ke Checkout');
    }

    public function test_cart_displays_warning_badge_when_an_item_is_unavailable(): void
    {
        $buyer = User::factory()->buyer()->create();
        $cart = $buyer->getOrCreateCart();

        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Habis Stok',
            'price' => 100000,
            'stock' => 1,
        ]);

        // Cart item has quantity 3, but stock is now only 1
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($buyer)->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee('Stok Tidak Mencukupi');
        $response->assertSee('Tersedia 1 unit');
    }

    public function test_catalog_product_detail_page_includes_add_to_cart_form_for_published_in_stock_products(): void
    {
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'slug' => 'mouse-wireless-ergonomis',
            'stock' => 15,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee(route('cart.items.store'));
        $response->assertSee('Tambah ke Keranjang');
    }

    public function test_marketplace_navigation_displays_cart_link(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->get(route('products.index'));

        $response->assertOk();
        $response->assertSee(route('cart.index'));
    }
}
