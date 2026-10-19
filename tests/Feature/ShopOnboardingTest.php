<?php

namespace Tests\Feature;

use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ShopOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_view_shop_application_form(): void
    {
        $buyer = User::factory()->create();

        $response = $this->actingAs($buyer)->get(route('buyer.shop.apply'));

        $response->assertOk();
        $response->assertSee('Formulir Pendaftaran Toko Baru');
        $response->assertSee('Nama Toko Resmi');
    }

    public function test_buyer_can_submit_shop_application_and_defaults_to_pending(): void
    {
        $buyer = User::factory()->create();

        $payload = [
            'name' => 'Toko Gadget Nusantara',
            'description' => 'Menyediakan berbagai aksesoris dan perangkat smartphone terlengkap.',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 45, Jakarta Pusat',
        ];

        $response = $this->actingAs($buyer)->post(route('buyer.shop.store'), $payload);

        $response->assertRedirect(route('buyer.shop.apply'));
        $response->assertSessionHas('status');

        $shop = Shop::where('user_id', $buyer->id)->first();

        $this->assertNotNull($shop);
        $this->assertSame('Toko Gadget Nusantara', $shop->name);
        $this->assertSame('toko-gadget-nusantara', $shop->slug);
        $this->assertSame(ShopStatus::PENDING, $shop->status);
        $this->assertNull($shop->approved_at);
        $this->assertNull($shop->rejection_reason);
        $this->assertTrue($shop->isPending());
        $this->assertFalse($shop->isApproved());
    }

    public function test_buyer_cannot_elevate_status_via_mass_assignment_in_application(): void
    {
        $buyer = User::factory()->create();

        $payload = [
            'name' => 'Toko Curang Exploit',
            'description' => 'Mencoba injeksi status approved secara ilegal.',
            'status' => 'approved',
            'rejection_reason' => 'Bypass attempt',
        ];

        $this->actingAs($buyer)->post(route('buyer.shop.store'), $payload);

        $shop = Shop::where('user_id', $buyer->id)->first();

        $this->assertNotNull($shop);
        $this->assertSame(ShopStatus::PENDING, $shop->status);
        $this->assertNull($shop->approved_at);
        $this->assertNull($shop->rejection_reason);
    }

    public function test_buyer_cannot_apply_again_when_shop_is_already_pending(): void
    {
        $buyer = User::factory()->create();
        Shop::factory()->create([
            'user_id' => $buyer->id,
            'name' => 'Toko Pertama',
            'status' => ShopStatus::PENDING,
        ]);

        $payload = [
            'name' => 'Toko Kedua Ilegal',
            'description' => 'Mencoba mendaftar toko ganda saat toko pertama masih pending.',
        ];

        // ApplyShopRequest authorization check will fail (403 Forbidden)
        $response = $this->actingAs($buyer)->post(route('buyer.shop.store'), $payload);

        $response->assertForbidden();
        $this->assertSame(1, Shop::where('user_id', $buyer->id)->count());
    }

    public function test_buyer_with_pending_shop_views_status_card_instead_of_blank_form(): void
    {
        $buyer = User::factory()->create();
        $shop = Shop::factory()->create([
            'user_id' => $buyer->id,
            'name' => 'Toko Menunggu Antrian',
            'status' => ShopStatus::PENDING,
        ]);

        $response = $this->actingAs($buyer)->get(route('buyer.shop.apply'));

        $response->assertOk();
        $response->assertSee('Pengajuan Toko Sedang Ditinjau');
        $response->assertSee('Toko Menunggu Antrian');
    }

    public function test_buyer_is_forbidden_from_admin_shop_approval_endpoints(): void
    {
        $buyer = User::factory()->create();
        $shop = Shop::factory()->create();

        // Index
        $this->actingAs($buyer)->get(route('admin.shops.index'))->assertForbidden();

        // Show
        $this->actingAs($buyer)->get(route('admin.shops.show', $shop))->assertForbidden();

        // Approve action
        $this->actingAs($buyer)->post(route('admin.shops.approve', $shop))->assertForbidden();

        // Reject action
        $this->actingAs($buyer)->post(route('admin.shops.reject', $shop), [
            'rejection_reason' => 'Alasan ilegal',
        ])->assertForbidden();
    }

    public function test_admin_can_view_shop_applications_list_and_details(): void
    {
        $admin = User::factory()->admin()->create();
        $pendingShop = Shop::factory()->create(['status' => ShopStatus::PENDING]);
        $approvedShop = Shop::factory()->approved()->create();

        // Index
        $response = $this->actingAs($admin)->get(route('admin.shops.index'));
        $response->assertOk();
        $response->assertSee($pendingShop->name);
        $response->assertSee($approvedShop->name);

        // Show
        $showResponse = $this->actingAs($admin)->get(route('admin.shops.show', $pendingShop));
        $showResponse->assertOk();
        $showResponse->assertSee($pendingShop->name);
        $showResponse->assertSee($pendingShop->description);
    }

    public function test_admin_can_approve_shop_application_and_elevates_owner_role(): void
    {
        $admin = User::factory()->admin()->create();
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);
        $shop = Shop::factory()->create([
            'user_id' => $buyer->id,
            'status' => ShopStatus::PENDING,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.shops.approve', $shop));

        $response->assertRedirect(route('admin.shops.index'));
        $response->assertSessionHas('status');

        $shop->refresh();
        $buyer->refresh();

        $this->assertSame(ShopStatus::APPROVED, $shop->status);
        $this->assertNotNull($shop->approved_at);
        $this->assertTrue($shop->isApproved());

        // User role elevated to SELLER
        $this->assertSame(UserRole::SELLER, $buyer->role);
        $this->assertTrue($buyer->isSeller());
    }

    public function test_admin_can_reject_shop_with_required_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);
        $shop = Shop::factory()->create([
            'user_id' => $buyer->id,
            'status' => ShopStatus::PENDING,
        ]);

        // Validation error if reason is empty
        $invalidResponse = $this->actingAs($admin)->post(route('admin.shops.reject', $shop), [
            'rejection_reason' => '',
        ]);
        $invalidResponse->assertSessionHasErrors('rejection_reason');

        // Valid rejection
        $response = $this->actingAs($admin)->post(route('admin.shops.reject', $shop), [
            'rejection_reason' => 'Identitas alamat usaha tidak valid dan tidak dapat diverifikasi.',
        ]);

        $response->assertRedirect(route('admin.shops.index'));
        $response->assertSessionHas('status');

        $shop->refresh();
        $buyer->refresh();

        $this->assertSame(ShopStatus::REJECTED, $shop->status);
        $this->assertSame('Identitas alamat usaha tidak valid dan tidak dapat diverifikasi.', $shop->rejection_reason);
        $this->assertNull($shop->approved_at);
        $this->assertTrue($shop->isRejected());

        // User role remains/reverts to BUYER
        $this->assertSame(UserRole::BUYER, $buyer->role);
    }

    public function test_buyer_with_rejected_shop_can_reapply_with_updated_data(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);
        $shop = Shop::factory()->rejected('Deskripsi kurang jelas.')->create([
            'user_id' => $buyer->id,
            'name' => 'Toko Ditolak Dulu',
        ]);

        // Form allows re-application
        $response = $this->actingAs($buyer)->get(route('buyer.shop.apply'));
        $response->assertOk();
        $response->assertSee('Deskripsi kurang jelas.');
        $response->assertSee('Kirim Ulang Pengajuan');

        // Resubmit
        $resubmitResponse = $this->actingAs($buyer)->post(route('buyer.shop.store'), [
            'name' => 'Toko Ditolak Dulu Revisi',
            'description' => 'Deskripsi baru yang sudah diperbaiki secara lengkap dan jelas.',
        ]);

        $resubmitResponse->assertRedirect(route('buyer.shop.apply'));

        $shop->refresh();
        $this->assertSame('Toko Ditolak Dulu Revisi', $shop->name);
        $this->assertSame(ShopStatus::PENDING, $shop->status);
        $this->assertNull($shop->rejection_reason);
    }

    public function test_seller_dashboard_is_forbidden_for_user_without_approved_shop(): void
    {
        // 1. Regular Buyer
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);
        $this->actingAs($buyer)->get(route('seller.dashboard'))->assertForbidden();

        // 2. User with role seller BUT shop is still pending (role alone is not enough!)
        $prematureSeller = User::factory()->create(['role' => UserRole::SELLER]);
        Shop::factory()->create([
            'user_id' => $prematureSeller->id,
            'status' => ShopStatus::PENDING,
        ]);
        $this->actingAs($prematureSeller)->get(route('seller.dashboard'))->assertForbidden();

        // 3. User with role seller BUT shop is rejected
        $rejectedSeller = User::factory()->create(['role' => UserRole::SELLER]);
        Shop::factory()->rejected()->create([
            'user_id' => $rejectedSeller->id,
        ]);
        $this->actingAs($rejectedSeller)->get(route('seller.dashboard'))->assertForbidden();
    }

    public function test_seller_with_approved_shop_can_access_seller_dashboard(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create([
            'user_id' => $seller->id,
            'name' => 'Toko Elektronik Makmur',
        ]);

        $response = $this->actingAs($seller)->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSee('Seller Center');
        $response->assertSee('Toko Elektronik Makmur');
        $response->assertSee('Toko Terverifikasi (Approved)');
    }

    public function test_shop_policy_prevents_unauthorized_user_from_updating_another_shop(): void
    {
        $sellerA = User::factory()->seller()->create();
        $shopA = Shop::factory()->approved()->create(['user_id' => $sellerA->id]);

        $sellerB = User::factory()->seller()->create();
        $shopB = Shop::factory()->approved()->create(['user_id' => $sellerB->id]);

        // Seller A can update their own shop
        $this->assertTrue(Gate::forUser($sellerA)->allows('update', $shopA));

        // Seller A cannot update Seller B's shop
        $this->assertFalse(Gate::forUser($sellerA)->allows('update', $shopB));

        // Seller B cannot update Seller A's shop
        $this->assertFalse(Gate::forUser($sellerB)->allows('update', $shopA));
    }

    public function test_admin_can_suspend_approved_shop_with_valid_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create([
            'user_id' => $seller->id,
            'name' => 'Toko Melanggar Kebijakan',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.shops.suspend', $shop), [
            'suspension_reason' => 'Toko terindikasi menjual barang tiruan tanpa sertifikasi resmi.',
        ]);

        $response->assertRedirect(route('admin.shops.index'));
        $response->assertSessionHas('status');

        $shop->refresh();
        $seller->refresh();

        $this->assertSame(ShopStatus::SUSPENDED, $shop->status);
        $this->assertSame('Toko terindikasi menjual barang tiruan tanpa sertifikasi resmi.', $shop->suspension_reason);
        $this->assertNotNull($shop->suspended_at);
        $this->assertTrue($shop->isSuspended());
        $this->assertFalse($shop->isApproved());

        // Role reverted to BUYER during suspension
        $this->assertSame(UserRole::BUYER, $seller->role);
    }

    public function test_admin_cannot_suspend_shop_without_valid_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);

        // Empty reason
        $responseEmpty = $this->actingAs($admin)->post(route('admin.shops.suspend', $shop), [
            'suspension_reason' => '',
        ]);
        $responseEmpty->assertSessionHasErrors('suspension_reason');

        // Reason too short (< 5 chars)
        $responseShort = $this->actingAs($admin)->post(route('admin.shops.suspend', $shop), [
            'suspension_reason' => 'abc',
        ]);
        $responseShort->assertSessionHasErrors('suspension_reason');

        $shop->refresh();
        $this->assertSame(ShopStatus::APPROVED, $shop->status);
    }

    public function test_non_admin_cannot_suspend_or_reactivate_shop(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        $suspendedShop = Shop::factory()->suspended()->create();

        // Buyer attempts
        $this->actingAs($buyer)->post(route('admin.shops.suspend', $shop), [
            'suspension_reason' => 'Alasan ilegal oleh pembeli.',
        ])->assertForbidden();

        $this->actingAs($buyer)->post(route('admin.shops.reactivate', $suspendedShop))->assertForbidden();

        // Seller attempts
        $this->actingAs($seller)->post(route('admin.shops.suspend', $shop), [
            'suspension_reason' => 'Alasan ilegal oleh penjual.',
        ])->assertForbidden();

        $this->actingAs($seller)->post(route('admin.shops.reactivate', $suspendedShop))->assertForbidden();
    }

    public function test_suspended_shop_cannot_access_seller_dashboard(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->suspended('Pelanggaran ketentuan operasional.')->create([
            'user_id' => $seller->id,
        ]);

        $response = $this->actingAs($seller)->get(route('seller.dashboard'));
        $response->assertForbidden();
    }

    public function test_admin_can_reactivate_suspended_shop(): void
    {
        $admin = User::factory()->admin()->create();
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);
        $shop = Shop::factory()->suspended('Pelanggaran sementara.')->create([
            'user_id' => $buyer->id,
            'name' => 'Toko Dipulihkan',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.shops.reactivate', $shop));

        $response->assertRedirect(route('admin.shops.index'));
        $response->assertSessionHas('status');

        $shop->refresh();
        $buyer->refresh();

        $this->assertSame(ShopStatus::APPROVED, $shop->status);
        $this->assertNull($shop->suspension_reason);
        $this->assertNull($shop->suspended_at);
        $this->assertTrue($shop->isApproved());
        $this->assertFalse($shop->isSuspended());

        // Role restored to SELLER
        $this->assertSame(UserRole::SELLER, $buyer->role);
        $this->assertTrue($buyer->isSeller());
    }

    public function test_buyer_with_suspended_shop_views_suspended_card_and_cannot_reapply(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);
        $shop = Shop::factory()->suspended('Dokumen izin usaha dinyatakan tidak sah.')->create([
            'user_id' => $buyer->id,
            'name' => 'Toko Dalam Masa Sanksi',
        ]);

        // Form renders suspended card
        $response = $this->actingAs($buyer)->get(route('buyer.shop.apply'));
        $response->assertOk();
        $response->assertSee('Toko Anda Sedang Ditangguhkan');
        $response->assertSee('Dokumen izin usaha dinyatakan tidak sah.');
        $response->assertDontSee('Ajukan Toko Sekarang');

        // Attempting to post another application fails authorization
        $postResponse = $this->actingAs($buyer)->post(route('buyer.shop.store'), [
            'name' => 'Toko Baru Mengelabui Sanksi',
            'description' => 'Mencoba mendaftar toko baru saat toko lama ditangguhkan.',
        ]);
        $postResponse->assertForbidden();
    }
}
