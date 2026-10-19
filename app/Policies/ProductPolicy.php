<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models in seller area.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || ($user->isSeller() && $user->hasApprovedShop());
    }

    /**
     * Determine whether the user can view the model in seller area.
     */
    public function view(User $user, Product $product): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isSeller() 
            && $user->hasApprovedShop() 
            && $product->shop 
            && $user->id === $product->shop->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSeller() && $user->hasApprovedShop();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isSeller() 
            && $user->hasApprovedShop() 
            && $product->shop 
            && $user->id === $product->shop->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isSeller() 
            && $user->hasApprovedShop() 
            && $product->shop 
            && $user->id === $product->shop->user_id;
    }
}
