<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_check_returns_success_json_without_sensitive_info(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'NexaMarket REST API is healthy and operational.',
                'data' => [
                    'service' => 'NexaMarket API',
                    'version' => 'v1',
                ],
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'service',
                    'version',
                    'timestamp',
                ],
            ]);

        // Ensure no sensitive app keys, credentials, or env details leaked
        $content = $response->getContent();
        $this->assertStringNotContainsString('password', $content);
        $this->assertStringNotContainsString('database', $content);
        $this->assertStringNotContainsString('secret', $content);
    }

    public function test_user_can_login_with_valid_credentials_and_receives_bearer_token(): void
    {
        $user = User::factory()->create([
            'email' => 'buyer@nexamarket.test',
            'password' => 'secret12345',
            'role' => UserRole::BUYER,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@nexamarket.test',
            'password' => 'secret12345',
            'device_name' => 'flutter_android_test',
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Login berhasil.',
                'data' => [
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => 'buyer@nexamarket.test',
                        'role' => 'buyer',
                    ],
                ],
            ]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);

        // Verify token exists in database hashed
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'flutter_android_test',
        ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'buyer@nexamarket.test',
            'password' => 'secret12345',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@nexamarket.test',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
            ])
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_when_user_has_no_password(): void
    {
        User::create([
            'name' => 'Google Only User',
            'email' => 'google@nexamarket.test',
            'firebase_uid' => 'firebase_uid_test_123',
            'password' => null,
            'role' => UserRole::BUYER,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'google@nexamarket.test',
            'password' => 'anypassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
            ])
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_buyer_can_register_successfully_and_receives_bearer_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Ahmad Pembeli',
            'email' => 'ahmad@nexamarket.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'flutter_android',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Registrasi akun pembeli berhasil.',
                'data' => [
                    'token_type' => 'Bearer',
                    'user' => [
                        'name' => 'Ahmad Pembeli',
                        'email' => 'ahmad@nexamarket.test',
                        'role' => 'buyer',
                    ],
                ],
            ]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);

        $user = User::where('email', 'ahmad@nexamarket.test')->first();
        $this->assertNotNull($user);
        $this->assertSame(UserRole::BUYER, $user->role);
        $this->assertTrue($user->isBuyer());
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_registration_fails_with_invalid_data_or_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@nexamarket.test']);

        // Test duplicate email
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Duplicate User',
            'email' => 'existing@nexamarket.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test password mismatch
        $responseMismatch = $this->postJson('/api/v1/auth/register', [
            'name' => 'Mismatch User',
            'email' => 'newuser@nexamarket.test',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $responseMismatch->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_strictly_forces_buyer_role_even_if_admin_or_seller_requested(): void
    {
        // Attacker attempts to register with role admin
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Sneaky User',
            'email' => 'sneaky@nexamarket.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.role', 'buyer');

        $user = User::where('email', 'sneaky@nexamarket.test')->first();
        $this->assertSame(UserRole::BUYER, $user->role);
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isSeller());
    }

    public function test_profile_endpoint_rejects_unauthenticated_request_with_401_json(): void
    {
        // Request without Bearer token must receive 401 JSON, NOT redirect to web login
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_profile_endpoint_returns_user_data_when_authenticated_with_valid_bearer_token(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@nexamarket.test',
            'role' => UserRole::BUYER,
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Profil pengguna berhasil diambil.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => 'Budi Santoso',
                        'email' => 'budi@nexamarket.test',
                        'role' => 'buyer',
                    ],
                ],
            ]);
    }

    public function test_logout_revokes_current_access_token(): void
    {
        $user = User::factory()->create();

        // Create two tokens (e.g. tablet and mobile)
        $tokenMobile = $user->createToken('mobile')->plainTextToken;
        $tokenTablet = $user->createToken('tablet')->plainTextToken;

        $this->assertCount(2, $user->tokens);

        // Logout using mobile token
        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenMobile)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Logout berhasil. Token aktif telah dicabut.',
            ]);

        // Mobile token was deleted, but tablet token is still active
        $this->assertCount(1, $user->fresh()->tokens);
        $this->assertEquals('tablet', $user->fresh()->tokens->first()->name);
    }

    public function test_revoked_token_cannot_be_used_again(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile')->plainTextToken;

        // Logout revokes token
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertDatabaseEmpty('personal_access_tokens');

        // Reset in-memory test auth guard state so next request re-authenticates from database
        \Illuminate\Support\Facades\Auth::forgetGuards();

        // Attempting to access profile with revoked token must return 401
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_web_routes_and_authentication_remain_fully_functional(): void
    {
        // 1. Welcome page renders properly
        $welcome = $this->get('/');
        $welcome->assertOk()
            ->assertSee('NexaMarket');

        // 2. Web session login works
        $user = User::factory()->create([
            'email' => 'webuser@nexamarket.test',
            'password' => 'password123',
            'role' => UserRole::BUYER,
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk();
    }
}
