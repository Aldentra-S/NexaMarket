<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_enum_has_all_expected_roles_and_methods(): void
    {
        $this->assertEquals('buyer', UserRole::BUYER->value);
        $this->assertEquals('admin', UserRole::ADMIN->value);

        $this->assertEquals('Pembeli', UserRole::BUYER->label());
        $this->assertEquals('Administrator', UserRole::ADMIN->label());

        $this->assertNotEmpty(UserRole::BUYER->badgeColor());
        $this->assertNotEmpty(UserRole::ADMIN->badgeColor());
        $this->assertContains('buyer', UserRole::values());
        $this->assertContains('admin', UserRole::values());
        $this->assertCount(2, UserRole::values());
    }

    public function test_newly_registered_user_defaults_to_buyer_role(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Pendaftar Publik')
            ->set('email', 'buyer@nexamarket.test')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'buyer@nexamarket.test')->first();

        $this->assertNotNull($user);
        $this->assertSame(UserRole::BUYER, $user->role);
        $this->assertTrue($user->isBuyer());
        $this->assertFalse($user->isSeller());
        $this->assertFalse($user->isAdmin());
    }

    public function test_role_cannot_be_overwritten_via_mass_assignment(): void
    {
        $user = User::create([
            'name' => 'Potential Exploit User',
            'email' => 'exploit@nexamarket.test',
            'password' => 'secret123',
            'role' => 'admin',
        ]);

        $this->assertSame(UserRole::BUYER, $user->fresh()->role);
        $this->assertFalse($user->isAdmin());
    }

    public function test_guests_are_redirected_to_login_from_role_routes(): void
    {
        $this->get('/buyer/area')->assertRedirect('/login');
        $this->get('/seller/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_buyers_without_shop_cannot_access_seller_or_admin_routes(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->get('/buyer/area')->assertOk();
        $this->actingAs($buyer)->get('/seller/dashboard')->assertForbidden();
        $this->actingAs($buyer)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_buyer_with_pending_shop_is_denied_from_seller_center(): void
    {
        $buyer = User::factory()->create();
        Shop::factory()->pending()->create(['user_id' => $buyer->id]);

        $this->assertTrue($buyer->hasShop());
        $this->assertFalse($buyer->hasApprovedShop());
        $this->assertFalse($buyer->canAccessSellerCenter());

        $this->actingAs($buyer)->get('/buyer/area')->assertOk();
        $this->actingAs($buyer)->get('/seller/dashboard')->assertForbidden();
        $this->actingAs($buyer)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_buyer_with_approved_shop_can_access_both_buyer_area_and_seller_center(): void
    {
        $seller = User::factory()->seller()->create();
        Shop::factory()->approved()->create(['user_id' => $seller->id]);

        $this->assertSame(UserRole::BUYER, $seller->role);
        $this->assertTrue($seller->isBuyer());
        $this->assertTrue($seller->isSeller());
        $this->assertTrue($seller->hasApprovedShop());
        $this->assertTrue($seller->canAccessSellerCenter());

        // Same account can access Buyer Area and Seller Center, but not Admin
        $this->actingAs($seller)->get('/buyer/area')->assertOk();
        $this->actingAs($seller)->get('/seller/dashboard')->assertOk();
        $this->actingAs($seller)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_admins_can_access_admin_route_but_cannot_access_seller_center(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin)->get('/seller/dashboard')->assertForbidden();
        $this->actingAs($admin)->get('/buyer/area')->assertForbidden();
    }

    public function test_authorization_gates_work_as_expected(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->seller()->create();
        Shop::factory()->approved()->create(['user_id' => $seller->id]);

        $pendingBuyer = User::factory()->create();
        Shop::factory()->pending()->create(['user_id' => $pendingBuyer->id]);

        $admin = User::factory()->admin()->create();

        // Regular buyer without shop
        $this->assertTrue(Gate::forUser($buyer)->allows('access-buyer'));
        $this->assertFalse(Gate::forUser($buyer)->allows('access-seller'));
        $this->assertFalse(Gate::forUser($buyer)->allows('access-admin'));

        // Buyer with approved shop (can shop as buyer AND manage seller center)
        $this->assertTrue(Gate::forUser($seller)->allows('access-buyer'));
        $this->assertTrue(Gate::forUser($seller)->allows('access-seller'));
        $this->assertFalse(Gate::forUser($seller)->allows('access-admin'));

        // Buyer with pending shop (cannot access seller center yet)
        $this->assertTrue(Gate::forUser($pendingBuyer)->allows('access-buyer'));
        $this->assertFalse(Gate::forUser($pendingBuyer)->allows('access-seller'));
        $this->assertFalse(Gate::forUser($pendingBuyer)->allows('access-admin'));

        // Admin
        $this->assertFalse(Gate::forUser($admin)->allows('access-buyer'));
        $this->assertFalse(Gate::forUser($admin)->allows('access-seller'));
        $this->assertTrue(Gate::forUser($admin)->allows('access-admin'));
    }

    public function test_marketplace_welcome_screen_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('NexaMarket');
        $response->assertSee('Peran Pembeli (Buyer)');
        $response->assertSee('Peran Penjual (Seller)');
        $response->assertSee('Peran Administrator');
    }

    public function test_marketplace_welcome_screen_renders_successfully_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Pembeli',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('NexaMarket');
        $response->assertSee('Budi Pembeli');
        $response->assertSee('Keluar (Logout)');
    }

    public function test_logout_named_route_is_available_and_uses_post(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('logout'));

        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('logout');
        $this->assertNotNull($route);
        $this->assertContains('POST', $route->methods());

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
