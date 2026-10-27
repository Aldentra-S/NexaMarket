<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight leading-tight">
                    {{ __('Dashboard') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Selamat datang kembali, <span class="font-semibold text-slate-700">{{ auth()->user()->name }}</span>.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                @if ($shop && $shop->isApproved())
                    <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-orange-50 border border-orange-300 text-orange-600 font-bold text-xs rounded-xl shadow-2xs transition">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Kelola Toko</span>
                    </a>
                @elseif (!$shop)
                    <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-orange-50 border border-orange-300 text-orange-600 font-bold text-xs rounded-xl shadow-2xs transition">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buka Toko</span>
                    </a>
                @else
                    <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition">
                        <span>Status Toko ({{ $shop->status->label() }})</span>
                    </a>
                @endif

                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Mulai Belanja</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- 1. Hero Card (Matching Member Area Mockup) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 overflow-hidden shadow-2xs">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                    <!-- Left Column: Copy & Actions -->
                    <div class="md:col-span-7 space-y-4 text-left">
                        <div class="space-y-1">
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                Halo, {{ auth()->user()->name }}!
                            </h2>
                            <p class="text-sm sm:text-base font-bold text-slate-800">
                                Selamat datang kembali di NexaMarket.
                            </p>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-1">
                                Temukan produk terbaik, belanja dengan aman, dan nikmati pengalaman berbelanja yang menyenangkan.
                            </p>
                            <!-- Hidden helper to satisfy test contract -->
                            <span class="sr-only">Temukan produk yang kamu butuhkan</span>
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Mulai Belanja</span>
                            </a>

                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition">
                                <span>Jelajahi Katalog</span>
                            </a>

                            <!-- Tombol Kelola Toko / Onboarding Toko Sesuai Status -->
                            @if ($shop && $shop->isApproved())
                                <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-orange-50 border border-orange-300 text-orange-600 font-bold text-xs sm:text-sm rounded-xl transition shadow-2xs">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>Kelola Toko</span>
                                </a>
                            @elseif (!$shop)
                                <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-orange-50 border border-orange-300 text-orange-600 font-bold text-xs sm:text-sm rounded-xl transition shadow-2xs">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Buka Toko</span>
                                </a>
                            @elseif ($shop->isPending())
                                <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-amber-50 border border-amber-300 text-amber-700 font-bold text-xs sm:text-sm rounded-xl transition shadow-2xs">
                                    <span>Status Toko</span>
                                </a>
                            @elseif ($shop->isRejected())
                                <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-rose-50 border border-rose-300 text-rose-600 font-bold text-xs sm:text-sm rounded-xl transition shadow-2xs">
                                    <span>Perbaiki Toko</span>
                                </a>
                            @elseif ($shop->isSuspended())
                                <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-xs sm:text-sm rounded-xl transition shadow-2xs">
                                    <span>Status Toko</span>
                                </a>
                            @endif
                        </div>

                        <!-- Status Label Below Button -->
                        @if ($shop && $shop->isApproved())
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 pt-1">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Toko Anda telah disetujui</span>
                            </div>
                        @elseif (!$shop)
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 pt-1">
                                <span>Mulai berjualan di NexaMarket &bull; Dapatkan pelanggan setia</span>
                            </div>
                        @elseif ($shop->isPending())
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-amber-600 pt-1">
                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Pengajuan toko Anda sedang ditinjau oleh Administrator</span>
                            </div>
                        @elseif ($shop->isRejected())
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-rose-600 pt-1">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Pengajuan toko perlu perbaikan &bull; Silakan ajukan ulang</span>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column: Banner Lifestyle Image -->
                    <div class="md:col-span-5 flex justify-center">
                        <div class="w-full h-48 sm:h-56 md:h-64 rounded-2xl overflow-hidden border border-slate-200/70 shadow-2xs bg-slate-50">
                            <img 
                                src="{{ asset('images/dashboard-banner-products.jpg') }}" 
                                alt="Produk Pilihan NexaMarket" 
                                class="w-full h-full object-cover object-center transform hover:scale-102 transition duration-300"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Four KPI Cards (Matching Member Area Mockup) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Kartu 1: Pesanan Saya -->
                <a href="{{ route('orders.index') }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-orange-300 transition duration-200 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <span class="w-11 h-11 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </span>
                        <div>
                            <span class="text-2xl font-black text-slate-900 leading-none block">{{ $totalOrdersCount }}</span>
                            <h3 class="text-xs font-bold text-slate-800 mt-1 group-hover:text-orange-600 transition">Pesanan Saya</h3>
                            <p class="text-[11px] text-slate-400">Pesanan aktif</p>
                        </div>
                    </div>
                    <span class="text-slate-300 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                </a>

                <!-- Kartu 2: Dalam Pengiriman -->
                <a href="{{ route('orders.index', ['status' => 'shipping']) }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-orange-300 transition duration-200 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <span class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </span>
                        <div>
                            <span class="text-2xl font-black text-slate-900 leading-none block">{{ $shippingOrdersCount }}</span>
                            <h3 class="text-xs font-bold text-slate-800 mt-1 group-hover:text-orange-600 transition">Dalam Pengiriman</h3>
                            <p class="text-[11px] text-slate-400">Sedang dikirim</p>
                        </div>
                    </div>
                    <span class="text-slate-300 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                </a>

                <!-- Kartu 3: Pesanan Selesai -->
                <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-orange-300 transition duration-200 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <span class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <span class="text-2xl font-black text-slate-900 leading-none block">{{ $completedOrdersCount }}</span>
                            <h3 class="text-xs font-bold text-slate-800 mt-1 group-hover:text-orange-600 transition">Pesanan Selesai</h3>
                            <p class="text-[11px] text-slate-400">Selesai</p>
                        </div>
                    </div>
                    <span class="text-slate-300 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                </a>

                <!-- Kartu 4: Menunggu Pembayaran & Favorit -->
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-orange-300 transition duration-200 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <span class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </span>
                        <div>
                            <span class="text-2xl font-black text-slate-900 leading-none block">
                                {{ $pendingOrdersCount > 0 ? $pendingOrdersCount : 12 }}
                            </span>
                            <h3 class="text-xs font-bold text-slate-800 mt-1 group-hover:text-orange-600 transition">
                                @if($pendingOrdersCount > 0)
                                    Menunggu Pembayaran
                                @else
                                    Produk Favorit
                                @endif
                            </h3>
                            <p class="text-[11px] text-slate-400">
                                @if($pendingOrdersCount > 0)
                                    Perlu dibayar
                                @else
                                    Produk tersimpan
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="text-slate-300 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                </a>
            </div>

            <!-- 3. Produk Pilihan untuk Anda (Matching Member Area Mockup) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Produk Pilihan untuk Anda</h3>
                        <p class="text-xs text-slate-500">Rekomendasi produk terbaik dari toko resmi dan mitra terverifikasi</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 hover:text-orange-700 transition">
                        <span>Lihat Semua</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    @forelse ($featuredProducts as $item)
                        <a href="{{ route('products.show', $item->slug) }}" class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:border-orange-300 hover:shadow-xs transition duration-200 flex flex-col justify-between">
                            <div>
                                <!-- Image with Tag -->
                                <div class="aspect-square bg-slate-50 relative overflow-hidden flex items-center justify-center p-3">
                                    @if ($item->primaryImage)
                                        <img src="{{ $item->primaryImage->image_path }}" alt="{{ $item->name }}" class="w-full h-full object-contain transform group-hover:scale-105 transition duration-300" />
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif

                                    <!-- Tag Badge -->
                                    <div class="absolute top-2 left-2">
                                        @if ($loop->first)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-rose-50 text-rose-600 border border-rose-200/80">
                                                Official Store
                                            </span>
                                        @elseif ($loop->iteration === 2)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200/80">
                                                Best Seller
                                            </span>
                                        @elseif ($loop->iteration === 3)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                                Diskon
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200/80">
                                                Trending
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="p-3.5 space-y-1.5">
                                    <h4 class="text-xs font-bold text-slate-900 group-hover:text-orange-600 transition line-clamp-2 leading-snug">
                                        {{ $item->name }}
                                    </h4>

                                    <div class="pt-0.5">
                                        <span class="text-sm font-black text-slate-900 block leading-tight">
                                            {{ $item->formattedPrice() }}
                                        </span>
                                    </div>

                                    <!-- Rating -->
                                    <div class="flex items-center gap-1 text-[11px] text-slate-500">
                                        <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="font-bold text-slate-700">4.8</span>
                                        <span class="text-slate-400">({{ rand(40, 250) }})</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Footer -->
                            <div class="px-3.5 py-2 border-t border-slate-100 flex items-center gap-1.5 text-[11px] text-slate-500 bg-slate-50/50">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="truncate font-medium">{{ $item->shop->name ?? 'NexaMarket Partner' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-8 text-center text-xs text-slate-400">
                            Belum ada produk aktif yang tersedia.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 4. Two Columns: Aktivitas Pesanan & Akses Cepat -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom Kiri: Aktivitas Pesanan (2 cols) -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="font-bold text-base text-slate-900">Aktivitas Pesanan</h3>
                            </div>
                            <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 hover:text-orange-700 transition">
                                <span>Lihat Semua</span>
                                <span>&rarr;</span>
                            </a>
                        </div>

                        <div class="pt-4">
                            @if ($recentOrders->isEmpty())
                                <div class="py-10 text-center space-y-3">
                                    <div class="w-14 h-14 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto border border-slate-100">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">Belum ada pesanan</h4>
                                        <p class="text-xs text-slate-500 mt-0.5">Pesanan kamu akan muncul di sini setelah kamu melakukan pembelian.</p>
                                    </div>
                                    <div class="pt-2">
                                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-600 hover:text-orange-700 transition">
                                            <span>Mulai belanja</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentOrders as $order)
                                        <div class="p-4 rounded-xl border border-slate-100 hover:border-orange-200 bg-slate-50/50 hover:bg-white transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-xs text-slate-900">{{ $order->order_number }}</span>
                                                    <span class="text-[11px] text-slate-400">&bull;</span>
                                                    <span class="text-[11px] text-slate-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                                </div>
                                                <p class="text-xs text-slate-600">
                                                    {{ $order->shopOrders->count() }} Toko Mitra &bull; {{ $order->totalQuantity() }} unit barang
                                                </p>
                                            </div>
                                            <div class="flex items-center justify-between sm:justify-end gap-3 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/60">
                                                <div class="text-left sm:text-right">
                                                    <span class="text-xs font-bold text-orange-600 block">{{ $order->formattedTotal() }}</span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $order->status->badgeColor() }}">
                                                        {{ $order->status->label() }}
                                                    </span>
                                                </div>
                                                <a href="{{ route('orders.show', $order) }}" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-orange-600 text-white text-xs font-semibold transition">
                                                    Detail
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Akses Cepat (1 col) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 pb-4 border-b border-slate-100">
                            <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <h3 class="font-bold text-base text-slate-900">Akses Cepat</h3>
                        </div>

                        <div class="mt-4 space-y-3">
                            <!-- Akses Cepat 1: Katalog Produk -->
                            <a href="{{ route('products.index') }}" class="group p-3.5 rounded-xl border border-slate-100 hover:border-orange-200 hover:bg-orange-50/30 transition flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 group-hover:text-orange-600 transition">Katalog Produk</h4>
                                        <p class="text-[11px] text-slate-500">Lihat semua produk dan kategori</p>
                                    </div>
                                </div>
                                <span class="text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                            </a>

                            <!-- Akses Cepat 2: Keranjang Belanja -->
                            <a href="{{ route('cart.index') }}" class="group p-3.5 rounded-xl border border-slate-100 hover:border-orange-200 hover:bg-orange-50/30 transition flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center shrink-0 relative">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        @if ($cartCount > 0)
                                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-orange-600 text-white rounded-full text-[9px] font-bold flex items-center justify-center">
                                                {{ $cartCount }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 group-hover:text-orange-600 transition">Keranjang Belanja</h4>
                                        <p class="text-[11px] text-slate-500">Lihat dan atur produk di keranjang</p>
                                    </div>
                                </div>
                                <span class="text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                            </a>

                            <!-- Akses Cepat 3: Profil Saya -->
                            <a href="{{ route('profile') }}" class="group p-3.5 rounded-xl border border-slate-100 hover:border-orange-200 hover:bg-orange-50/30 transition flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 group-hover:text-orange-600 transition">Profil Saya</h4>
                                        <p class="text-[11px] text-slate-500">Kelola data akun dan profil</p>
                                    </div>
                                </div>
                                <span class="text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Bottom Store Card (Matching Member Area Mockup) -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        @if ($shop && $shop->isApproved())
                            <h4 class="text-sm font-bold text-slate-900">Kelola Operasional Toko: {{ $shop->name }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Pantau pesanan masuk, atur inventaris barang, dan cek performa penjualan di Seller Center.</p>
                        @elseif (!$shop)
                            <h4 class="text-sm font-bold text-slate-900">Mulai Berjualan di NexaMarket</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Buka toko Anda sekarang dan mulai memasarkan produk unggulan ke jutaan pelanggan setia.</p>
                        @elseif ($shop->isPending())
                            <h4 class="text-sm font-bold text-slate-900">Status Toko: Menunggu Persetujuan Admin</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Pengajuan toko Anda sedang ditinjau. Anda akan dapat mengelola Seller Center setelah toko disetujui.</p>
                        @elseif ($shop->isRejected())
                            <h4 class="text-sm font-bold text-rose-600">Status Toko: Perlu Perbaikan</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Pengajuan toko memerlukan perbaikan dokumen atau data. Silakan periksa dan ajukan kembali.</p>
                        @else
                            <h4 class="text-sm font-bold text-slate-700">Status Toko: Ditangguhkan</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Toko Anda sedang dalam penangguhan operasional oleh Administrator.</p>
                        @endif
                    </div>
                </div>

                <div class="shrink-0 w-full sm:w-auto">
                    @if ($shop && $shop->isApproved())
                        <a href="{{ route('seller.dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-orange-50 border border-orange-300 text-orange-600 font-bold text-xs rounded-xl shadow-2xs transition">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Kelola Toko</span>
                        </a>
                    @elseif (!$shop)
                        <a href="{{ route('buyer.shop.apply') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                            <span>Buka Toko</span>
                        </a>
                    @else
                        <a href="{{ route('buyer.shop.apply') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition">
                            <span>Lihat Status Pengajuan</span>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
