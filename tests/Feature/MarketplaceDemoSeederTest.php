<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use App\Models\User;
use Database\Seeders\MarketplaceDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class MarketplaceDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_seeder_runs_successfully_and_populates_expected_entities(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        // Verify categories count
        $this->assertEquals(5, Category::count());
        $this->assertDatabaseHas('categories', ['slug' => 'elektronik-komputer']);
        $this->assertDatabaseHas('categories', ['slug' => 'fashion-aksesori']);
        $this->assertDatabaseHas('categories', ['slug' => 'rumah-perlengkapan-meja']);
        $this->assertDatabaseHas('categories', ['slug' => 'alat-tulis-belajar']);
        $this->assertDatabaseHas('categories', ['slug' => 'tas-kebutuhan-harian']);

        // Verify seller users count
        $this->assertEquals(5, User::where('email', 'like', 'seller.%@nexamarket.test')->count());

        // Verify shops count & statuses (4 approved, 1 pending)
        $this->assertEquals(5, Shop::count());
        $this->assertEquals(4, Shop::where('status', ShopStatus::APPROVED)->count());
        $this->assertEquals(1, Shop::where('status', ShopStatus::PENDING)->count());

        // Verify products count (20 in approved shops + 1 in pending shop)
        $this->assertEquals(21, Product::count());
        $this->assertEquals(20, Product::published()->forActiveShop()->count());

        // Verify product images created and stored
        $this->assertEquals(21, ProductImage::count());
    }

    public function test_seeder_is_strictly_idempotent_when_run_multiple_times(): void
    {
        // First run
        $this->seed(MarketplaceDemoSeeder::class);

        $initialUserCount = User::count();
        $initialShopCount = Shop::count();
        $initialCategoryCount = Category::count();
        $initialProductCount = Product::count();
        $initialImageCount = ProductImage::count();

        // Second run
        $this->seed(MarketplaceDemoSeeder::class);

        $this->assertEquals($initialUserCount, User::count(), 'Running seeder twice should not create duplicate users.');
        $this->assertEquals($initialShopCount, Shop::count(), 'Running seeder twice should not create duplicate shops.');
        $this->assertEquals($initialCategoryCount, Category::count(), 'Running seeder twice should not create duplicate categories.');
        $this->assertEquals($initialProductCount, Product::count(), 'Running seeder twice should not create duplicate products.');
        $this->assertEquals($initialImageCount, ProductImage::count(), 'Running seeder twice should not create duplicate product images.');
    }

    public function test_sellers_and_shops_have_correct_roles_and_relations(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        // 1. PixelGear Store (Approved Seller)
        $pixelUser = User::where('email', 'seller.pixelgear@nexamarket.test')->firstOrFail();
        $this->assertSame(UserRole::SELLER, $pixelUser->role);
        $this->assertTrue(Hash::check('password123', $pixelUser->password));
        $this->assertNotNull($pixelUser->email_verified_at);
        $this->assertNotNull($pixelUser->shop);
        $this->assertSame('pixelgear-store', $pixelUser->shop->slug);
        $this->assertSame(ShopStatus::APPROVED, $pixelUser->shop->status);
        $this->assertNotNull($pixelUser->shop->approved_at);

        // 2. DailyCarry Goods (Pending Seller / Lifecycle Onboarding)
        $dailyUser = User::where('email', 'seller.dailycarry@nexamarket.test')->firstOrFail();
        $this->assertSame(UserRole::BUYER, $dailyUser->role, 'Pending seller remains BUYER until approved by admin.');
        $this->assertTrue(Hash::check('password123', $dailyUser->password));
        $this->assertNotNull($dailyUser->shop);
        $this->assertSame('dailycarry-goods', $dailyUser->shop->slug);
        $this->assertSame(ShopStatus::PENDING, $dailyUser->shop->status);
        $this->assertNull($dailyUser->shop->approved_at);
    }

    public function test_products_of_approved_shops_are_visible_in_public_catalog(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        // Public catalog listing page
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);

        // Check products from approved shops visible on default page 1 (latest items)
        $response->assertSee('Threadbound Dotted Hardcover Journal 160 GSM');
        $response->assertSee('AuraGlow Desk Screenbar Light');

        // Check products with category filter (Elektronik & Komputer)
        $catResponse = $this->get(route('products.index', ['category' => 'elektronik-komputer']));
        $catResponse->assertStatus(200);
        $catResponse->assertSee('NexaMechanical RGB Keyboard 75%');
        $catResponse->assertSee('ProGlide Wireless Gaming Mouse');

        // Check products on page 2 of general catalog
        $page2Response = $this->get(route('products.index', ['page' => 2]));
        $page2Response->assertStatus(200);
        $page2Response->assertSee('NexaMechanical RGB Keyboard 75%');

        // Public product detail page
        $detailResponse = $this->get(route('products.show', 'nexamechanical-rgb-keyboard-75'));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('PixelGear Store');
        $detailResponse->assertSee('649.000');
    }

    public function test_products_of_pending_shop_do_not_leak_into_public_catalog(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        // Product from DailyCarry Goods (Pending Shop)
        $pendingProductSlug = 'venture-weatherproof-commuter-backpack';

        // Must not be visible on public catalog index
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Venture Weatherproof Commuter Backpack');

        // Accessing detail of a pending shop product must return 404
        $detailResponse = $this->get(route('products.show', $pendingProductSlug));
        $detailResponse->assertStatus(404);
    }

    public function test_approved_seller_can_access_seller_dashboard_but_forbidden_from_admin(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        $seller = User::where('email', 'seller.pixelgear@nexamarket.test')->firstOrFail();

        // Seller Center access
        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertStatus(200);

        // Admin Center access must be forbidden (403)
        $this->actingAs($seller)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    public function test_pending_seller_cannot_access_seller_dashboard(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        $pendingSeller = User::where('email', 'seller.dailycarry@nexamarket.test')->firstOrFail();

        // Because role is BUYER and shop is PENDING, accessing /seller/dashboard must be blocked (403 or redirect)
        $response = $this->actingAs($pendingSeller)->get(route('seller.dashboard'));
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_seeder_protects_against_running_in_production(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('MarketplaceDemoSeeder hanya diizinkan untuk lingkungan development lokal atau testing!');

        app()->detectEnvironment(fn () => 'production');

        $seeder = new MarketplaceDemoSeeder();
        $seeder->run();
    }
}
