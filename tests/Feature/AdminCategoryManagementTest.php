<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    private function createSellerWithApprovedShop(): array
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create([
            'user_id' => $seller->id,
        ]);

        return [$seller, $shop];
    }

    public function test_admin_can_view_categories_index(): void
    {
        $admin = $this->createAdmin();
        Category::factory()->create(['name' => 'Elektronik Rumah']);

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Kategori Produk');
        $response->assertSee('Elektronik Rumah');
    }

    public function test_admin_can_create_category_successfully(): void
    {
        $admin = $this->createAdmin();

        $payload = [
            'name' => 'Pakaian & Aksesori Pria',
            'slug' => 'pakaian-aksesori-pria',
            'description' => 'Koleksi busana pria modern, kemeja, celana, dan aksesori.',
            'is_active' => true,
        ];

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), $payload);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Pakaian & Aksesori Pria',
            'slug' => 'pakaian-aksesori-pria',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_edit_and_update_category(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create([
            'name' => 'Perabotan Kamar',
            'slug' => 'perabotan-kamar',
        ]);

        $editResponse = $this->actingAs($admin)->get(route('admin.categories.edit', $category));
        $editResponse->assertOk();
        $editResponse->assertSee('Perabotan Kamar');

        $updateResponse = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Perabotan Kamar Tidur Minimalis',
            'slug' => 'perabotan-kamar-tidur-minimalis',
            'description' => 'Kasur, meja rias, dan lemari pakaian.',
            'is_active' => true,
        ]);

        $updateResponse->assertRedirect(route('admin.categories.index'));
        $updateResponse->assertSessionHas('success');

        $category->refresh();
        $this->assertSame('Perabotan Kamar Tidur Minimalis', $category->name);
        $this->assertSame('perabotan-kamar-tidur-minimalis', $category->slug);
    }

    public function test_admin_can_delete_unused_category(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create(['name' => 'Kategori Sementara']);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_buyer_and_seller_cannot_access_admin_category_management(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);
        [$seller] = $this->createSellerWithApprovedShop();
        $category = Category::factory()->create();

        // 1. Buyer blocked
        $this->actingAs($buyer)->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs($buyer)->get(route('admin.categories.create'))->assertForbidden();
        $this->actingAs($buyer)->post(route('admin.categories.store'), ['name' => 'Hack Cat'])->assertForbidden();
        $this->actingAs($buyer)->get(route('admin.categories.edit', $category))->assertForbidden();
        $this->actingAs($buyer)->put(route('admin.categories.update', $category), ['name' => 'Hack Update'])->assertForbidden();
        $this->actingAs($buyer)->delete(route('admin.categories.destroy', $category))->assertForbidden();

        // 2. Seller blocked
        $this->actingAs($seller)->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs($seller)->get(route('admin.categories.create'))->assertForbidden();
        $this->actingAs($seller)->post(route('admin.categories.store'), ['name' => 'Seller Cat'])->assertForbidden();
        $this->actingAs($seller)->delete(route('admin.categories.destroy', $category))->assertForbidden();

        // 3. Guest redirected to login
        auth()->logout();
        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
    }

    public function test_category_name_and_unique_slug_validation_are_enforced(): void
    {
        $admin = $this->createAdmin();
        Category::factory()->create(['slug' => 'elektronik-pilihan']);

        // Blank name
        $res1 = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => '',
            'slug' => 'kategori-baru',
        ]);
        $res1->assertSessionHasErrors('name');

        // Duplicate slug
        $res2 = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Elektronik Baru',
            'slug' => 'elektronik-pilihan',
        ]);
        $res2->assertSessionHasErrors('slug');
    }

    public function test_seller_can_associate_product_with_valid_category(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $category = Category::factory()->create(['name' => 'Perlengkapan Dapur']);

        $payload = [
            'category_id' => $category->id,
            'name' => 'Wajan Anti Lengket 28cm',
            'description' => 'Wajan penggorengan granite coating tebal dan mudah dibersihkan.',
            'price' => 175000,
            'stock' => 20,
            'status' => ProductStatus::PUBLISHED->value,
        ];

        $response = $this->actingAs($seller)->post(route('seller.products.store'), $payload);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Wajan Anti Lengket 28cm')->first();
        $this->assertNotNull($product);
        $this->assertSame($category->id, $product->category_id);
        $this->assertSame('Perlengkapan Dapur', $product->category->name);
    }

    public function test_invalid_category_is_rejected_on_product_creation_and_update(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $invalidPayload = [
            'category_id' => 999999, // Non-existent category
            'name' => 'Produk Kategori Palsu',
            'description' => 'Deskripsi produk testing.',
            'price' => 50000,
            'stock' => 10,
            'status' => ProductStatus::PUBLISHED->value,
        ];

        // On Store
        $storeRes = $this->actingAs($seller)->post(route('seller.products.store'), $invalidPayload);
        $storeRes->assertSessionHasErrors('category_id');

        // On Update
        $updateRes = $this->actingAs($seller)->put(route('seller.products.update', $product), $invalidPayload);
        $updateRes->assertSessionHasErrors('category_id');
    }

    public function test_deleting_category_in_use_does_not_break_product_data(): void
    {
        $admin = $this->createAdmin();
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $category = Category::factory()->create(['name' => 'Buku & Alat Tulis']);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Buku Catatan Hardcover A5',
        ]);

        // Attempt deletion through admin controller should be safely blocked with validation error
        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertSessionHasErrors('category');

        // Verify product remains intact with its category
        $product->refresh();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Buku Catatan Hardcover A5',
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
        $this->assertSame($category->id, $product->category_id);

        // Even if category is deleted directly at model/DB level, products must NOT be deleted (nullOnDelete)
        $category->delete();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $product->refresh();
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertNull($product->category_id);
    }

    public function test_category_filter_only_displays_products_meeting_catalog_visibility_rules(): void
    {
        [$approvedSeller, $approvedShop] = $this->createSellerWithApprovedShop();
        $category = Category::factory()->create(['name' => 'Audio & Headphone', 'slug' => 'audio-headphone']);
        $otherCategory = Category::factory()->create(['name' => 'Pakaian', 'slug' => 'pakaian']);

        // 1. Published product in target category from approved shop -> SHOULD APPEAR
        $visibleProduct = Product::factory()->published()->create([
            'shop_id' => $approvedShop->id,
            'category_id' => $category->id,
            'name' => 'Headphone Bluetooth Bass Pro',
        ]);

        // 2. Draft product in target category -> SHOULD NOT APPEAR
        $draftProduct = Product::factory()->draft()->create([
            'shop_id' => $approvedShop->id,
            'category_id' => $category->id,
            'name' => 'Headphone Draft Eksperimen',
        ]);

        // 3. Product in DIFFERENT category -> SHOULD NOT APPEAR
        $otherCatProduct = Product::factory()->published()->create([
            'shop_id' => $approvedShop->id,
            'category_id' => $otherCategory->id,
            'name' => 'Kemeja Katun Pria',
        ]);

        // 4. Product from suspended shop in target category -> SHOULD NOT APPEAR
        $suspendedSeller = User::factory()->seller()->create();
        $suspendedShop = Shop::factory()->suspended('Pelanggaran toko.')->create([
            'user_id' => $suspendedSeller->id,
        ]);
        $suspendedShopProduct = Product::factory()->published()->create([
            'shop_id' => $suspendedShop->id,
            'category_id' => $category->id,
            'name' => 'Headphone Toko Suspended',
        ]);

        // 5. Product from pending shop in target category -> SHOULD NOT APPEAR
        $pendingSeller = User::factory()->seller()->create();
        $pendingShop = Shop::factory()->create([
            'user_id' => $pendingSeller->id,
            'status' => ShopStatus::PENDING,
        ]);
        $pendingShopProduct = Product::factory()->published()->create([
            'shop_id' => $pendingShop->id,
            'category_id' => $category->id,
            'name' => 'Headphone Toko Pending',
        ]);

        // Execute category filter
        $response = $this->get(route('products.index', ['category' => 'audio-headphone']));

        $response->assertOk();
        $response->assertSee('Headphone Bluetooth Bass Pro');
        $response->assertDontSee('Headphone Draft Eksperimen');
        $response->assertDontSee('Kemeja Katun Pria');
        $response->assertDontSee('Headphone Toko Suspended');
        $response->assertDontSee('Headphone Toko Pending');
    }
}
