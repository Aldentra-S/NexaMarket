<?php

namespace App\Policies;

use App\Models\ShopOrder;
use App\Models\User;

class ShopOrderPolicy
{
    /**
     * Determine whether the user can view any shop orders for their shop.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSeller() && $user->hasApprovedShop();
    }

    /**
     * Determine whether the user can view the specific shop order.
     */
    public function view(User $user, ShopOrder $shopOrder): bool
    {
        return $user->isSeller() 
            && $user->hasApprovedShop() 
            && $user->shop 
            && $shopOrder->shop_id === $user->shop->id;
    }

    /**
     * Determine whether the user can update the specific shop order status.
     */
    public function update(User $user, ShopOrder $shopOrder): bool
    {
        return $user->isSeller() 
            && $user->hasApprovedShop() 
            && $user->shop 
            && $shopOrder->shop_id === $user->shop->id;
    }
}
