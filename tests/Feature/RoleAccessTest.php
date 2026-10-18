<?php

namespace Tests\Feature;

use App\Enums\UserRole;
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
        $this->assertEquals('seller', UserRole::SELLER->value);
        $this->assertEquals('admin', UserRole::ADMIN->value);

        $this->assertEquals('Pembeli', UserRole::BUYER->label());
        $this->assertEquals('Penjual', UserRole::SELLER->label());
        $this->assertEquals('Administrator', UserRole::ADMIN->label());

        $this->assertNotEmpty(UserRole::BUYER->badgeColor());
        $this->assertContains('buyer', UserRole::values());
        $this->assertContains('seller', UserRole::values());
        $this->assertContains('admin', UserRole::values());
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

    public function test_buyers_cannot_access_seller_or_admin_routes(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->get('/buyer/area')->assertOk();
        $this->actingAs($buyer)->get('/seller/dashboard')->assertForbidden();
        $this->actingAs($buyer)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_sellers_cannot_access_admin_routes(): void
    {
        $seller = User::factory()->seller()->create();

        $this->actingAs($seller)->get('/seller/dashboard')->assertOk();
        $this->actingAs($seller)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_admins_can_access_admin_route(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    }

    public function test_authorization_gates_work_as_expected(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->seller()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue(Gate::forUser($buyer)->allows('access-buyer'));
        $this->assertFalse(Gate::forUser($buyer)->allows('access-seller'));
        $this->assertFalse(Gate::forUser($buyer)->allows('access-admin'));

        $this->assertFalse(Gate::forUser($seller)->allows('access-buyer'));
        $this->assertTrue(Gate::forUser($seller)->allows('access-seller'));
        $this->assertFalse(Gate::forUser($seller)->allows('access-admin'));

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
}
