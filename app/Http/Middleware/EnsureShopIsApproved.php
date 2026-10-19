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

        // Must have role seller
        if ($user->role !== UserRole::SELLER) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak: Anda harus memiliki peran Penjual resmi.');
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
