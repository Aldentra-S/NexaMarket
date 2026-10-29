<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect('/login');
    }

    public function test_admin_visiting_dashboard_is_redirected_to_admin_center(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_buyer_can_view_redesigned_shopping_dashboard(): void
    {
        $buyer = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => UserRole::BUYER,
        ]);

        $response = $this->actingAs($buyer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Selamat datang kembali,');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Mulai Belanja');
        $response->assertSee('Temukan produk yang kamu butuhkan');
        $response->assertSee('Jelajahi Katalog');
        $response->assertSee('Pesanan Saya');
        $response->assertSee('Menunggu Pembayaran');
        $response->assertSee('Dalam Pengiriman');
        $response->assertSee('Pesanan Selesai');
        $response->assertSee('Aktivitas Pesanan');
        $response->assertSee('Akses Cepat');
        $response->assertSee('Katalog Produk');
        $response->assertSee('Keranjang Belanja');
        $response->assertSee('Profil Saya');

        // Confirm obsolete role-info cards and open shop promo are removed
        $response->assertDontSee('Area Pembeli');
        $response->assertDontSee('Admin Control');
        $response->assertDontSee('Ingin berjualan di NexaMarket?');
        $response->assertDontSee('Pelajari cara membuka toko');
    }

    public function test_buyer_dashboard_calculates_real_database_order_counts(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);
        $otherBuyer = User::factory()->create(['role' => UserRole::BUYER]);

        // Pending order for buyer
        Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-PENDING-001',
            'status' => OrderStatus::PENDING,
            'total_amount' => 150000,
        ]);

        // Paid order (in process/shipping) for buyer
        $paidOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-PAID-002',
            'status' => OrderStatus::PAID,
            'total_amount' => 300000,
        ]);

        ShopOrder::create([
            'order_id' => $paidOrder->id,
            'shop_name' => 'Mitra Elektronik',
            'order_number' => 'SO-002-1',
            'status' => OrderStatus::PROCESSING,
            'subtotal' => 300000,
        ]);

        // Completed order for buyer
        Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-DONE-003',
            'status' => OrderStatus::COMPLETED,
            'total_amount' => 75000,
        ]);

        // Order belonging to other buyer (must not be counted)
        Order::create([
            'user_id' => $otherBuyer->id,
            'order_number' => 'ORD-OTHER-004',
            'status' => OrderStatus::PENDING,
            'total_amount' => 500000,
        ]);

        $response = $this->actingAs($buyer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalOrdersCount', 3);
        $response->assertViewHas('pendingOrdersCount', 1);
        $response->assertViewHas('shippingOrdersCount', 1);
        $response->assertViewHas('completedOrdersCount', 1);
    }

    public function test_order_index_filters_properly_by_status(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);

        Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-PEND-TEST',
            'status' => OrderStatus::PENDING,
            'total_amount' => 100000,
        ]);

        Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-DONE-TEST',
            'status' => OrderStatus::COMPLETED,
            'total_amount' => 200000,
        ]);

        // Filter pending
        $pendingResp = $this->actingAs($buyer)->get(route('orders.index', ['status' => 'pending']));
        $pendingResp->assertOk();
        $pendingResp->assertSee('ORD-PEND-TEST');
        $pendingResp->assertDontSee('ORD-DONE-TEST');

        // Filter completed
        $completedResp = $this->actingAs($buyer)->get(route('orders.index', ['status' => 'completed']));
        $completedResp->assertOk();
        $completedResp->assertSee('ORD-DONE-TEST');
        $completedResp->assertDontSee('ORD-PEND-TEST');
    }

    public function test_profile_displays_onboarding_option_when_buyer_has_no_shop(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);

        $response = $this->actingAs($buyer)->get(route('profile'));

        $response->assertOk();
        $response->assertSee('Buka Toko di NexaMarket');
        $response->assertSee('Daftar Menjadi Penjual');
        $response->assertSee(route('buyer.shop.apply'));
    }

    public function test_profile_displays_pending_status_when_shop_is_pending(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);

        $shop = new Shop([
            'name' => 'Toko Menunggu Verifikasi',
            'slug' => 'toko-menunggu-verifikasi',
            'description' => 'Deskripsi toko uji coba',
        ]);
        $shop->user_id = $buyer->id;
        $shop->status = ShopStatus::PENDING;
        $shop->save();

        $response = $this->actingAs($buyer)->get(route('profile'));

        $response->assertOk();
        $response->assertSee('Toko Menunggu Verifikasi');
        $response->assertSee('Menunggu Persetujuan');
        $response->assertSee('Lihat Detail Pengajuan');
    }

    public function test_profile_displays_rejected_status_when_shop_is_rejected(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);

        $shop = new Shop([
            'name' => 'Toko Butuh Revisi',
            'slug' => 'toko-butuh-revisi',
            'description' => 'Deskripsi toko uji coba',
        ]);
        $shop->user_id = $buyer->id;
        $shop->status = ShopStatus::REJECTED;
        $shop->rejection_reason = 'KTP dan dokumen usaha belum jelas.';
        $shop->save();

        $response = $this->actingAs($buyer)->get(route('profile'));

        $response->assertOk();
        $response->assertSee('Toko Butuh Revisi');
        $response->assertSee('Perlu Perbaikan / Ditolak');
        $response->assertSee('KTP dan dokumen usaha belum jelas.');
        $response->assertSee('Perbaiki');
        $response->assertSee('Ajukan Ulang');
    }

    public function test_profile_displays_active_seller_center_when_user_is_seller(): void
    {
        $seller = User::factory()->create(['role' => UserRole::BUYER]);

        $shop = new Shop([
            'name' => 'Toko Resmi Juara',
            'slug' => 'toko-resmi-juara',
            'description' => 'Toko resmi aktif',
        ]);
        $shop->user_id = $seller->id;
        $shop->status = ShopStatus::APPROVED;
        $shop->save();

        $response = $this->actingAs($seller)->get(route('profile'));

        $response->assertOk();
        $response->assertSee('Toko Resmi Juara');
        $response->assertSee('Toko Aktif');
        $response->assertSee('Disetujui');
        $response->assertSee('Buka Seller Center');
        $response->assertSee(route('seller.dashboard'));
        $response->assertDontSee('Daftar Menjadi Penjual');
    }

    public function test_buyer_dashboard_displays_zero_pending_orders_without_fallback_twelve_or_fake_label(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);

        $response = $this->actingAs($buyer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Menunggu Pembayaran');
        $response->assertDontSee('Produk Favorit');
        $response->assertDontSee('Produk tersimpan');
        $response->assertViewHas('pendingOrdersCount', 0);
    }

    public function test_buyer_dashboard_featured_products_do_not_contain_fake_ratings_or_arbitrary_loop_badges(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::BUYER]);

        $seller = User::factory()->create(['role' => UserRole::BUYER]);
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        $category = \App\Models\Category::create([
            'name' => 'Kategori Pilihan',
            'slug' => 'kategori-pilihan',
            'is_active' => true,
        ]);

        \App\Models\Product::factory()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Laptop Ultra Spesial',
            'slug' => 'laptop-ultra-spesial',
            'price' => 15000000,
            'status' => \App\Enums\ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($buyer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Laptop Ultra Spesial');
        $response->assertSee('Kategori Pilihan');
        $response->assertDontSee('Official Store');
        $response->assertDontSee('Best Seller');
        $response->assertDontSee('Diskon');
        $response->assertDontSee('Trending');
    }
}
