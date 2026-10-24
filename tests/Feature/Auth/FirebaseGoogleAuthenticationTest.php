<?php

namespace Tests\Feature\Auth;

use App\Contracts\FirebaseTokenVerifierInterface;
use App\Enums\UserRole;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirebaseGoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function bindFakeVerifier(array $claims = [], ?Exception $exception = null): void
    {
        $fake = new class($claims, $exception) implements FirebaseTokenVerifierInterface {
            public function __construct(
                private array $claims,
                private ?Exception $exception
            ) {}

            public function verifyIdToken(string $idToken): array
            {
                if ($this->exception) {
                    throw $this->exception;
                }

                return $this->claims;
            }
        };

        $this->app->instance(FirebaseTokenVerifierInterface::class, $fake);
    }

    public function test_successful_google_login_creates_new_buyer_user_and_regenerates_session(): void
    {
        $this->bindFakeVerifier([
            'uid' => 'google-uid-12345',
            'email' => 'newbuyer@example.com',
            'email_verified' => true,
            'name' => 'Budi Pembeli Baru',
            'picture' => 'https://lh3.googleusercontent.com/photo.jpg',
        ]);

        $initialSessionId = session()->getId();

        $response = $this->postJson(route('auth.firebase.google'), [
            'id_token' => 'valid-mock-token',
        ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'redirect' => route('dashboard', absolute: false),
            'user' => [
                'email' => 'newbuyer@example.com',
                'role' => UserRole::BUYER->value,
            ],
        ]);

        $this->assertAuthenticated();
        $user = auth()->user();
        $this->assertSame('newbuyer@example.com', $user->email);
        $this->assertSame('google-uid-12345', $user->firebase_uid);
        $this->assertSame(UserRole::BUYER, $user->role);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotSame($initialSessionId, session()->getId());
    }

    public function test_successful_google_login_authenticates_existing_firebase_user(): void
    {
        $existingUser = User::factory()->buyer()->create([
            'email' => 'returning@example.com',
            'firebase_uid' => 'google-uid-99999',
            'name' => 'Returning User',
        ]);

        $this->bindFakeVerifier([
            'uid' => 'google-uid-99999',
            'email' => 'returning@example.com',
            'email_verified' => true,
            'name' => 'Returning User',
            'picture' => null,
        ]);

        $response = $this->postJson(route('auth.firebase.google'), [
            'id_token' => 'valid-mock-token',
        ]);

        $response->assertOk();
        $this->assertAuthenticatedAs($existingUser);
    }

    public function test_unverified_email_token_is_rejected(): void
    {
        $this->bindFakeVerifier([
            'uid' => 'unverified-uid',
            'email' => 'unverified@example.com',
            'email_verified' => false,
            'name' => 'Unverified Person',
            'picture' => null,
        ]);

        $response = $this->postJson(route('auth.firebase.google'), [
            'id_token' => 'token-unverified-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['token']);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
    }

    public function test_invalid_or_expired_token_is_rejected(): void
    {
        $this->bindFakeVerifier([], new Exception('Token ID Firebase telah kedaluwarsa atau tidak valid.'));

        $response = $this->postJson(route('auth.firebase.google'), [
            'id_token' => 'expired-or-malformed-token',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['token']);
        $this->assertGuest();
    }

    public function test_existing_local_password_account_cannot_be_taken_over_by_google_login(): void
    {
        // Existing local user created via password registration (firebase_uid is null)
        $localUser = User::factory()->buyer()->create([
            'email' => 'victim@example.com',
            'password' => bcrypt('secret123'),
            'firebase_uid' => null,
            'name' => 'Original Account Holder',
        ]);

        // Attacker attempts Google login with a verified token containing victim's email
        $this->bindFakeVerifier([
            'uid' => 'attacker-google-uid-666',
            'email' => 'victim@example.com',
            'email_verified' => true,
            'name' => 'Attacker Name',
            'picture' => null,
        ]);

        $response = $this->postJson(route('auth.firebase.google'), [
            'id_token' => 'attacker-token',
        ]);

        // Security requirement: Reject takeover
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['token']);
        $this->assertGuest();

        // Local account remains untouched
        $localUser->refresh();
        $this->assertNull($localUser->firebase_uid);
        $this->assertSame('Original Account Holder', $localUser->name);
    }

    public function test_endpoint_ignores_and_does_not_accept_injected_role_from_browser(): void
    {
        $this->bindFakeVerifier([
            'uid' => 'google-uid-tamper',
            'email' => 'tamper@example.com',
            'email_verified' => true,
            'name' => 'Tamper Attempt',
            'picture' => null,
        ]);

        $maliciousPayload = [
            'id_token' => 'valid-mock-token',
            'role' => 'admin',
            'email' => 'admin@hacked.com',
            'uid' => 'fake-admin-uid',
        ];

        $response = $this->postJson(route('auth.firebase.google'), $maliciousPayload);

        $response->assertOk();
        $createdUser = User::where('email', 'tamper@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertSame(UserRole::BUYER, $createdUser->role);
        $this->assertFalse($createdUser->isAdmin());
    }

    public function test_google_authenticated_user_can_logout_via_standard_route(): void
    {
        $user = User::factory()->buyer()->create([
            'email' => 'buyer@example.com',
            'firebase_uid' => 'google-uid-logout-test',
        ]);

        $this->actingAs($user);
        $this->assertAuthenticated();

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_google_authenticated_buyer_cannot_access_seller_or_admin_routes(): void
    {
        $user = User::factory()->buyer()->create([
            'email' => 'regularbuyer@example.com',
            'firebase_uid' => 'google-uid-buyer-only',
        ]);

        $resSeller = $this->actingAs($user)->get(route('seller.dashboard'));
        $resSeller->assertForbidden();

        $resAdmin = $this->actingAs($user)->get(route('admin.dashboard'));
        $resAdmin->assertForbidden();
    }
}
