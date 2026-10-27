<!-- Seller Center Topbar Component -->
<header class="sticky top-0 z-30 bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 select-none">
    <!-- Left: Mobile Toggle & Store Information -->
    <div class="flex items-center gap-3">
        <!-- Mobile Drawer Hamburger Button -->
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            type="button"
            class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition focus:outline-none"
            aria-label="Toggle navigasi seller center"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Brand Icon on Mobile -->
        <div class="lg:hidden flex items-center gap-2">
            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-1.5">
                <x-nexa-logo size="sm" />
            </a>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                Seller Center
            </span>
        </div>

        <!-- Store Identification (Plain Text on Desktop) -->
        <div class="hidden lg:flex items-center">
            <span class="text-xs font-semibold text-slate-700 truncate max-w-[200px]">
                {{ auth()->user()->shop?->name ?? 'Toko Saya' }}
            </span>
        </div>
    </div>

    <!-- Right: User Profile Dropdown -->
    <div class="flex items-center gap-3 sm:gap-4">

        <!-- Settings Dropdown (Identical to Member Area) -->
        <x-dropdown align="right" width="60">
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
                    <p class="text-[11px] text-slate-400 font-medium">Akun Pemilik Toko</p>
                    <p class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                        {{ auth()->user()->role->label() }}
                    </span>
                </div>

                <!-- Back to Member Area in Dropdown -->
                <div class="p-1.5 border-b border-slate-100">
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200/60 transition group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Dashboard Member Area</span>
                        </span>
                        <span class="text-sky-400 group-hover:translate-x-0.5 transition">&rarr;</span>
                    </a>
                </div>

                <x-dropdown-link :href="route('products.index')">
                    {{ __('Katalog Marketplace') }}
                </x-dropdown-link>

                <x-dropdown-link :href="route('orders.index')">
                    {{ __('Pesanan Belanja Saya') }}
                </x-dropdown-link>

                <x-dropdown-link :href="route('profile')">
                    {{ __('Pengaturan Profil') }}
                </x-dropdown-link>

                <div class="border-t border-slate-100">
                    <button type="button" @click="logoutModalOpen = true" class="w-full text-start cursor-pointer">
                        <x-dropdown-link class="text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-semibold">
                            {{ __('Keluar (Log Out)') }}
                        </x-dropdown-link>
                    </button>
                </div>
            </x-slot>
        </x-dropdown>
    </div>
</header>
