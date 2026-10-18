<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'NexaMarket') }} — Multi-Vendor Marketplace</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-full flex flex-col">
    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white text-xs py-1.5 px-4 sm:px-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <p class="font-medium truncate">
                Selamat Datang di NexaMarket — Belanja Aman, Cepat & Terpercaya dari Ribuan Toko Terbaik.
            </p>
            <div class="hidden md:flex items-center space-x-6 text-emerald-100">
                <span class="hover:text-white cursor-pointer transition">Bantuan & FAQ</span>
                <span class="hover:text-white cursor-pointer transition">Garansi NexaCare</span>
                <span class="font-semibold text-white">IDR (Rp)</span>
            </div>
        </div>
    </div>

    <!-- Main Marketplace Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 transition-all shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4 md:gap-8">
                <!-- Brand Logo -->
                <div class="shrink-0 flex items-center space-x-3">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-extrabold text-xl shadow-md group-hover:scale-105 transition transform">
                            N
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black tracking-tight text-gray-900 group-hover:text-emerald-600 transition">
                                Nexa<span class="text-emerald-600">Market</span>
                            </span>
                            <span class="text-[10px] uppercase font-bold tracking-widest text-gray-400 -mt-1">
                                Verified Marketplace
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Search Input Simulation (Catalog ready) -->
                <div class="hidden lg:flex flex-1 max-w-2xl">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="search"
                            placeholder="Cari jutaan produk berkualitas, kategori, atau toko..."
                            class="w-full pl-11 pr-24 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition outline-none"
                        />
                        <button class="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                            Cari
                        </button>
                    </div>
                </div>

                <!-- Navigation Actions -->
                <div class="flex items-center space-x-3 sm:space-x-5">
                    @auth
                        <!-- Role-Specific Action Links -->
                        @if (auth()->user()->isAdmin())
                            <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                Portal Admin
                            </span>
                        @elseif (auth()->user()->isSeller())
                            <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Seller Center
                            </span>
                        @else
                            <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                                Toko Saya
                            </a>
                        @endif

                        <!-- Cart Button Simulation -->
                        <div class="relative text-gray-700 hover:text-emerald-600 p-2 cursor-pointer transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-emerald-600 rounded-full">
                                0
                            </span>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-gray-100 transition focus:outline-none">
                                <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden md:flex flex-col text-left">
                                    <span class="text-xs font-bold text-gray-800 truncate max-w-[120px]">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 capitalize">
                                        {{ auth()->user()->role->label() }}
                                    </span>
                                </div>
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50 divide-y divide-gray-100"
                                style="display: none;"
                            >
                                <div class="px-4 py-2.5">
                                    <p class="text-xs text-gray-500">Masuk sebagai</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 text-[11px] font-semibold px-2 py-0.5 rounded border {{ auth()->user()->role->badgeColor() }}">
                                        {{ auth()->user()->role->label() }}
                                    </span>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition">
                                        Pengaturan Profil
                                    </a>
                                </div>
                                <div class="py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition font-medium">
                                            Keluar (Logout)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Guest State -->
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold text-gray-700 hover:text-emerald-600 transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition transform hover:-translate-y-0.5">
                                Daftar Akun
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages / Notifications -->
    @if (session('status') || session('success') || session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if (session('status') || session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
                    <span>{{ session('status') ?? session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Professional Marketplace Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16 text-gray-600 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 uppercase tracking-wider text-[11px]">Tentang NexaMarket</h4>
                    <ul class="space-y-2 text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600 transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Karir & Peluang</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Berita & Pengumuman</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Pusat Bantuan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 uppercase tracking-wider text-[11px]">Beli di NexaMarket</h4>
                    <ul class="space-y-2 text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600 transition">Cara Belanja</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Simulasi Pembayaran</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Lacak Pengiriman</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Garansi & Refund</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 uppercase tracking-wider text-[11px]">Jual di NexaMarket</h4>
                    <ul class="space-y-2 text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600 transition">Pusat Seller Edukasi</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Daftar Menjadi Toko</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Ketentuan Komisi</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Buku Besar & Pencairan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 uppercase tracking-wider text-[11px]">Keamanan & Privasi</h4>
                    <ul class="space-y-2 text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600 transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Penyelesaian Sengketa</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Laporkan Pelanggaran</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-100 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between text-gray-400">
                <p>&copy; {{ date('Y') }} NexaMarket Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
                <p class="mt-2 sm:mt-0 font-medium text-emerald-600">Phase 1: Foundation & Authentication Active</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
