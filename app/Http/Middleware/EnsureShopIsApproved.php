<?php

namespace App\Http\Middleware;

use App\Enums\ShopStatus;
use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Administrators are not seller store operators
        if ($user->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak: Administrator tidak dapat mengelola Seller Center.');
        }

        // Must have a registered shop with status APPROVED
        $shop = $user->shop;

        if (! $shop || $shop->status !== ShopStatus::APPROVED) {
            if ($shop?->isSuspended()) {
                abort(Response::HTTP_FORBIDDEN, 'Akses ditolak: Toko Anda sedang ditangguhkan oleh Administrator.');
            }

            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak: Toko Anda belum disetujui atau masih dalam proses peninjauan.');
        }

        return $next($request);
    }
}
