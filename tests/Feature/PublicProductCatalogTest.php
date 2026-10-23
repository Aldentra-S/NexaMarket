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

    public function test_catalog_displays_product_thumbnails_and_categories_correctly(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $shop = Shop::factory()->approved()->create();
        $category = \App\Models\Category::factory()->create(['name' => 'Elektronik Audio']);
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Speaker Bluetooth Mini',
        ]);

        \Illuminate\Support\Facades\Storage::disk('public')->put('products/speaker.jpg', 'speaker-content');
        $product->images()->create([
            'file_path' => 'products/speaker.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Speaker Bluetooth Mini');
        $response->assertSee('Elektronik Audio');
        $response->assertSee('products/speaker.jpg');
    }

    public function test_category_filter_works_together_with_search_sort_and_pagination(): void
    {
        $shop = Shop::factory()->approved()->create();
        $techCategory = \App\Models\Category::factory()->create(['name' => 'Teknologi Komputer', 'slug' => 'teknologi-komputer']);
        $fashionCategory = \App\Models\Category::factory()->create(['name' => 'Fashion Pria', 'slug' => 'fashion-pria']);

        // Tech products matching search "wireless"
        $cheapTech = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'category_id' => $techCategory->id,
            'name' => 'Wireless Mouse Murah',
            'price' => 50000,
        ]);
        $expensiveTech = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'category_id' => $techCategory->id,
            'name' => 'Wireless Keyboard Mahal',
            'price' => 250000,
        ]);

        // Tech product NOT matching "wireless"
        $otherTech = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'category_id' => $techCategory->id,
            'name' => 'Kabel HDMI 4K',
            'price' => 30000,
        ]);

        // Fashion product matching "wireless" (e.g. ear warmer)
        $fashion = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'category_id' => $fashionCategory->id,
            'name' => 'Wireless Smart Jacket',
            'price' => 500000,
        ]);

        // Filter: category=teknologi-komputer AND q=Wireless AND sort=termurah
        $response = $this->get(route('products.index', [
            'category' => 'teknologi-komputer',
            'q' => 'Wireless',
            'sort' => 'termurah',
        ]));

        $response->assertOk();
        $response->assertSeeInOrder(['Wireless Mouse Murah', 'Wireless Keyboard Mahal']);
        $response->assertDontSee('Kabel HDMI 4K');
        $response->assertDontSee('Wireless Smart Jacket');
    }

    public function test_category_filter_strictly_excludes_draft_and_archived_products(): void
    {
        $shop = Shop::factory()->approved()->create();
        $category = \App\Models\Category::factory()->create(['slug' => 'aksesori-gadget']);

        $draft = Product::factory()->draft()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Powerbank Draft Rahasia',
        ]);

        $archived = Product::factory()->archived()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Powerbank Usang Diarsipkan',
        ]);

        $published = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Powerbank Fast Charging 20000mAh',
        ]);

        $response = $this->get(route('products.index', ['category' => 'aksesori-gadget']));

        $response->assertOk();
        $response->assertSee('Powerbank Fast Charging 20000mAh');
        $response->assertDontSee('Powerbank Draft Rahasia');
        $response->assertDontSee('Powerbank Usang Diarsipkan');
    }

    public function test_product_detail_page_displays_complete_data_including_images_and_category(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $shop = Shop::factory()->approved()->create(['name' => 'Toko Gadget Resmi']);
        $category = \App\Models\Category::factory()->create(['name' => 'Fotografi & Kamera', 'slug' => 'fotografi-kamera']);
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Lensa Kamera Mirrorless 50mm',
            'slug' => 'lensa-kamera-mirrorless-50mm',
            'description' => 'Lensa aperture f/1.8 dengan bokeh halus dan autofokus cepat.',
            'price' => 2450000,
            'stock' => 8,
        ]);

        \Illuminate\Support\Facades\Storage::disk('public')->put('products/lens-main.jpg', 'lens1');
        \Illuminate\Support\Facades\Storage::disk('public')->put('products/lens-side.jpg', 'lens2');

        $product->images()->create([
            'file_path' => 'products/lens-main.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);
        $product->images()->create([
            'file_path' => 'products/lens-side.jpg',
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Lensa Kamera Mirrorless 50mm');
        $response->assertSee('Fotografi & Kamera');
        $response->assertSee('Toko Gadget Resmi');
        $response->assertSee('2.450.000');
        $response->assertSee('8 unit');
        $response->assertSee('Lensa aperture f/1.8 dengan bokeh halus');
        $response->assertSee('products/lens-main.jpg');
        $response->assertSee('products/lens-side.jpg');
    }

    public function test_legacy_products_without_images_or_category_display_without_errors(): void
    {
        $shop = Shop::factory()->approved()->create(['name' => 'Toko Tradisional']);
        $legacyProduct = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'category_id' => null,
            'name' => 'Produk Klasik Tanpa Foto Kategori',
            'slug' => 'produk-klasik-tanpa-foto-kategori',
        ]);

        // Catalog Index
        $indexResponse = $this->get(route('products.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Produk Klasik Tanpa Foto Kategori');
        $indexResponse->assertSee('placeholder-product.svg');

        // Catalog Show
        $showResponse = $this->get(route('products.show', $legacyProduct->slug));
        $showResponse->assertOk();
        $showResponse->assertSee('Produk Klasik Tanpa Foto Kategori');
        $showResponse->assertSee('placeholder-product.svg');
    }
}
