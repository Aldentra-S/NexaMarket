<?php

namespace App\Contracts;

interface FirebaseTokenVerifierInterface
{
    /**
     * Verify a Firebase ID token and return its verified claims.
     *
     * @param string $idToken
     * @return array{
     *     uid: string,
     *     email: string,
     *     email_verified: bool,
     *     name: ?string,
     *     picture: ?string
     * }
     * @throws \Exception
     */
    public function verifyIdToken(string $idToken): array;
}
