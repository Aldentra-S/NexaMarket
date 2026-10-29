<!-- Seller Center Sidebar Component -->
<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed top-0 bottom-0 left-0 w-64 bg-white border-r border-slate-200 z-50 flex flex-col transition-transform duration-200 ease-in-out select-none"
    aria-label="Seller Center Navigation"
>
    <!-- Brand & Workspace Header -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-slate-100 shrink-0">
        <a href="{{ route('seller.dashboard') }}" class="flex flex-col group focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-lg">
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
                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 leading-tight">
                    Seller Center
                </span>
            </div>
        </a>

        <!-- Mobile Drawer Close Button -->
        <button 
            @click="sidebarOpen = false"
            class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
            aria-label="Tutup navigasi"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Scrollable Navigation Links -->
    <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1">
        <!-- 1. Dashboard -->
        <a 
            href="{{ route('seller.dashboard') }}" 
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition group focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ request()->routeIs('seller.dashboard') ? 'bg-orange-50 text-orange-700 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium' }}"
        >
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('seller.dashboard') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- 2. Produk Saya -->
        <a 
            href="{{ route('seller.products.index') }}" 
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition group focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ request()->routeIs('seller.products.*') ? 'bg-orange-50 text-orange-700 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium' }}"
        >
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('seller.products.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span>Produk Saya</span>
        </a>

        <!-- 3. Pesanan Masuk -->
        <a 
            href="{{ route('seller.orders.index') }}" 
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition group focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ request()->routeIs('seller.orders.*') ? 'bg-orange-50 text-orange-700 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium' }}"
        >
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('seller.orders.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span>Pesanan Masuk</span>
        </a>

        <!-- 4. Ulasan -->
        <a 
            href="{{ route('seller.reviews.index') }}" 
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition group focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ request()->routeIs('seller.reviews.*') ? 'bg-orange-50 text-orange-700 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium' }}"
        >
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('seller.reviews.*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            <span>Ulasan</span>
        </a>

        <!-- 5. Pengaturan Toko -->
        <a 
            href="{{ route('seller.shop.settings') }}" 
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition group focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ request()->routeIs('seller.shop.settings*') ? 'bg-orange-50 text-orange-700 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium' }}"
        >
            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('seller.shop.settings*') ? 'text-orange-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Pengaturan Toko</span>
        </a>
    </nav>

    <!-- Bottom Sidebar: Return to Member Area -->
    <div class="p-3 border-t border-slate-100 shrink-0">
        <a 
            href="{{ route('dashboard') }}" 
            class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-orange-600 hover:bg-orange-50/70 transition group focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
        >
            <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Member Area</span>
        </a>
    </div>
</aside>
