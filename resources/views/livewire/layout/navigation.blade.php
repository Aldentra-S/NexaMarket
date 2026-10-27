<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false, logoutModalOpen: false }" @keydown.escape.window="open = false; logoutModalOpen = false" class="bg-white border-b border-slate-200 relative z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-50">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo & Workspace Tag -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                        <x-nexa-logo size="sm" />
                    </a>

                    @if (auth()->user()->isAdmin())
                        <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            Admin Center
                        </span>
                    @elseif (auth()->user()->isSeller())
                        <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                            Seller Center
                        </span>
                    @else
                        <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                            Member Area
                        </span>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if (auth()->user()->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 text-orange-600 font-bold border border-orange-200 text-xs hover:bg-orange-100 transition">
                                <span>Admin Center</span>
                                <span class="text-orange-400">&rarr;</span>
                            </span>
                        </x-nav-link>
                    @elseif (auth()->user()->isSeller())
                        <x-nav-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.dashboard')">
                            {{ __('Seller Center') }}
                        </x-nav-link>
                        <x-nav-link :href="route('seller.products.index')" :active="request()->routeIs('seller.products.*')">
                            {{ __('Produk Saya') }}
                        </x-nav-link>
                        <x-nav-link :href="route('seller.orders.index')" :active="request()->routeIs('seller.orders.*')">
                            {{ __('Pesanan Masuk') }}
                        </x-nav-link>
                        <x-nav-link :href="route('seller.reviews.index')" :active="request()->routeIs('seller.reviews.*')">
                            {{ __('Ulasan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('seller.shop.settings')" :active="request()->routeIs('seller.shop.settings')">
                            {{ __('Pengaturan Toko') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        {{ __('Katalog Publik') }}
                    </x-nav-link>

                    <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                        <span class="flex items-center gap-1.5">
                            {{ __('Keranjang') }}
                            @if ((auth()->user()->cart?->totalQuantity() ?? 0) > 0)
                                <span class="px-1.5 py-0.2 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full">
                                    {{ auth()->user()->cart->totalQuantity() }}
                                </span>
                            @endif
                        </span>
                    </x-nav-link>

                    <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                        {{ __('Pesanan Saya') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-slate-200 text-xs font-semibold rounded-xl text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition">
                            <div class="w-6 h-6 rounded-md bg-orange-600 text-white font-bold flex items-center justify-center text-[10px]">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2.5 border-b border-slate-100">
                            <p class="text-[11px] text-slate-400 font-medium">Akun Pengguna</p>
                            <p class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                {{ auth()->user()->role->label() }}
                            </span>
                        </div>

                        <x-dropdown-link :href="route('orders.index')">
                            {{ __('Pesanan Saya') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Pengaturan Profil') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('products.index')">
                            {{ __('Belanja di Marketplace') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <div class="border-t border-slate-100">
                            <button type="button" @click="logoutModalOpen = true" class="w-full text-start cursor-pointer">
                                <x-dropdown-link class="text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-semibold">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click.stop="open = ! open"
                    :aria-expanded="open.toString()"
                    aria-controls="mobile-workspace-nav"
                    aria-label="Toggle Navigation Menu"
                    class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Backdrop Overlay -->
    <div
        x-show="open"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-slate-900/40 z-40 sm:hidden"
        style="display: none;"
        aria-hidden="true"
    ></div>

    <!-- Responsive Mobile Navigation Panel Overlay -->
    <div
        id="mobile-workspace-nav"
        data-ui
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        @click.outside="open = false"
        @click="if ($event.target.closest('a, button')) open = false"
        class="absolute top-full left-0 right-0 z-50 bg-white border-b border-slate-200 shadow-xl max-h-[calc(100vh-4rem)] overflow-y-auto sm:hidden" {{-- taste-skill-ignore --}}
        style="display: none;"
    >
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if (auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    <span class="font-bold text-orange-600">{{ __('Masuk Admin Center') }} &rarr;</span>
                </x-responsive-nav-link>
            @elseif (auth()->user()->isSeller())
                <x-responsive-nav-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.dashboard')">
                    {{ __('Seller Center') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('seller.products.index')" :active="request()->routeIs('seller.products.*')">
                    {{ __('Produk Saya') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('seller.orders.index')" :active="request()->routeIs('seller.orders.*')">
                    {{ __('Pesanan Masuk') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('seller.reviews.index')" :active="request()->routeIs('seller.reviews.*')">
                    {{ __('Ulasan Toko') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('seller.shop.settings')" :active="request()->routeIs('seller.shop.settings*')">
                    {{ __('Pengaturan Toko') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Katalog Publik') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                {{ __('Keranjang Belanja') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                {{ __('Pesanan Saya') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-slate-100 px-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-orange-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-xs text-slate-800 truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('orders.index')">
                    {{ __('Pesanan Saya') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Pengaturan Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button type="button" @click="logoutModalOpen = true; open = false" class="w-full text-start cursor-pointer">
                    <x-responsive-nav-link class="text-rose-600 font-semibold">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Logout Confirmation Modal (No Window Alert) -->
    <div 
        x-show="logoutModalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="nav-logout-modal-title" 
        role="dialog" 
        aria-modal="true"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Modal Backdrop -->
            <div 
                x-show="logoutModalOpen" 
                x-transition:enter="transition-opacity ease-out duration-200" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="transition-opacity ease-out duration-150" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                @click="logoutModalOpen = false" 
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" {{-- taste-skill-ignore --}}
            ></div>

            <!-- Modal Content Card -->
            <div 
                x-show="logoutModalOpen" 
                x-transition:enter="transition ease-out duration-200" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="transition ease-out duration-150" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-left align-middle bg-white rounded-2xl shadow-xl transition-all border border-slate-200 z-10" {{-- taste-skill-ignore --}}
            >
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900" id="nav-logout-modal-title">Konfirmasi Keluar</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Sesi akun Anda akan diakhiri.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 mt-3.5 leading-relaxed">
                    Apakah Anda yakin ingin keluar dari akun NexaMarket Anda? Anda harus masuk kembali untuk melanjutkan transaksi atau pengelolaan toko.
                </p>

                <div class="mt-6 flex items-center justify-end gap-2.5">
                    <button 
                        type="button" 
                        @click="logoutModalOpen = false" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="logout" 
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer shadow-xs"
                    >
                        Ya, Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
