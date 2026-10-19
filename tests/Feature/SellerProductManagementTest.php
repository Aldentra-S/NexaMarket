<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerProductManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create a seller user with an approved shop.
     */
    private function createSellerWithApprovedShop(): array
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create([
            'user_id' => $seller->id,
        ]);

        return [$seller, $shop];
    }

    public function test_approved_seller_can_view_seller_products_index(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Toko Sendiri',
        ]);

        $response = $this->actingAs($seller)->get(route('seller.products.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Produk Toko');
        $response->assertSee('Produk Toko Sendiri');
    }

    public function test_approved_seller_can_view_create_product_form(): void
    {
        [$seller] = $this->createSellerWithApprovedShop();

        $response = $this->actingAs($seller)->get(route('seller.products.create'));

        $response->assertOk();
        $response->assertSee('Tambah Produk Baru');
        $response->assertSee('Nama Produk');
        $response->assertSee('Harga Jual Satuan (IDR / Rp)');
    }

    public function test_approved_seller_can_create_product_successfully_with_auto_slug(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();

        $payload = [
            'name' => 'Kopi Arabika Gayo Organik 250g',
            'description' => 'Biji kopi pilihan roasted medium dark dengan aroma floral dan rasa fruity khas dataran tinggi Gayo.',
            'price' => 85000,
            'stock' => 50,
            'status' => ProductStatus::PUBLISHED->value,
        ];

        $response = $this->actingAs($seller)->post(route('seller.products.store'), $payload);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'shop_id' => $shop->id,
            'name' => 'Kopi Arabika Gayo Organik 250g',
            'price' => '85000.00',
            'stock' => 50,
            'status' => ProductStatus::PUBLISHED->value,
        ]);

        $product = Product::where('name', 'Kopi Arabika Gayo Organik 250g')->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('kopi-arabika-gayo-organik-250g', $product->slug);
    }

    public function test_product_creation_validation_rules_are_enforced(): void
    {
        [$seller] = $this->createSellerWithApprovedShop();

        $response = $this->actingAs($seller)->post(route('seller.products.store'), [
            'name' => '',
            'description' => '',
            'price' => -500,
            'stock' => -1,
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['name', 'description', 'price', 'stock', 'status']);
        $this->assertSame(0, Product::count());
    }

    public function test_product_creation_prevents_shop_id_spoofing(): void
    {
        [$sellerA, $shopA] = $this->createSellerWithApprovedShop();
        [, $shopB] = $this->createSellerWithApprovedShop();

        $payload = [
            'shop_id' => $shopB->id, // Malicious attempt to assign to Shop B
            'name' => 'Produk Penetrasi Shop ID',
            'description' => 'Deskripsi produk testing keamanan multi-vendor.',
            'price' => 50000,
            'stock' => 20,
            'status' => ProductStatus::PUBLISHED->value,
        ];

        $response = $this->actingAs($sellerA)->post(route('seller.products.store'), $payload);

        $response->assertRedirect(route('seller.products.index'));

        // Verify product was assigned to shop A (caller's shop), NOT shop B
        $this->assertDatabaseHas('products', [
            'shop_id' => $shopA->id,
            'name' => 'Produk Penetrasi Shop ID',
        ]);
        $this->assertDatabaseMissing('products', [
            'shop_id' => $shopB->id,
            'name' => 'Produk Penetrasi Shop ID',
        ]);
    }

    public function test_approved_seller_can_view_edit_and_update_own_product(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Lama',
            'price' => 25000,
            'stock' => 10,
            'status' => ProductStatus::DRAFT,
        ]);

        // Edit view
        $editResponse = $this->actingAs($seller)->get(route('seller.products.edit', $product));
        $editResponse->assertOk();
        $editResponse->assertSee('Edit Produk');
        $editResponse->assertSee('Produk Lama');

        // Update action
        $updateResponse = $this->actingAs($seller)->put(route('seller.products.update', $product), [
            'name' => 'Produk Diperbarui',
            'description' => 'Deskripsi yang telah disempurnakan dan diperbarui.',
            'price' => 35000,
            'stock' => 25,
            'status' => ProductStatus::PUBLISHED->value,
        ]);

        $updateResponse->assertRedirect(route('seller.products.index'));
        $updateResponse->assertSessionHas('success');

        $product->refresh();
        $this->assertSame('Produk Diperbarui', $product->name);
        $this->assertSame('35000.00', (string) $product->price);
        $this->assertSame(25, $product->stock);
        $this->assertSame(ProductStatus::PUBLISHED, $product->status);
    }

    public function test_seller_cannot_view_or_manage_product_of_another_shop(): void
    {
        [$sellerA, $shopA] = $this->createSellerWithApprovedShop();
        [$sellerB, $shopB] = $this->createSellerWithApprovedShop();

        $productB = Product::factory()->create([
            'shop_id' => $shopB->id,
            'name' => 'Produk Rahasia Seller B',
            'price' => 99000,
        ]);

        // Seller A tries to view Seller B's product in seller area
        $showResponse = $this->actingAs($sellerA)->get(route('seller.products.show', $productB));
        $showResponse->assertForbidden();

        // Seller A tries to view Seller B's product edit page
        $editResponse = $this->actingAs($sellerA)->get(route('seller.products.edit', $productB));
        $editResponse->assertForbidden();

        // Seller A tries to update Seller B's product
        $updateResponse = $this->actingAs($sellerA)->put(route('seller.products.update', $productB), [
            'name' => 'Produk Dibajak',
            'description' => 'Mencoba membajak produk toko lain.',
            'price' => 1000,
            'stock' => 999,
            'status' => ProductStatus::PUBLISHED->value,
        ]);
        $updateResponse->assertForbidden();

        // Seller A tries to delete Seller B's product
        $deleteResponse = $this->actingAs($sellerA)->delete(route('seller.products.destroy', $productB));
        $deleteResponse->assertForbidden();

        // Confirm product B remains unaltered in database
        $productB->refresh();
        $this->assertSame('Produk Rahasia Seller B', $productB->name);
        $this->assertSame('99000.00', (string) $productB->price);
    }

    public function test_approved_seller_can_delete_own_product(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Akan Dihapus',
        ]);

        $response = $this->actingAs($seller)->delete(route('seller.products.destroy', $product));

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_guest_cannot_access_seller_product_management(): void
    {
        $response = $this->get(route('seller.products.index'));
        $response->assertRedirect(route('login'));

        $postResponse = $this->post(route('seller.products.store'), [
            'name' => 'Produk Guest',
        ]);
        $postResponse->assertRedirect(route('login'));
    }

    public function test_buyer_without_seller_role_cannot_access_seller_products(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);

        $response = $this->actingAs($buyer)->get(route('seller.products.index'));
        $response->assertForbidden();

        $postResponse = $this->actingAs($buyer)->post(route('seller.products.store'), [
            'name' => 'Produk Buyer',
        ]);
        $postResponse->assertForbidden();
    }

    public function test_seller_with_pending_shop_cannot_manage_products(): void
    {
        $seller = User::factory()->seller()->create();
        Shop::factory()->create([
            'user_id' => $seller->id,
            'status' => ShopStatus::PENDING,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.products.index'));
        $response->assertForbidden();

        $postResponse = $this->actingAs($seller)->post(route('seller.products.store'), [
            'name' => 'Produk Toko Pending',
            'description' => 'Deskripsi produk toko pending.',
            'price' => 10000,
            'stock' => 10,
            'status' => ProductStatus::PUBLISHED->value,
        ]);
        $postResponse->assertForbidden();
    }

    public function test_seller_with_rejected_shop_cannot_manage_products(): void
    {
        $seller = User::factory()->seller()->create();
        Shop::factory()->rejected()->create([
            'user_id' => $seller->id,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.products.index'));
        $response->assertForbidden();
    }

    public function test_seller_with_suspended_shop_cannot_manage_products(): void
    {
        $seller = User::factory()->seller()->create();
        Shop::factory()->suspended('Pelanggaran berulang.')->create([
            'user_id' => $seller->id,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.products.index'));
        $response->assertForbidden();

        $createResponse = $this->actingAs($seller)->get(route('seller.products.create'));
        $createResponse->assertForbidden();
    }

    public function test_shop_deletion_cascades_and_deletes_its_products(): void
    {
        [, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $shop->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
