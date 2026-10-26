<!-- Admin Sidebar Component -->
<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed top-0 bottom-0 left-0 w-64 bg-white border-r border-slate-200 z-50 flex flex-col transition-transform duration-200 ease-in-out select-none"
    aria-label="Admin Navigation"
>
    <!-- Brand & Workspace Header -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-slate-100 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col group">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-600 text-white flex items-center justify-center p-1.5 shadow-xs transition group-hover:bg-orange-700">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <path d="M16 10a4 4 0 01-8 0" />
                    </svg>
                </div>
                <span class="text-lg font-black tracking-tight text-slate-900 leading-none">
                    Nexa<span class="text-orange-600">Market</span>
                </span>
            </div>
            <div class="mt-1.5 pl-10.5">
                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-200/60 leading-tight">
                    Admin Center
                </span>
            </div>
        </a>

        <!-- Mobile Drawer Close Button -->
        <button 
            @click="sidebarOpen = false"
            class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
            aria-label="Tutup navigasi"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Scrollable Navigation Links -->
    <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-6">
        <!-- Group: OVERVIEW -->
        <div class="space-y-1">
            <a 
                href="{{ route('admin.dashboard') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Ringkasan</span>
            </a>
        </div>

        <!-- Group: MANAJEMEN PLATFORM -->
        <div class="space-y-1">
            <div class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">
                Manajemen Platform
            </div>

            <!-- Persetujuan Toko -->
            <a 
                href="{{ route('admin.shops.index') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.shops.*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.shops.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Persetujuan Toko</span>
            </a>

            <!-- Kategori Produk -->
            <a 
                href="{{ route('admin.categories.index') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.categories.*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.categories.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V5a2 2 0 012-2z" />
                </svg>
                <span>Kategori Produk</span>
            </a>

            <!-- Pengguna -->
            <a 
                href="{{ route('admin.users.index') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.users.*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Pengguna</span>
            </a>

            <!-- Produk -->
            <a 
                href="{{ route('admin.products.index') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.products.*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.products.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Produk</span>
            </a>

            <!-- Pesanan -->
            <a 
                href="{{ route('admin.orders.index') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.orders.*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.orders.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Pesanan</span>
            </a>

            <!-- Pembayaran -->
            <a 
                href="{{ route('admin.payments.index') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.payments.*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.payments.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Pembayaran</span>
            </a>

            <!-- Refund -->
            <a 
                href="{{ route('admin.refunds.index') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.refunds.*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.refunds.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Refund</span>
            </a>
        </div>

        <!-- Group: ANALISIS -->
        <div class="space-y-1">
            <div class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">
                Analisis
            </div>

            <!-- Laporan -->
            <a 
                href="{{ route('admin.reports.index') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.reports.*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Laporan</span>
            </a>
        </div>

        <!-- Group: PENGATURAN -->
        <div class="space-y-1">
            <div class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">
                Pengaturan
            </div>

            <!-- Profil Akun -->
            <a 
                href="{{ route('admin.profile') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition relative group {{ request()->routeIs('admin.profile*') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.profile*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Profil Akun</span>
            </a>
        </div>
    </nav>

    <!-- Bottom Administrator Profile Footer -->
    <div class="p-3 border-t border-slate-200 shrink-0">
        <div x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false" class="relative">
            <button 
                @click.stop="userMenuOpen = !userMenuOpen"
                type="button"
                class="w-full flex items-center justify-between p-2 rounded-xl hover:bg-slate-100 transition group focus:outline-none cursor-pointer"
                :aria-expanded="userMenuOpen"
            >
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-orange-600 text-white font-black flex items-center justify-center text-xs shadow-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="text-left min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate leading-tight group-hover:text-slate-900">
                            {{ auth()->user()->name ?? 'Administrator' }}
                        </p>
                        <p class="text-[10px] font-medium text-slate-400 truncate mt-0.5 leading-none">
                            Admin
                        </p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 shrink-0 transition" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Bottom Profile Dropdown Popover -->
            <div 
                x-show="userMenuOpen" 
                x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-out duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 text-xs" {{-- taste-skill-ignore --}}
            >
                <div class="px-4 py-2 border-b border-slate-100">
                    <p class="font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                </div>

                <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-orange-600 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profil Akun</span>
                </a>

                <a href="{{ route('products.index') }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-orange-600 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>Storefront Publik</span>
                </a>

                <div class="border-t border-slate-100 mt-1 pt-1">
                    <button 
                        type="button" 
                        @click="logoutModalOpen = true; userMenuOpen = false" 
                        class="w-full flex items-center gap-2.5 px-4 py-2 text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar (Logout)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</aside>
