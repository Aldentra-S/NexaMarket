<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin', fn (User $user) => $user->isAdmin());
        Gate::define('access-seller', fn (User $user) => $user->isSeller());
        Gate::define('access-buyer', fn (User $user) => $user->isBuyer());

        Gate::policy(\App\Models\CartItem::class, \App\Policies\CartItemPolicy::class);
        Gate::policy(\App\Models\Order::class, \App\Policies\OrderPolicy::class);
    }


}
