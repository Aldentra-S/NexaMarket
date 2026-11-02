<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingCostTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_has_default_shipping_cost_of_zero(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);

        $this->assertEquals(0.00, (float) $shop->shipping_cost);
        $this->assertEquals('Gratis Ongkir', $shop->formattedShippingCost());
    }

    public function test_seller_can_update_shop_shipping_cost(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create([
            'user_id' => $seller->id,
            'name' => 'Toko Alat Tulis',
            'shipping_cost' => 0.00,
        ]);

        $response = $this->actingAs($seller)->put(route('seller.shop.settings.update'), [
            'name' => 'Toko Alat Tulis',
            'description' => 'Deskripsi toko lengkap',
            'shipping_cost' => 15000,
        ]);

        $response->assertRedirect(route('seller.shop.settings'));
        $response->assertSessionHas('success');

        $shop->refresh();
        $this->assertEquals(15000.00, (float) $shop->shipping_cost);
        $this->assertEquals('Rp 15.000', $shop->formattedShippingCost());
    }

    public function test_shipping_cost_validation_rejects_negative_value(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);

        $response = $this->actingAs($seller)->put(route('seller.shop.settings.update'), [
            'name' => $shop->name,
            'shipping_cost' => -5000,
        ]);

        $response->assertSessionHasErrors(['shipping_cost']);
    }

    public function test_regular_buyer_without_shop_cannot_access_or_update_shop_settings(): void
    {
        $buyer = User::factory()->buyer()->create();

        $this->actingAs($buyer)->get(route('seller.shop.settings'))
            ->assertForbidden();

        $this->actingAs($buyer)->put(route('seller.shop.settings.update'), [
            'name' => 'Toko Baru',
            'shipping_cost' => 10000,
        ])->assertForbidden();
    }
}
