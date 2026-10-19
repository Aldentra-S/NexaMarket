<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectShopRequest;
use App\Http\Requests\Admin\SuspendShopRequest;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ShopApprovalController extends Controller
{
    /**
     * Display a listing of shop applications for administrative review.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Shop::class);

        $statusFilter = $request->query('status');

        $query = Shop::with('user')->latest();

        if ($statusFilter && in_array($statusFilter, ShopStatus::values(), true)) {
            $query->where('status', $statusFilter);
        }

        $shops = $query->paginate(15)->withQueryString();

        return view('admin.shops.index', [
            'shops' => $shops,
            'currentFilter' => $statusFilter,
            'pendingCount' => Shop::where('status', ShopStatus::PENDING)->count(),
            'approvedCount' => Shop::where('status', ShopStatus::APPROVED)->count(),
            'rejectedCount' => Shop::where('status', ShopStatus::REJECTED)->count(),
            'suspendedCount' => Shop::where('status', ShopStatus::SUSPENDED)->count(),
        ]);
    }

    /**
     * Display the specified shop application details.
     */
    public function show(Shop $shop): View
    {
        Gate::authorize('view', $shop);

        $shop->load('user');

        return view('admin.shops.show', [
            'shop' => $shop,
        ]);
    }

    /**
     * Approve the shop application and elevate the owner to seller.
     */
    public function approve(Request $request, Shop $shop): RedirectResponse
    {
        Gate::authorize('approve', $shop);

        DB::transaction(function () use ($shop) {
            $shop->status = ShopStatus::APPROVED;
            $shop->rejection_reason = null;
            $shop->approved_at = now();
            $shop->save();

            // Elevate user role to SELLER
            $owner = $shop->user;
            $owner->role = UserRole::SELLER;
            $owner->save();
        });

        return redirect()->route('admin.shops.index')
            ->with('status', 'Toko "' . $shop->name . '" berhasil disetujui. Pemilik toko kini resmi memiliki peran Penjual.');
    }

    /**
     * Reject the shop application with a recorded reason.
     */
    public function reject(RejectShopRequest $request, Shop $shop): RedirectResponse
    {
        Gate::authorize('reject', $shop);

        DB::transaction(function () use ($request, $shop) {
            $shop->status = ShopStatus::REJECTED;
            $shop->rejection_reason = $request->validated('rejection_reason');
            $shop->approved_at = null;
            $shop->save();

            // If user's only shop was rejected, maintain/revert role to BUYER
            $owner = $shop->user;
            $owner->role = UserRole::BUYER;
            $owner->save();
        });

        return redirect()->route('admin.shops.index')
            ->with('status', 'Pengajuan toko "' . $shop->name . '" telah ditolak dengan alasan yang disimpan.');
    }

    /**
     * Suspend an approved shop with a recorded reason.
     */
    public function suspend(SuspendShopRequest $request, Shop $shop): RedirectResponse
    {
        Gate::authorize('suspend', $shop);

        DB::transaction(function () use ($request, $shop) {
            $shop->status = ShopStatus::SUSPENDED;
            $shop->suspension_reason = $request->validated('suspension_reason');
            $shop->suspended_at = now();
            $shop->save();

            // Revert owner role to BUYER while shop is suspended
            $owner = $shop->user;
            $owner->role = UserRole::BUYER;
            $owner->save();
        });

        return redirect()->route('admin.shops.index')
            ->with('status', 'Toko "' . $shop->name . '" telah berhasil ditangguhkan.');
    }

    /**
     * Reactivate a suspended shop back to approved status.
     */
    public function reactivate(Request $request, Shop $shop): RedirectResponse
    {
        Gate::authorize('reactivate', $shop);

        DB::transaction(function () use ($shop) {
            $shop->status = ShopStatus::APPROVED;
            $shop->suspension_reason = null;
            $shop->suspended_at = null;
            $shop->save();

            // Restore owner role to SELLER
            $owner = $shop->user;
            $owner->role = UserRole::SELLER;
            $owner->save();
        });

        return redirect()->route('admin.shops.index')
            ->with('status', 'Toko "' . $shop->name . '" berhasil diaktifkan kembali. Pemilik toko kini dapat kembali mengakses Seller Center.');
    }
}
