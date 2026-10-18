<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowedRoles = array_map(function ($role) {
            return $role instanceof UserRole ? $role->value : (string) $role;
        }, $roles);

        $currentUserRole = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if (! in_array($currentUserRole, $allowedRoles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Akses tidak diizinkan untuk peran ini.');
        }

        return $next($request);
    }
}
