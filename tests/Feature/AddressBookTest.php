<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_address_book(): void
    {
        $response = $this->get(route('buyer.addresses.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_buyer_can_view_their_empty_address_list(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->get(route('buyer.addresses.index'));

        $response->assertOk();
        $response->assertSee('Buku Alamat Pengiriman');
        $response->assertSee('Belum Ada Alamat Pengiriman');
    }

    public function test_buyer_can_view_create_address_page(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->get(route('buyer.addresses.create'));

        $response->assertOk();
        $response->assertSee('Tambah Alamat Pengiriman');
    }

    public function test_first_created_address_automatically_becomes_default(): void
    {
        $buyer = User::factory()->buyer()->create();

        $payload = [
            'label' => 'Rumah',
            'recipient_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru',
            'village' => 'Senayan',
            'postal_code' => '12190',
            'address_line' => 'Jl. Jend. Sudirman No. 10',
            'delivery_notes' => 'Pagar hitam samping pos',
            'is_default' => 0,
        ];

        $response = $this->actingAs($buyer)->post(route('buyer.addresses.store'), $payload);

        $response->assertRedirect(route('buyer.addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'user_id' => $buyer->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi Santoso',
            'is_default' => true,
        ]);
    }

    public function test_subsequent_address_with_is_default_unsets_previous_default(): void
    {
        $buyer = User::factory()->buyer()->create();
        $firstAddress = Address::factory()->create([
            'user_id' => $buyer->id,
            'is_default' => true,
        ]);

        $secondPayload = [
            'label' => 'Kantor',
            'recipient_name' => 'Budi Santoso (Kantor)',
            'phone' => '081298765432',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'district' => 'Gambir',
            'village' => 'Gambir',
            'postal_code' => '10110',
            'address_line' => 'Jl. Medan Merdeka Barat No. 5',
            'is_default' => 1,
        ];

        $response = $this->actingAs($buyer)->post(route('buyer.addresses.store'), $secondPayload);

        $response->assertRedirect(route('buyer.addresses.index'));

        $this->assertFalse($firstAddress->fresh()->is_default);
        $this->assertDatabaseHas('addresses', [
            'user_id' => $buyer->id,
            'label' => 'Kantor',
            'is_default' => true,
        ]);
    }

    public function test_buyer_can_update_their_address(): void
    {
        $buyer = User::factory()->buyer()->create();
        $address = Address::factory()->create([
            'user_id' => $buyer->id,
            'recipient_name' => 'Nama Lama',
        ]);

        $updatePayload = [
            'label' => 'Rumah Baru',
            'recipient_name' => 'Nama Baru',
            'phone' => '081299998888',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40132',
            'address_line' => 'Jl. Dago Asri No. 12',
            'is_default' => 1,
        ];

        $response = $this->actingAs($buyer)->put(route('buyer.addresses.update', $address), $updatePayload);

        $response->assertRedirect(route('buyer.addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'recipient_name' => 'Nama Baru',
            'city' => 'Bandung',
        ]);
    }

    public function test_buyer_can_set_address_as_default_via_toggle(): void
    {
        $buyer = User::factory()->buyer()->create();
        $address1 = Address::factory()->create(['user_id' => $buyer->id, 'is_default' => true]);
        $address2 = Address::factory()->create(['user_id' => $buyer->id, 'is_default' => false]);

        $response = $this->actingAs($buyer)->patch(route('buyer.addresses.default', $address2));

        $response->assertRedirect(route('buyer.addresses.index'));
        $this->assertFalse($address1->fresh()->is_default);
        $this->assertTrue($address2->fresh()->is_default);
    }

    public function test_buyer_can_delete_address(): void
    {
        $buyer = User::factory()->buyer()->create();
        $address = Address::factory()->create(['user_id' => $buyer->id, 'is_default' => false]);

        $response = $this->actingAs($buyer)->delete(route('buyer.addresses.destroy', $address));

        $response->assertRedirect(route('buyer.addresses.index'));
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_buyer_cannot_view_edit_update_or_delete_other_users_address(): void
    {
        $buyerA = User::factory()->buyer()->create();
        $buyerB = User::factory()->buyer()->create();

        $addressB = Address::factory()->create(['user_id' => $buyerB->id]);

        // Edit
        $this->actingAs($buyerA)->get(route('buyer.addresses.edit', $addressB))
            ->assertForbidden();

        // Update
        $this->actingAs($buyerA)->put(route('buyer.addresses.update', $addressB), [
            'label' => 'Hack',
            'recipient_name' => 'Hacker',
            'phone' => '081234567890',
            'province' => 'DKI',
            'city' => 'Jakarta',
            'district' => 'Gambir',
            'postal_code' => '10110',
            'address_line' => 'Jl. Merdeka',
        ])->assertForbidden();

        // Set Default
        $this->actingAs($buyerA)->patch(route('buyer.addresses.default', $addressB))
            ->assertForbidden();

        // Destroy
        $this->actingAs($buyerA)->delete(route('buyer.addresses.destroy', $addressB))
            ->assertForbidden();
    }

    public function test_store_address_validates_required_fields(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->post(route('buyer.addresses.store'), []);

        $response->assertSessionHasErrors([
            'label',
            'recipient_name',
            'phone',
            'province',
            'city',
            'district',
            'postal_code',
            'address_line',
        ]);
    }

    public function test_redirect_to_checkout_works_after_creating_address(): void
    {
        $buyer = User::factory()->buyer()->create();

        $payload = [
            'label' => 'Rumah',
            'recipient_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru',
            'postal_code' => '12190',
            'address_line' => 'Jl. Jend. Sudirman No. 10',
            'redirect_to_checkout' => 1,
        ];

        $response = $this->actingAs($buyer)->post(route('buyer.addresses.store'), $payload);

        $address = Address::where('user_id', $buyer->id)->first();
        $response->assertRedirect(route('checkout.create', ['address_id' => $address->id]));
    }
}
