<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate user with email and password, issuing a personal Bearer token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->validated('email')));
        $password = $request->validated('password');

        $user = User::where('email', $email)->first();

        if (! $user || empty($user->password) || ! Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
                'errors' => [
                    'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
                ],
            ], 422);
        }

        $deviceName = $request->validated('device_name') ?: 'nexamarket_mobile';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                ],
            ],
        ]);
    }

    /**
     * Register a new buyer account and issue an initial Bearer token.
     * Public registration is strictly restricted to BUYER role.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => trim($request->validated('name')),
            'email' => strtolower(trim($request->validated('email'))),
            'password' => $request->validated('password'),
        ]);

        // Explicitly ensure user is BUYER role
        if ($user->role !== UserRole::BUYER) {
            $user->forceFill(['role' => UserRole::BUYER])->save();
        }

        $deviceName = $request->validated('device_name') ?: 'nexamarket_mobile';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi akun pembeli berhasil.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                ],
            ],
        ], 201);
    }

    /**
     * Retrieve the authenticated user's profile details.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil pengguna berhasil diambil.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                    'created_at' => $user->created_at?->toIso8601String(),
                ],
            ],
        ]);
    }

    /**
     * Revoke the current access token used for this request.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil. Token aktif telah dicabut.',
        ]);
    }
}
