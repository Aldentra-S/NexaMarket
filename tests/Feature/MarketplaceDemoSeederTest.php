<?php

namespace Tests\Feature;

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

        // Verify natural buyer shop owners count
        $this->assertEquals(5, User::whereIn('email', [
            'budi@gmail.com',
            'siti@gmail.com',
            'andi@gmail.com',
            'rina@gmail.com',
            'dika@gmail.com',
        ])->count());

        // Verify all 5 shop owner accounts have BUYER role (no seller role added)
        $shopOwnerUsers = User::whereIn('email', [
            'budi@gmail.com',
            'siti@gmail.com',
            'andi@gmail.com',
            'rina@gmail.com',
            'dika@gmail.com',
        ])->get();

        foreach ($shopOwnerUsers as $user) {
            $this->assertSame(UserRole::BUYER, $user->role, "{$user->email} must strictly have BUYER role.");
            $this->assertTrue($user->isBuyer());
        }

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

    public function test_shop_owners_are_buyers_with_correct_relations_and_credentials(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        // 1. Budi (PixelGear Store - Approved Shop)
        $budi = User::where('email', 'budi@gmail.com')->firstOrFail();
        $this->assertSame(UserRole::BUYER, $budi->role);
        $this->assertTrue($budi->isBuyer());
        $this->assertTrue($budi->hasApprovedShop());
        $this->assertTrue($budi->isSeller(), 'Buyer with approved shop acts as seller.');
        $this->assertTrue(Hash::check('password123', $budi->password));
        $this->assertNotNull($budi->email_verified_at);
        $this->assertNotNull($budi->shop);
        $this->assertSame('pixelgear-store', $budi->shop->slug);
        $this->assertSame(ShopStatus::APPROVED, $budi->shop->status);
        $this->assertNotNull($budi->shop->approved_at);

        // 2. Dika (DailyCarry Goods - Pending Shop)
        $dika = User::where('email', 'dika@gmail.com')->firstOrFail();
        $this->assertSame(UserRole::BUYER, $dika->role);
        $this->assertTrue($dika->isBuyer());
        $this->assertTrue($dika->hasShop());
        $this->assertTrue($dika->isSeller(), 'Buyer with registered shop is a seller by shop ownership.');
        $this->assertFalse($dika->hasApprovedShop(), 'Pending shop is not approved.');
        $this->assertFalse($dika->canAccessSellerCenter(), 'Buyer with pending shop cannot access Seller Center.');
        $this->assertTrue(Hash::check('password123', $dika->password));
        $this->assertNotNull($dika->shop);
        $this->assertSame('dailycarry-goods', $dika->shop->slug);
        $this->assertSame(ShopStatus::PENDING, $dika->shop->status);
        $this->assertNull($dika->shop->approved_at);
    }

    public function test_buyer_with_approved_shop_can_use_same_account_for_shopping_and_seller_center(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        $budi = User::where('email', 'budi@gmail.com')->firstOrFail();

        // 1. Can access Buyer Dashboard (Shopping activity)
        $this->actingAs($budi)
            ->get(route('dashboard'))
            ->assertStatus(200);

        // 2. Can access Seller Center (Managing shop)
        $this->actingAs($budi)
            ->get(route('seller.dashboard'))
            ->assertStatus(200);

        // 3. Cannot access Admin Center (Forbidden)
        $this->actingAs($budi)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    public function test_buyer_with_pending_shop_cannot_access_seller_center(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        $dika = User::where('email', 'dika@gmail.com')->firstOrFail();

        // 1. Can access Buyer Dashboard
        $this->actingAs($dika)
            ->get(route('dashboard'))
            ->assertStatus(200);

        // 2. Cannot access Seller Center because shop is pending
        $this->actingAs($dika)
            ->get(route('seller.dashboard'))
            ->assertStatus(403);
    }

    public function test_products_of_approved_shops_are_visible_in_public_catalog(): void
    {
        $this->seed(MarketplaceDemoSeeder::class);

        // Public catalog listing page
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);

        // Default page 1 includes recent items
        $response->assertSee('Threadbound Dotted Hardcover Journal 160 GSM');
        $response->assertSee('AuraGlow Desk Screenbar Light');

        // Category filter
        $catResponse = $this->get(route('products.index', ['category' => 'elektronik-komputer']));
        $catResponse->assertStatus(200);
        $catResponse->assertSee('NexaMechanical RGB Keyboard 75%');
        $catResponse->assertSee('ProGlide Wireless Gaming Mouse');

        // Detail page
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

    public function test_seeder_protects_against_running_in_production(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('MarketplaceDemoSeeder hanya diizinkan untuk lingkungan development lokal atau testing!');

        app()->detectEnvironment(fn () => 'production');

        $seeder = new MarketplaceDemoSeeder();
        $seeder->run();
    }
}
