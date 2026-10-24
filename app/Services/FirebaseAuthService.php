<?php

namespace App\Services;

use App\Contracts\FirebaseTokenVerifierInterface;
use App\Enums\UserRole;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FirebaseAuthService
{
    public function __construct(
        private FirebaseTokenVerifierInterface $verifier
    ) {}

    /**
     * Authenticate or register a user using a verified Firebase Google ID token.
     *
     * @throws ValidationException
     */
    public function authenticateWithToken(string $idToken): User
    {
        try {
            $claims = $this->verifier->verifyIdToken($idToken);
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'token' => 'Autentikasi Google gagal: ' . $e->getMessage(),
            ]);
        }

        // 1. Enforce verified email policy
        if (empty($claims['email']) || ! ($claims['email_verified'] ?? false)) {
            throw ValidationException::withMessages([
                'token' => 'Email akun Google Anda belum diverifikasi oleh Google. Silakan verifikasi email Anda terlebih dahulu.',
            ]);
        }

        $firebaseUid = $claims['uid'];
        $email = strtolower(trim($claims['email']));

        // 2. Lookup existing user by verified Firebase UID
        $user = User::where('firebase_uid', $firebaseUid)->first();

        if ($user) {
            return $user;
        }

        // 3. Security Check / Anti-Takeover:
        // If an account with this email already exists locally without this firebase_uid,
        // do NOT automatically link or log in. Reject with clear guidance to prevent account takeover.
        $existingLocalUser = User::where('email', $email)->first();
        if ($existingLocalUser) {
            throw ValidationException::withMessages([
                'token' => 'Email ini sudah terdaftar menggunakan kata sandi lokal. Silakan masuk menggunakan form login email dan kata sandi.',
            ]);
        }

        // 4. Create new user account with default role BUYER
        $displayName = ! empty($claims['name']) ? trim($claims['name']) : Str::headline(Str::before($email, '@'));

        return User::create([
            'name' => $displayName,
            'email' => $email,
            'firebase_uid' => $firebaseUid,
            'email_verified_at' => now(),
            'role' => UserRole::BUYER, // Role is explicitly BUYER, never trusted from browser
            'password' => null,
        ]);
    }
}
