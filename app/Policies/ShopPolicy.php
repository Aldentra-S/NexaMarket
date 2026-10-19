<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    /**
     * Determine whether the user can view any models (e.g. admin review list).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Shop $shop): bool
    {
        // Admin can view any shop
        if ($user->isAdmin()) {
            return true;
        }

        // Shop owner can view their own shop (even if pending or rejected)
        if ($user->id === $shop->user_id) {
            return true;
        }

        // Public/other buyers can only view approved shops
        return $shop->isApproved();
    }

    /**
     * Determine whether the user can apply to create a shop.
     */
    public function create(User $user): bool
    {
        // Buyer can create a shop only if they don't already have one, or if previously rejected
        if ($user->shop === null) {
            return true;
        }

        return $user->shop->isRejected();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Shop $shop): bool
    {
        // Admin can update
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can update only their own approved shop
        return $user->id === $shop->user_id && $shop->isApproved();
    }

    /**
     * Determine whether the user can approve the shop.
     */
    public function approve(User $user, Shop $shop): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reject the shop.
     */
    public function reject(User $user, Shop $shop): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can suspend the shop.
     */
    public function suspend(User $user, Shop $shop): bool
    {
        return $user->isAdmin() && $shop->isApproved();
    }

    /**
     * Determine whether the user can reactivate the shop.
     */
    public function reactivate(User $user, Shop $shop): bool
    {
        return $user->isAdmin() && $shop->isSuspended();
    }
}
