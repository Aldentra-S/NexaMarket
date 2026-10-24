<?php

namespace App\Services;

use App\Contracts\FirebaseTokenVerifierInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class FirebaseTokenVerifier implements FirebaseTokenVerifierInterface
{
    private const GOOGLE_PUBLIC_KEYS_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';

    public function __construct(
        private ?string $projectId = null
    ) {
        $this->projectId = $projectId ?: config('services.firebase.project_id', 'nexamarket-56e7c');
    }

    /**
     * Verify a Firebase ID token and return its verified claims.
     *
     * @throws Exception
     */
    public function verifyIdToken(string $idToken): array
    {
        if (empty(trim($idToken))) {
            throw new InvalidArgumentException('ID token tidak boleh kosong.');
        }

        // 1. Decode header to extract Key ID (kid) and algorithm
        $parts = explode('.', $idToken);
        if (count($parts) !== 3) {
            throw new Exception('Format token ID Firebase tidak valid.');
        }

        $headerJson = base64_decode(strtr($parts[0], '-_', '+/'));
        $header = json_decode($headerJson, true);

        if (! isset($header['kid']) || ! isset($header['alg']) || $header['alg'] !== 'RS256') {
            throw new Exception('Header token Firebase tidak valid atau tidak menggunakan algoritma RS256.');
        }

        $kid = $header['kid'];

        // 2. Fetch Google public certificates
        $publicKeys = $this->getGooglePublicKeys();

        if (! isset($publicKeys[$kid])) {
            // Invalidate cache and retry once in case keys rotated
            Cache::forget('firebase_google_public_keys');
            $publicKeys = $this->getGooglePublicKeys();

            if (! isset($publicKeys[$kid])) {
                throw new Exception("Sertifikat publik Google untuk Key ID '{$kid}' tidak ditemukan.");
            }
        }

        $publicKeyPem = $publicKeys[$kid];

        // 3. Verify signature and standard JWT claims (exp, nbf, iat)
        try {
            $decoded = JWT::decode($idToken, new Key($publicKeyPem, 'RS256'));
        } catch (Exception $e) {
            throw new Exception('Verifikasi tanda tangan token Firebase gagal: ' . $e->getMessage(), 0, $e);
        }

        // 4. Verify Firebase-specific claims
        $expectedIssuer = "https://securetoken.google.com/{$this->projectId}";
        if (! isset($decoded->iss) || $decoded->iss !== $expectedIssuer) {
            throw new Exception("Issuer token tidak valid. Diharapkan: '{$expectedIssuer}'.");
        }

        if (! isset($decoded->aud) || $decoded->aud !== $this->projectId) {
            throw new Exception("Audience token tidak valid. Diharapkan project ID: '{$this->projectId}'.");
        }

        if (empty($decoded->sub)) {
            throw new Exception('Subject (UID) pada token Firebase kosong.');
        }

        if (isset($decoded->auth_time) && $decoded->auth_time > (time() + 300)) {
            throw new Exception('Waktu autentikasi token Firebase berada di masa depan.');
        }

        return [
            'uid' => (string) $decoded->sub,
            'email' => isset($decoded->email) ? (string) $decoded->email : '',
            'email_verified' => (bool) ($decoded->email_verified ?? false),
            'name' => isset($decoded->name) ? (string) $decoded->name : null,
            'picture' => isset($decoded->picture) ? (string) $decoded->picture : null,
        ];
    }

    /**
     * Retrieve Google public certificates with caching.
     */
    private function getGooglePublicKeys(): array
    {
        return Cache::remember('firebase_google_public_keys', 3600, function () {
            $response = Http::timeout(5)->get(self::GOOGLE_PUBLIC_KEYS_URL);

            if (! $response->successful()) {
                throw new Exception('Gagal mengambil sertifikat publik Google dari server.');
            }

            return $response->json() ?? [];
        });
    }
}
