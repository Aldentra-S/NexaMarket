<?php

namespace Tests\Feature;

use App\Enums\ShopStatus;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerShopSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function createSellerWithApprovedShop(): array
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create([
            'user_id' => $seller->id,
            'name' => 'Toko Asli Seller',
            'slug' => 'toko-asli-seller',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
        ]);

        return [$seller, $shop];
    }

    public function test_approved_seller_can_view_shop_settings_page(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();

        $response = $this->actingAs($seller)->get(route('seller.shop.settings'));

        $response->assertOk();
        $response->assertSee('Pengaturan & Profil Toko');
        $response->assertSee('Toko Asli Seller');
        $response->assertSee('081234567890');
    }

    public function test_approved_seller_can_update_shop_settings(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();

        $response = $this->actingAs($seller)->put(route('seller.shop.settings.update'), [
            'name' => 'Toko Sukses Makmur',
            'description' => 'Menjual aneka kebutuhan harian berkualitas.',
            'phone' => '08987654321',
            'address' => 'Jl. Sudirman No. 45, Bandung',
        ]);

        $response->assertRedirect(route('seller.shop.settings'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Toko Sukses Makmur',
            'slug' => 'toko-sukses-makmur',
            'phone' => '08987654321',
            'address' => 'Jl. Sudirman No. 45, Bandung',
        ]);
    }

    public function test_shop_name_must_be_unique_except_current_shop(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();

        // Create another shop
        $otherShop = Shop::factory()->approved()->create([
            'name' => 'Toko Pesaing',
            'slug' => 'toko-pesaing',
        ]);

        // Trying to use another shop's name fails
        $response = $this->actingAs($seller)->put(route('seller.shop.settings.update'), [
            'name' => 'Toko Pesaing',
            'phone' => '081234567890',
            'address' => 'Jl. Alamat',
        ]);

        $response->assertSessionHasErrors('name');

        // Keeping the same name passes
        $responseSame = $this->actingAs($seller)->put(route('seller.shop.settings.update'), [
            'name' => 'Toko Asli Seller',
            'description' => 'Deskripsi baru',
            'phone' => '081234567890',
            'address' => 'Jl. Alamat Baru',
        ]);

        $responseSame->assertSessionHasNoErrors();
    }

    public function test_buyer_without_approved_shop_cannot_access_seller_settings(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->get(route('seller.shop.settings'));
        $response->assertForbidden();
    }

    public function test_approved_seller_can_view_reviews_page(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Keren',
        ]);

        $response = $this->actingAs($seller)->get(route('seller.reviews.index'));

        $response->assertOk();
        $response->assertSee('Ulasan & Penilaian Produk');
        $response->assertSee('Produk Keren');
    }
}
