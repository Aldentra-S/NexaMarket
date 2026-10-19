<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_is_accessible_to_guests(): void
    {
        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Jelajahi Produk Berkualitas');
    }

    public function test_public_catalog_displays_published_products_from_approved_shops(): void
    {
        $shop = Shop::factory()->approved()->create(['name' => 'Toko Elektronik Hebat']);
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Mouse Wireless Ergonomis',
            'price' => 175000,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Mouse Wireless Ergonomis');
        $response->assertSee('Toko Elektronik Hebat');
        $response->assertSee('175.000');
    }

    public function test_public_catalog_hides_draft_and_archived_products(): void
    {
        $shop = Shop::factory()->approved()->create();
        $draftProduct = Product::factory()->draft()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Masih Draft Rahasia',
        ]);
        $archivedProduct = Product::factory()->archived()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Sudah Diarsipkan Usang',
        ]);
        $publishedProduct = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Publik Siap Beli',
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Produk Publik Siap Beli');
        $response->assertDontSee('Produk Masih Draft Rahasia');
        $response->assertDontSee('Produk Sudah Diarsipkan Usang');
    }

    public function test_public_catalog_hides_products_from_pending_rejected_and_suspended_shops(): void
    {
        // 1. Pending shop
        $pendingShop = Shop::factory()->create(['status' => ShopStatus::PENDING]);
        Product::factory()->published()->create([
            'shop_id' => $pendingShop->id,
            'name' => 'Produk Toko Pending',
        ]);

        // 2. Rejected shop
        $rejectedShop = Shop::factory()->rejected()->create();
        Product::factory()->published()->create([
            'shop_id' => $rejectedShop->id,
            'name' => 'Produk Toko Ditolak',
        ]);

        // 3. Suspended shop
        $suspendedShop = Shop::factory()->suspended('Pelanggaran aturan.')->create();
        Product::factory()->published()->create([
            'shop_id' => $suspendedShop->id,
            'name' => 'Produk Toko Ditangguhkan',
        ]);

        // 4. Approved shop
        $approvedShop = Shop::factory()->approved()->create();
        Product::factory()->published()->create([
            'shop_id' => $approvedShop->id,
            'name' => 'Produk Toko Aktif Resmi',
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Produk Toko Aktif Resmi');
        $response->assertDontSee('Produk Toko Pending');
        $response->assertDontSee('Produk Toko Ditolak');
        $response->assertDontSee('Produk Toko Ditangguhkan');
    }

    public function test_public_catalog_search_filters_products_by_name_or_description(): void
    {
        $shop = Shop::factory()->approved()->create();
        Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Keyboard Mekanikal RGB',
            'description' => 'Switch biru tactile memuaskan untuk mengetik cepat.',
        ]);
        Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Earphone Bluetooth ANC',
            'description' => 'Peredam bising aktif baterai tahan 30 jam.',
        ]);

        // Search for keyboard
        $response = $this->get(route('products.index', ['q' => 'Keyboard']));
        $response->assertOk();
        $response->assertSee('Keyboard Mekanikal RGB');
        $response->assertDontSee('Earphone Bluetooth ANC');

        // Search for description keyword "bising"
        $descResponse = $this->get(route('products.index', ['q' => 'bising']));
        $descResponse->assertOk();
        $descResponse->assertSee('Earphone Bluetooth ANC');
        $descResponse->assertDontSee('Keyboard Mekanikal RGB');
    }

    public function test_public_catalog_sorts_products_by_price(): void
    {
        $shop = Shop::factory()->approved()->create();
        $cheap = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Murah Meriah',
            'price' => 15000,
        ]);
        $expensive = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Mewah Premium',
            'price' => 950000,
        ]);

        // Sort termurah (asc)
        $termurahResponse = $this->get(route('products.index', ['sort' => 'termurah']));
        $termurahResponse->assertOk();
        $termurahResponse->assertSeeInOrder(['Produk Murah Meriah', 'Produk Mewah Premium']);

        // Sort termahal (desc)
        $termahalResponse = $this->get(route('products.index', ['sort' => 'termahal']));
        $termahalResponse->assertOk();
        $termahalResponse->assertSeeInOrder(['Produk Mewah Premium', 'Produk Murah Meriah']);
    }

    public function test_public_product_detail_page_is_accessible_for_active_product(): void
    {
        $shop = Shop::factory()->approved()->create([
            'name' => 'Toko Buku Cendekia',
            'slug' => 'toko-buku-cendekia',
        ]);
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Buku Arsitektur Software Modern',
            'slug' => 'buku-arsitektur-software-modern',
            'description' => 'Panduan komprehensif merancang arsitektur aplikasi skala besar.',
            'price' => 145000,
            'stock' => 15,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Buku Arsitektur Software Modern');
        $response->assertSee('Toko Buku Cendekia');
        $response->assertSee('145.000');
        $response->assertSee('15 unit');
    }

    public function test_public_product_detail_returns_404_for_draft_or_archived_product(): void
    {
        $shop = Shop::factory()->approved()->create();
        $draft = Product::factory()->draft()->create([
            'shop_id' => $shop->id,
            'slug' => 'produk-draft-tersembunyi',
        ]);
        $archived = Product::factory()->archived()->create([
            'shop_id' => $shop->id,
            'slug' => 'produk-archived-usang',
        ]);

        $this->get(route('products.show', $draft->slug))->assertNotFound();
        $this->get(route('products.show', $archived->slug))->assertNotFound();
    }

    public function test_public_product_detail_returns_404_for_product_from_inactive_shop(): void
    {
        $suspendedShop = Shop::factory()->suspended('Pelanggaran.')->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $suspendedShop->id,
            'slug' => 'produk-toko-terlarang',
        ]);

        $this->get(route('products.show', $product->slug))->assertNotFound();
    }
}
