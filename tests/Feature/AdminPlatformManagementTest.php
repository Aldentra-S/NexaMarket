<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPlatformManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_view_dashboard_with_platform_kpis(): void
    {
        $admin = $this->createAdmin();

        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        $buyer = User::factory()->buyer()->create();

        $order = Order::factory()->create([
            'user_id' => $buyer->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 250000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Administrasi Platform');
        $response->assertSee('Gross Merchandise Value (GMV)');
        $response->assertSee('Rp 250.000');
        $response->assertSee('Total Pengguna Terdaftar');
    }

    public function test_buyer_and_seller_cannot_access_admin_dashboard(): void
    {
        $buyer = User::factory()->buyer()->create();
        $seller = User::factory()->seller()->create();

        $this->actingAs($buyer)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($seller)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_view_users_list_with_role_filtering(): void
    {
        $admin = $this->createAdmin();
        $buyer = User::factory()->buyer()->create(['name' => 'Budi Pembeli Santosa', 'email' => 'budi@pembeli.com']);
        $seller = User::factory()->seller()->create(['name' => 'Siti Penjual Berkah', 'email' => 'siti@penjual.com']);
        Shop::factory()->approved()->create(['user_id' => $seller->id, 'name' => 'Toko Berkah Siti']);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee('Manajemen Pengguna Platform');
        $response->assertSee('Budi Pembeli Santosa');
        $response->assertSee('Siti Penjual Berkah');

        // Filter by role buyer
        $responseBuyer = $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'buyer']));
        $responseBuyer->assertOk();
        $responseBuyer->assertSee('Budi Pembeli Santosa');
        $responseBuyer->assertDontSee('Siti Penjual Berkah');

        // Filter by role seller
        $responseSeller = $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'seller']));
        $responseSeller->assertOk();
        $responseSeller->assertSee('Siti Penjual Berkah');
        $responseSeller->assertDontSee('Budi Pembeli Santosa');
    }

    public function test_admin_can_view_products_oversight_list(): void
    {
        $admin = $this->createAdmin();
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        $category = Category::factory()->create(['name' => 'Kategori Pakaian']);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Kemeja Katun Premium',
            'price' => 150000,
            'stock' => 10,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertSee('Katalog Produk Seluruh Penjual');
        $response->assertSee('Kemeja Katun Premium');
        $response->assertSee('Rp 150.000');
        $response->assertSee('Kategori Pakaian');
    }

    public function test_admin_can_view_orders_and_order_details(): void
    {
        $admin = $this->createAdmin();
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create(['user_id' => $seller->id]);
        $buyer = User::factory()->buyer()->create(['name' => 'Ahmad Pembeli']);

        $order = Order::factory()->create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-ADM-TEST-01',
            'status' => OrderStatus::PAID,
            'total_amount' => 125000,
            'notes' => 'Tolong kirim rapi.',
        ]);

        $shopOrder = ShopOrder::factory()->create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => 'ORD-ADM-TEST-01-S1',
            'status' => OrderStatus::PAID,
            'subtotal' => 125000,
        ]);

        $product = Product::factory()->create(['shop_id' => $shop->id]);

        OrderItem::factory()->create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => 'Sepatu Sneakers Kulit',
            'unit_price' => 125000,
            'quantity' => 1,
            'subtotal' => 125000,
        ]);

        // 1. Index orders
        $responseIndex = $this->actingAs($admin)->get(route('admin.orders.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('ORD-ADM-TEST-01');
        $responseIndex->assertSee('Ahmad Pembeli');

        // 2. Show order details
        $responseShow = $this->actingAs($admin)->get(route('admin.orders.show', $order));
        $responseShow->assertOk();
        $responseShow->assertSee('ORD-ADM-TEST-01');
        $responseShow->assertSee('Sepatu Sneakers Kulit');
        $responseShow->assertSee('Tolong kirim rapi.');
    }

    public function test_admin_can_view_payments_and_refunds(): void
    {
        $admin = $this->createAdmin();
        $buyer = User::factory()->buyer()->create();

        $order = Order::factory()->create([
            'user_id' => $buyer->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 75000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-ADM-001',
            'status' => PaymentStatus::SUCCESS,
            'amount' => 75000,
            'payment_method' => 'qris',
        ]);

        // 1. Payments view
        $responsePay = $this->actingAs($admin)->get(route('admin.payments.index'));
        $responsePay->assertOk();
        $responsePay->assertSee('Pusat Pembayaran Gateway');
        $responsePay->assertSee('PAY-ADM-001');
        $responsePay->assertSee('QRIS');

        // 2. Cancel order and verify refunds view
        $order->update(['status' => OrderStatus::CANCELLED, 'cancelled_at' => now()]);

        $responseRef = $this->actingAs($admin)->get(route('admin.refunds.index'));
        $responseRef->assertOk();
        $responseRef->assertSee('Rekonsiliasi Refund');
        $responseRef->assertSee($order->order_number);
    }

    public function test_admin_can_view_reports_and_analytics(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Laporan Performa Bisnis Platform');
        $response->assertSee('Gross Merchandise Value (GMV)');
        $response->assertSee('Ekspor CSV');
        $response->assertSee('Ekspor PDF');
    }

    public function test_admin_can_export_reports_csv(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.reports.export.csv'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('NEXAMARKET - LAPORAN PERFORMA BISNIS PLATFORM', $response->streamedContent());
    }

    public function test_admin_can_export_reports_pdf(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.reports.export.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_accessing_dashboard_is_redirected_to_admin_center(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_access_admin_profile(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.profile'));

        $response->assertOk();
        $response->assertSee('Pengaturan Profil Akun');
        $response->assertSee('Admin Center');
    }

    public function test_admin_accessing_profile_is_redirected_to_admin_profile(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/profile');

        $response->assertRedirect(route('admin.profile'));
    }
}
