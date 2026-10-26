<!-- Admin Topbar Component -->
<header class="sticky top-0 z-30 bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 select-none">
    <!-- Left: Mobile Toggle & Breadcrumbs -->
    <div class="flex items-center gap-3">
        <!-- Mobile Drawer Hamburger Button -->
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            type="button"
            class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition focus:outline-none"
            aria-label="Toggle navigasi admin"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Dynamic Breadcrumb -->
        <nav class="flex items-center text-xs font-medium" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-900 transition">
                Admin Center
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="text-slate-900 font-semibold truncate">
                @if(request()->routeIs('admin.dashboard'))
                    Ringkasan
                @elseif(request()->routeIs('admin.shops.*'))
                    Persetujuan Toko
                @elseif(request()->routeIs('admin.categories.*'))
                    Kategori Produk
                @elseif(request()->routeIs('admin.users.*'))
                    Pengguna
                @elseif(request()->routeIs('admin.products.*'))
                    Produk
                @elseif(request()->routeIs('admin.orders.*'))
                    Pesanan
                @elseif(request()->routeIs('admin.payments.*'))
                    Pembayaran
                @elseif(request()->routeIs('admin.refunds.*'))
                    Refund
                @elseif(request()->routeIs('admin.reports.*'))
                    Laporan
                @elseif(request()->routeIs('admin.profile*'))
                    Profil Akun
                @else
                    Administrasi
                @endif
            </span>
        </nav>
    </div>

    <!-- Right: Search, Storefront Jump, Profile Dropdown -->
    <div class="flex items-center gap-3 sm:gap-4">
        <!-- Quick Search Form -->
        <form action="{{ route('admin.products.index') }}" method="GET" class="hidden md:block relative w-48 sm:w-60 lg:w-80">
            <input 
                type="search" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Cari data, produk, pesanan..." 
                class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 rounded-xl focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-slate-900 placeholder:text-slate-400 transition"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </form>

        <!-- Storefront Shortcut Button -->
        <a 
            href="{{ route('products.index') }}" 
            target="_blank"
            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 transition"
            title="Buka etalase belanja publik"
        >
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Storefront</span>
        </a>

        <div x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false" class="relative">
            <button 
                @click.stop="userMenuOpen = !userMenuOpen" 
                type="button"
                class="flex items-center gap-2 p-1 sm:px-2 sm:py-1 rounded-xl hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                :aria-expanded="userMenuOpen"
            >
                <div class="w-8 h-8 rounded-full bg-orange-600 text-white font-black flex items-center justify-center text-xs shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="hidden sm:inline text-xs font-bold text-slate-800">
                    {{ auth()->user()->name ?? 'Administrator' }}
                </span>
                <svg class="w-3.5 h-3.5 text-slate-400 transition" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="userMenuOpen" 
                x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-out duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-lg border border-slate-200 py-1.5 z-50 text-xs" {{-- taste-skill-ignore --}}
            >
                <div class="px-4 py-2 border-b border-slate-100">
                    <p class="font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1 px-1.5 py-0.2 rounded text-[10px] font-bold bg-orange-50 text-orange-600">
                        Administrator
                    </span>
                </div>

                <a href="{{ route('admin.profile') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-orange-600 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Akun
                </a>

                <div class="border-t border-slate-100 mt-1 pt-1">
                    <button 
                        type="button" 
                        @click="logoutModalOpen = true; userMenuOpen = false" 
                        class="w-full flex items-center gap-2 px-4 py-2 text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer"
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
</header>
