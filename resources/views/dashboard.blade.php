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
            <div>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Banner Penjelajahan Katalog -->
            <div class="bg-amber-50/70 border border-orange-200/80 rounded-3xl p-6 sm:p-8 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-4 max-w-xl text-left">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                            Temukan produk yang kamu butuhkan
                        </h2>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Jelajahi ribuan produk berkualitas dari berbagai toko terpercaya di NexaMarket. Belanja lebih mudah, aman, dan nyaman.
                    </p>

                    <div>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Jelajahi Katalog</span>
                        </a>
                    </div>
                </div>

                <div class="shrink-0 w-full md:w-80 lg:w-96 rounded-2xl overflow-hidden border border-orange-200/60 shadow-xs bg-white/70">
                    <img src="{{ asset('images/dashboard-banner-products.jpg') }}" alt="Produk Pilihan NexaMarket" class="w-full h-44 sm:h-52 object-cover object-center transform hover:scale-102 transition duration-300">
                </div>
            </div>

            <!-- Ringkasan Pesanan (4 Kartu) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Kartu 1: Pesanan Saya -->
                <a href="{{ route('orders.index') }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-orange-300 transition duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-orange-600 transition">Pesanan Saya</h3>
                                <p class="text-[11px] text-slate-400">Total pesanan yang telah kamu buat</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-baseline justify-between">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $totalOrdersCount }}</span>
                        <span class="text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                    </div>
                </a>

                <!-- Kartu 2: Menunggu Pembayaran -->
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-orange-300 transition duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-orange-600 transition">Menunggu Pembayaran</h3>
                                <p class="text-[11px] text-slate-400">Pesanan yang perlu dibayar</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-baseline justify-between">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $pendingOrdersCount }}</span>
                        <span class="text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                    </div>
                </a>

                <!-- Kartu 3: Dalam Pengiriman -->
                <a href="{{ route('orders.index', ['status' => 'shipping']) }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-orange-300 transition duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-orange-600 transition">Dalam Pengiriman</h3>
                                <p class="text-[11px] text-slate-400">Pesanan yang sedang dikirim</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-baseline justify-between">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $shippingOrdersCount }}</span>
                        <span class="text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                    </div>
                </a>

                <!-- Kartu 4: Pesanan Selesai -->
                <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-orange-300 transition duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-orange-600 transition">Pesanan Selesai</h3>
                                <p class="text-[11px] text-slate-400">Pesanan yang sudah diterima</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-baseline justify-between">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $completedOrdersCount }}</span>
                        <span class="text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition text-sm font-bold">&rarr;</span>
                    </div>
                </a>
            </div>

            <!-- Two Columns: Aktivitas Pesanan & Akses Cepat -->
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

        </div>
    </div>
</x-app-layout>
