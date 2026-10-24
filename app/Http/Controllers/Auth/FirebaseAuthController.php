<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\FirebaseLoginRequest;
use App\Services\FirebaseAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FirebaseAuthController extends Controller
{
    /**
     * Handle incoming Google sign-in via Firebase ID token.
     */
    public function handleGoogleLogin(FirebaseLoginRequest $request, FirebaseAuthService $authService): JsonResponse
    {
        $idToken = $request->validated('id_token');

        $user = $authService->authenticateWithToken($idToken);

        // Authenticate user via Laravel session
        Auth::login($user);

        // Regenerate session ID to prevent session fixation attacks
        $request->session()->regenerate();

        return response()->json([
            'status' => 'success',
            'message' => 'Login Google berhasil.',
            'redirect' => route('dashboard', absolute: false),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
            ],
        ]);
    }
}
