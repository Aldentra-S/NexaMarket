<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'NexaMarket') }} | Belanja Mudah, Hidup Lebih Baik</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=4">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}?v=4">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=4">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-full flex flex-col" x-data="{ mobileMenuOpen: false, logoutModalOpen: false }" @keydown.escape.window="mobileMenuOpen = false; logoutModalOpen = false">
    <!-- Main Marketplace Header -->
    @php
        $headerCategories = \App\Models\Category::active()->orderBy('name')->get();
    @endphp
    <header @keydown.escape.window="mobileMenuOpen = false" class="sticky top-0 z-40 bg-white border-b border-slate-200/80 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-50">
            <div class="flex items-center justify-between h-20 gap-4 lg:gap-8">
                <!-- Brand Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="group transition-opacity hover:opacity-90">
                        <x-nexa-logo size="md" :subtitle="true" />
                    </a>
                </div>

                <!-- Desktop Search Bar (Prominent & Modern) -->
                <form action="{{ route('products.index') }}" method="GET" class="hidden md:flex flex-1 max-w-2xl">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') ?? request('search') }}"
                            placeholder="Cari produk, kategori, atau toko..."
                            class="w-full pl-10 pr-24 py-2.5 bg-slate-50 hover:bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition outline-none"
                        />
                        <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-orange-600 hover:bg-orange-700 text-white font-medium text-xs rounded-lg transition focus:ring-2 focus:ring-orange-500 shadow-2xs">
                            Cari
                        </button>
                    </div>
                </form>

                <!-- Navigation Actions -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Katalog Link -->
                    <a href="{{ route('products.index') }}" class="hidden lg:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-orange-600 transition px-3 py-2 rounded-xl hover:bg-slate-100">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Katalog
                    </a>

                    <!-- Shopping Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="relative text-slate-700 hover:text-orange-600 p-2 rounded-xl hover:bg-slate-100 transition" title="Keranjang Belanja">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @auth
                            @php
                                $cartCount = auth()->user()->cart?->totalQuantity() ?? 0;
                            @endphp
                            @if ($cartCount > 0)
                                <span class="absolute top-1 right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold leading-none text-white bg-orange-600 rounded-full shadow-2xs">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        @endauth
                    </a>

                    @auth
                        <!-- Role-Specific Action Button (Admin / Seller Only) -->
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-300 hover:bg-slate-200 transition">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-700"></span>
                                Admin Panel
                            </a>
                        @elseif (auth()->user()->isSeller())
                            <a href="{{ route('seller.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition">
                                <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>
                                Seller Center
                            </a>
                        @endif

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-none focus:ring-2 focus:ring-orange-500/20">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-orange-600 text-white font-bold flex items-center justify-center text-xs sm:text-sm shadow-2xs">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden xl:flex flex-col text-left">
                                    <span class="text-xs font-bold text-slate-800 truncate max-w-[120px]">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-medium">
                                        {{ auth()->user()->role->label() }}
                                    </span>
                                </div>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-out duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-elevated border border-slate-200/80 py-1.5 z-50 divide-y divide-slate-100"
                                style="display: none;"
                            >
                                <div class="px-4 py-3">
                                    <p class="text-[11px] text-slate-400 font-medium">Masuk sebagai</p>
                                    <p class="text-xs font-bold text-slate-800 truncate mt-0.5">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                    <div class="mt-2">
                                        <x-badge :status="auth()->user()->role->value" size="sm" :label="auth()->user()->role->label()" />
                                    </div>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('orders.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Pesanan Saya
                                    </a>
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        Dashboard Akun
                                    </a>
                                    <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Pengaturan Profil
                                    </a>
                                </div>

                                <div class="py-1">
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-orange-700 transition">
                                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            Admin Dashboard
                                        </a>
                                        <a href="{{ route('admin.shops.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-orange-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            Admin Shop Approval
                                        </a>
                                    @elseif (auth()->user()->isSeller())
                                        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-orange-700 hover:bg-orange-50 transition">
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            Seller Center
                                        </a>
                                        <a href="{{ route('seller.products.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            Kelola Produk Toko
                                        </a>
                                        <a href="{{ route('seller.orders.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            Pesanan Masuk
                                        </a>
                                    @else
                                        <a href="{{ route('buyer.shop.apply') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-orange-700 hover:bg-orange-50 transition">
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            Buka Toko Gratis
                                        </a>
                                    @endif
                                </div>

                                <div class="py-1">
                                    <button 
                                        type="button" 
                                        @click="logoutModalOpen = true; profileOpen = false" 
                                        class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar (Logout)
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Guest State (Visible on Tablet & Desktop; Mobile users access via drawer) -->
                        <div class="hidden sm:flex items-center space-x-2 sm:space-x-3">
                            <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:text-orange-600 transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-semibold text-white bg-orange-600 hover:bg-orange-700 rounded-xl transition shadow-2xs">
                                Daftar
                            </a>
                        </div>
                    @endauth

                    <!-- Mobile Hamburger Button -->
                    <button
                        type="button"
                        @click.stop="mobileMenuOpen = !mobileMenuOpen"
                        :aria-expanded="mobileMenuOpen.toString()"
                        aria-controls="mobile-storefront-drawer"
                        aria-label="Toggle Navigation Menu"
                        class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none focus:ring-2 focus:ring-orange-500"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search Bar (Visible on small screens) -->
            <div class="md:hidden pb-3">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') ?? request('search') }}"
                            placeholder="Cari di NexaMarket..."
                            class="w-full pl-9 pr-16 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition outline-none"
                        />
                        <button type="submit" class="absolute right-1 top-1 bottom-1 px-3 bg-orange-600 text-white font-medium text-xs rounded-lg">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Secondary Sub-Navbar (Shopcart Style) -->
        <div class="hidden lg:block border-t border-slate-100 bg-white text-xs text-slate-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-11">
                <div class="flex items-center gap-6">
                    <!-- Categories Dropdown Trigger -->
                    <div class="relative" x-data="{ catOpen: false }" @click.outside="catOpen = false" @keydown.escape.window="catOpen = false">
                        <button
                            type="button"
                            @click="catOpen = !catOpen"
                            :aria-expanded="catOpen.toString()"
                            aria-haspopup="true"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-orange-50 text-orange-700 hover:bg-orange-100 transition font-bold focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                        >
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            <span>Semua Kategori</span>
                            <svg class="w-3.5 h-3.5 text-orange-600 transition-transform" :class="{ 'rotate-180': catOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            x-show="catOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-out duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-64 bg-white rounded-2xl border border-slate-200 shadow-2xs py-2 z-50 divide-y divide-slate-100"
                            style="display: none;"
                        >
                            <div class="max-h-72 overflow-y-auto space-y-0.5 pr-1 scrollbar-thin">
                                @foreach($headerCategories as $hCat)
                                    <a
                                        href="{{ route('products.index', ['category' => $hCat->slug]) }}"
                                        class="flex items-center justify-between px-4 py-2 text-xs text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition font-medium"
                                    >
                                        <span>{{ $hCat->name }}</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                            <div class="pt-1.5 mt-1.5 px-4">
                                <a href="{{ route('products.index') }}" class="text-[11px] font-bold text-orange-600 hover:text-orange-700 block py-1">
                                    Lihat Semua Kategori &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Key Shopping Links -->
                    <a href="{{ route('products.index', ['sort' => 'terbaru']) }}" class="hover:text-orange-600 font-semibold transition">
                        Promo &amp; Deals
                    </a>
                    <a href="{{ route('products.index') }}" class="hover:text-orange-600 font-semibold transition">
                        Produk Terbaru
                    </a>
                    <a href="{{ route('products.index') }}" class="hover:text-orange-600 font-semibold transition">
                        Mitra Resmi Terverifikasi
                    </a>
                    <a href="{{ url('/') }}" class="hover:text-orange-600 font-semibold transition">
                        Pengiriman Seluruh Indonesia
                    </a>
                </div>

                <div class="flex items-center gap-5 text-slate-600">
                    @auth
                        @if(!auth()->user()->isSeller() && !auth()->user()->isAdmin())
                            <a href="{{ route('buyer.shop.apply') }}" class="hover:text-orange-600 transition font-medium flex items-center gap-1">
                                <span>Buka Toko Mitra</span>
                            </a>
                            <span class="text-slate-200">|</span>
                        @endif
                    @else
                        <a href="{{ route('buyer.shop.apply') }}" class="hover:text-orange-600 transition font-medium flex items-center gap-1">
                            <span>Buka Toko Mitra Gratis</span>
                        </a>
                        <span class="text-slate-200">|</span>
                    @endauth
                    <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Bantuan Pelanggan 24/7</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Navigation Overlay Backdrop -->
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-out duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileMenuOpen = false"
            class="fixed inset-0 bg-slate-900/40 z-40 md:hidden"
            style="display: none;"
            aria-hidden="true"
        ></div>

        <!-- Mobile Drawer Navigation Overlay Panel -->
        <div
            id="mobile-storefront-drawer"
            data-ui
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-out duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click.outside="mobileMenuOpen = false"
            @click="if ($event.target.closest('a, button')) mobileMenuOpen = false"
            class="absolute top-full left-0 right-0 z-50 md:hidden border-t border-slate-200 bg-white px-4 pt-3.5 pb-6 space-y-3.5 shadow-xl max-h-[calc(100vh-5rem)] overflow-y-auto" {{-- taste-skill-ignore --}}
            style="display: none;"
        >
            @auth
                <!-- User Profile Summary in Mobile Drawer -->
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-orange-600 text-white font-bold flex items-center justify-center text-sm shadow-2xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-xs text-slate-900 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</div>
                        <span class="inline-block mt-0.5 text-[10px] font-semibold text-orange-700 bg-orange-50 px-1.5 py-0.2 rounded border border-orange-200">
                            {{ auth()->user()->role->label() }}
                        </span>
                    </div>
                </div>
            @else
                <!-- Guest Auth CTAs in Mobile Drawer -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Masuk Akun
                    </a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2.5 rounded-xl bg-orange-600 text-white text-xs font-semibold hover:bg-orange-700 transition shadow-2xs">
                        Daftar Baru
                    </a>
                </div>
            @endauth

            <div class="space-y-1 pt-1 border-t border-slate-100">
                <a href="{{ route('products.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-orange-50 hover:text-orange-600 transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Jelajahi Katalog</span>
                    </span>
                    <span class="text-slate-400">&rarr;</span>
                </a>

                <!-- Mobile Drawer Categories Accordion -->
                <div x-data="{ mobileCatOpen: false }" class="rounded-xl overflow-hidden">
                    <button
                        type="button"
                        @click="mobileCatOpen = !mobileCatOpen"
                        :aria-expanded="mobileCatOpen.toString()"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-orange-50 hover:text-orange-600 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                    >
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            <span>Kategori Produk</span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': mobileCatOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="mobileCatOpen" style="display: none;" class="pl-7 pr-3 py-1 space-y-1 max-h-48 overflow-y-auto scrollbar-thin bg-slate-50/70 rounded-xl my-1 border border-slate-100">
                        <a href="{{ route('products.index') }}" class="block py-1.5 text-xs text-orange-600 font-bold hover:underline">
                            Semua Kategori &rarr;
                        </a>
                        @foreach($headerCategories as $mCat)
                            <a href="{{ route('products.index', ['category' => $mCat->slug]) }}" class="block py-1.5 text-xs text-slate-600 hover:text-orange-600 transition font-medium">
                                {{ $mCat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('cart.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-orange-50 hover:text-orange-600 transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Keranjang Belanja</span>
                    </span>
                    @auth
                        @if((auth()->user()->cart?->totalQuantity() ?? 0) > 0)
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full">
                                {{ auth()->user()->cart->totalQuantity() }} item
                            </span>
                        @endif
                    @endauth
                </a>

                @auth
                    <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-orange-50 hover:text-orange-600 transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Pesanan Saya</span>
                        </span>
                        <span class="text-slate-400">&rarr;</span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-orange-50 hover:text-orange-600 transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Dashboard Akun</span>
                        </span>
                        <span class="text-slate-400">&rarr;</span>
                    </a>

                    @if(auth()->user()->isSeller())
                        <a href="{{ route('seller.dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-orange-700 bg-orange-50/60 hover:bg-orange-100 transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Seller Center</span>
                            </span>
                            <span class="text-orange-600">&rarr;</span>
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 bg-slate-100 hover:bg-slate-200 transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Admin Dashboard</span>
                            </span>
                            <span class="text-slate-600">&rarr;</span>
                        </a>
                    @else
                        <a href="{{ route('buyer.shop.apply') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-orange-700 bg-orange-50/60 hover:bg-orange-100 transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Buka Toko Mitra Gratis</span>
                            </span>
                            <span class="text-orange-600">&rarr;</span>
                        </a>
                    @endif

                    <div class="pt-2">
                        <button 
                            type="button" 
                            @click="logoutModalOpen = true; mobileMenuOpen = false" 
                            class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 transition flex items-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Keluar (Logout)</span>
                        </button>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Marketplace Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-12">
                <!-- Col 1: Brand & Tagline -->
                <div class="lg:col-span-2 space-y-4">
                    <x-nexa-logo size="md" :subtitle="true" />
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-sm">
                        Platform marketplace multi-vendor terpercaya di Indonesia. Belanja mudah, hidup lebih baik dengan pengalaman transaksi cepat, produk pilihan, dan pembayaran digital terjamin.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 rounded-lg text-xs font-semibold border border-orange-200">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Midtrans Snap Terverifikasi
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold border border-slate-200">
                            Multi-Vendor Ready
                        </span>
                    </div>
                </div>

                <!-- Col 2: Belanja & Katalog -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-900">Jelajahi Belanja</h4>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li><a href="{{ route('products.index') }}" class="hover:text-orange-600 transition">Semua Produk</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'terbaru']) }}" class="hover:text-orange-600 transition">Produk Terbaru</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'termurah']) }}" class="hover:text-orange-600 transition">Harga Termurah</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-orange-600 transition">Keranjang Belanja</a></li>
                    </ul>
                </div>

                <!-- Col 3: Kemitraan & Penjual -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-900">Kemitraan Penjual</h4>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li><a href="{{ route('buyer.shop.apply') }}" class="hover:text-orange-600 transition">Buka Toko Resmi</a></li>
                        @auth
                            @if(auth()->user()->isSeller())
                                <li><a href="{{ route('seller.dashboard') }}" class="hover:text-orange-600 transition">Seller Center</a></li>
                                <li><a href="{{ route('seller.products.index') }}" class="hover:text-orange-600 transition">Kelola Produk</a></li>
                            @endif
                        @endauth
                        <li><a href="{{ route('orders.index') }}" class="hover:text-orange-600 transition">Pelacakan Pesanan</a></li>
                    </ul>
                </div>

                <!-- Col 4: Keamanan & Bantuan -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-900">Keamanan & Dukungan</h4>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li><span class="text-slate-500">Enkripsi Pembayaran TLS 1.3</span></li>
                        <li><span class="text-slate-500">Firebase Token Security</span></li>
                        <li><span class="text-slate-500">Midtrans Sandbox Gateway</span></li>
                        <li><span class="text-slate-500">Bantuan Pelanggan 24/7</span></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} NexaMarket. Hak Cipta Dilindungi Undang-Undang. Belanja Mudah, Hidup Lebih Baik.</p>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('products.index') }}" class="hover:text-orange-600 transition">Katalog</a>
                    <a href="{{ route('cart.index') }}" class="hover:text-orange-600 transition">Keranjang</a>
                    @guest
                        <a href="{{ route('login') }}" class="hover:text-orange-600 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="hover:text-orange-600 transition">Daftar</a>
                    @endguest
                </div>
            </div>
        </div>
    </footer>

    <!-- Logout Confirmation Modal -->
    <div 
        x-show="logoutModalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="logout-modal-title" 
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
                        <h3 class="text-sm font-bold text-slate-900" id="logout-modal-title">Konfirmasi Keluar</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Sesi akun belanja Anda akan diakhiri.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 mt-3.5 leading-relaxed">
                    Apakah Anda yakin ingin keluar dari NexaMarket? Anda dapat masuk kembali kapan saja untuk melanjutkan belanja.
                </p>

                <div class="mt-6 flex items-center justify-end gap-2.5">
                    <button 
                        type="button" 
                        @click="logoutModalOpen = false" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer shadow-xs"
                        >
                            Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
