<x-marketplace-layout>
    <x-slot name="title">
        NexaMarket | Belanja Mudah, Hidup Lebih Baik
    </x-slot>

    @php
        $popularCategories = \App\Models\Category::active()
            ->withCount(['products' => function ($q) {
                $q->published()->forActiveShop();
            }])
            ->orderByDesc('products_count')
            ->take(8)
            ->get();

        $featuredProducts = \App\Models\Product::published()
            ->forActiveShop()
            ->with(['shop', 'category', 'primaryImage', 'images'])
            ->latest()
            ->take(8)
            ->get();

        $topDeals = \App\Models\Product::published()
            ->forActiveShop()
            ->with(['shop', 'category', 'primaryImage', 'images'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        $featuredShops = \App\Models\Shop::where('status', \App\Enums\ShopStatus::APPROVED)
            ->withCount(['products' => function ($q) {
                $q->published();
            }])
            ->orderByDesc('products_count')
            ->take(4)
            ->get();

        $spotlightProduct = $featuredProducts->first();
    @endphp

    <div class="space-y-12 sm:space-y-16 pb-16">
        <!-- Hero Section (Shopcart by Musemind Style) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8">
            <div class="relative overflow-hidden rounded-3xl bg-orange-600 text-white p-6 sm:p-10 lg:p-12 border border-orange-500">
                <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left Hero Content -->
                    <div class="lg:col-span-7 space-y-5 text-left">
                        <div class="inline-flex max-w-full items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-[11px] sm:text-xs font-semibold text-white border border-white/20">
                            <span class="w-2 h-2 rounded-full bg-amber-300 shrink-0"></span>
                            <span class="truncate sm:overflow-visible sm:whitespace-normal">Penawaran Terbaik &bull; Belanja Mudah, Hidup Lebih Baik</span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white">
                            Belanja Produk Pilihan dari Ribuan Mitra Resmi
                        </h1>

                        <p class="text-xs sm:text-base text-orange-100 max-w-xl leading-relaxed">
                            Platform marketplace multi-vendor terpercaya di Indonesia. Nikmati ribuan produk berkualitas, kepastian garansi toko mitra, dan perlindungan pembayaran digital Midtrans.
                        </p>

                        <!-- CTA Actions -->
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a href="{{ route('products.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white text-orange-600 hover:bg-orange-50 font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-2xs">
                                <span>Buka Katalog Belanja</span>
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>

                            @auth
                                @if (auth()->user()->isSeller())
                                    <a href="{{ route('seller.dashboard') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs sm:text-sm border border-white/30 transition text-center">
                                        Kelola Toko Saya
                                    </a>
                                @else
                                    <a href="{{ route('buyer.shop.apply') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs sm:text-sm border border-white/30 transition text-center">
                                        Buka Toko Mitra Gratis
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs sm:text-sm border border-white/30 transition text-center">
                                    Daftar Akun Gratis
                                </a>
                            @endauth
                        </div>

                        <!-- Mini Value Highlights -->
                        <div class="pt-4 border-t border-white/20 flex flex-wrap items-center gap-3 sm:gap-6 text-xs text-orange-100">
                            <span class="flex items-center gap-1.5 font-medium">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                100% Toko Mitra Resmi
                            </span>
                            <span class="flex items-center gap-1.5 font-medium">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Midtrans Snap Guaranteed
                            </span>
                            <span class="flex items-center gap-1.5 font-medium">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Pengiriman Cepat
                            </span>
                        </div>
                    </div>

                    <!-- Right Hero Visual Showcase Card (Shopcart Featured Spotlight) -->
                    <div class="lg:col-span-5 hidden lg:block">
                        @if($spotlightProduct)
                            <div class="bg-white rounded-2xl p-5 text-slate-900 border border-white/30 max-w-sm mx-auto shadow-2xs">
                                <div class="relative aspect-square w-full rounded-xl overflow-hidden bg-slate-50 mb-4 border border-slate-100">
                                    <img
                                        src="{{ $spotlightProduct->thumbnail_url }}"
                                        alt="{{ $spotlightProduct->name }}"
                                        class="w-full h-full object-cover"
                                    />
                                    <span class="absolute top-2.5 left-2.5 px-2.5 py-1 bg-orange-600 text-white text-[11px] font-bold rounded-lg shadow-2xs">
                                        Produk Pilihan
                                    </span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500 font-medium truncate max-w-[180px]">{{ $spotlightProduct->shop->name ?? 'Toko Resmi' }}</span>
                                        <span class="text-amber-500 font-bold flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 fill-current text-amber-500" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <span>4.8</span>
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-base line-clamp-1 hover:text-orange-600 transition">
                                        <a href="{{ route('products.show', $spotlightProduct->slug) }}">{{ $spotlightProduct->name }}</a>
                                    </h3>
                                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                        <div>
                                            <span class="text-xs text-orange-600 font-bold">Rp</span>
                                            <span class="text-xl font-black text-orange-600 tracking-tight">{{ number_format($spotlightProduct->price, 0, ',', '.') }}</span>
                                        </div>
                                        <a href="{{ route('products.show', $spotlightProduct->slug) }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-xl transition shadow-2xs">
                                            Beli Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-white/10 rounded-2xl p-8 border border-white/20 text-center space-y-3">
                                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto text-white">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <h3 class="font-bold text-lg text-white">Marketplace Terpadu</h3>
                                <p class="text-xs text-orange-100">Belanja mudah dari ribuan mitra terverifikasi di seluruh Indonesia.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Shop Our Top Categories Section (Shopcart Circular/Pills Category Architecture) -->
        @if ($popularCategories->isNotEmpty())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            Kategori Pilihan Populer
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Jelajahi beragam kategori belanja terfavorit pilihan mitra</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 flex items-center gap-1 group self-start sm:self-auto shrink-0">
                        <span>Lihat Semua Kategori</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 sm:gap-4">
                    @foreach ($popularCategories as $cat)
                        <a
                            href="{{ route('products.index', ['category' => $cat->slug]) }}"
                            class="group bg-white rounded-2xl p-4 border border-slate-200/90 hover:border-orange-500 transition text-center flex flex-col items-center justify-center gap-2.5 h-full shadow-2xs"
                        >
                            <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors flex items-center justify-center shrink-0">
                                @php
                                    $catLower = strtolower($cat->name);
                                @endphp
                                @if(str_contains($catLower, 'elektronik') || str_contains($catLower, 'gadget') || str_contains($catLower, 'tech') || str_contains($catLower, 'laptop'))
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @elseif(str_contains($catLower, 'fashion') || str_contains($catLower, 'baju') || str_contains($catLower, 'pakaian') || str_contains($catLower, 'busana'))
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                @elseif(str_contains($catLower, 'sepatu') || str_contains($catLower, 'sneaker') || str_contains($catLower, 'olahraga'))
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @elseif(str_contains($catLower, 'rumah') || str_contains($catLower, 'furniture') || str_contains($catLower, 'dapur'))
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                @elseif(str_contains($catLower, 'buku') || str_contains($catLower, 'edukasi'))
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-800 group-hover:text-orange-600 transition line-clamp-1">
                                    {{ $cat->name }}
                                </h3>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $cat->products_count }} Produk
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Today's Best Deals Section (Shopcart Signature) -->
        @if ($topDeals->isNotEmpty())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-orange-50 text-orange-700 text-xs font-bold border border-orange-200 mb-1">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Promo Terbatas Hari Ini</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            Penawaran Terbaik Hari Ini
                        </h2>
                    </div>
                    <a href="{{ route('products.index', ['sort' => 'terbaru']) }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 flex items-center gap-1 group self-start sm:self-auto shrink-0">
                        <span>Lihat Semua Promo</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($topDeals as $deal)
                        <x-product-card :product="$deal" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Services To Help You Shop Section (Shopcart Value Proposition Architecture) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-10 space-y-8 shadow-2xs">
                <div class="max-w-xl space-y-1">
                    <span class="text-xs font-bold text-orange-600">Jaminan Transaksi Resmi</span>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Layanan Terpercaya Untuk Anda
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Kami berkomitmen menghadirkan pengalaman belanja online yang aman, cepat, dan transparan bagi setiap pembeli di Indonesia.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Service 1: FAQ & Customer Support -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                        <div class="w-11 h-11 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Bantuan Transaksi 24/7</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Pusat panduan belanja lengkap, kendala pesanan, dan dukungan bantuan ramah yang selalu siap melayani Anda.
                        </p>
                    </div>

                    <!-- Service 2: Midtrans Online Payment -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Pembayaran Digital Terjamin</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Didukung resmi oleh Midtrans Snap dengan enkripsi pembayaran mutakhir via QRIS, Virtual Account, dan E-Wallet.
                        </p>
                    </div>

                    <!-- Service 3: Fast Delivery -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Pengiriman Terpercaya</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Kemitraan pengiriman ekspres terintegrasi dengan pelacakan nomor resi otomatis dari gudang toko mitra.
                        </p>
                    </div>

                    <!-- Service 4: Multi-Vendor Marketplace -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                        <div class="w-11 h-11 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Satu Checkout Terpadu</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Belanja berbagai macam produk dari toko-toko mitra yang berbeda dalam satu keranjang belanja dengan pembayaran terpadu.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recommended & Latest Products Section (Produk Pilihan) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-orange-50 text-orange-700 text-xs font-bold border border-orange-200 mb-1">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Rekomendasi Marketplace</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Produk Pilihan
                    </h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 flex items-center gap-1 group self-start sm:self-auto shrink-0">
                    <span>Lihat Semua Produk</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </a>
            </div>

            @if ($featuredProducts->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($featuredProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 text-center max-w-md mx-auto space-y-3 shadow-2xs">
                    <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Belum Ada Produk Tersedia</h3>
                    <p class="text-xs text-slate-500">Katalog produk saat ini sedang diperbarui oleh mitra toko kami.</p>
                </div>
            @endif
        </section>

        <!-- Choose by Store / Toko Mitra Resmi Terpilih (Shopcart Brand/Store Section) -->
        @if ($featuredShops->isNotEmpty())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            Pilih Berdasarkan Toko Mitra
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kunjungi toko-toko resmi terverifikasi dengan jaminan produk asli</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 flex items-center gap-1 group self-start sm:self-auto shrink-0">
                        <span>Semua Toko Mitra</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($featuredShops as $fShop)
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 space-y-4 hover:border-orange-500 transition-colors shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-orange-600 text-white font-black text-lg flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($fShop->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-900 text-sm truncate">
                                        {{ $fShop->name }}
                                    </h3>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Mitra Terverifikasi
                                    </span>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">{{ $fShop->products_count }} Produk Aktif</span>
                                <a href="{{ route('products.index') }}" class="text-orange-600 font-bold hover:text-orange-700">
                                    Katalog &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Multi-Vendor Ecosystem & Role Explanation Section (Required by Test & Architecture) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-10 space-y-8 shadow-2xs">
                <div class="max-w-2xl space-y-2">
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Ekosistem Marketplace NexaMarket
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600">
                        NexaMarket menghubungkan seluruh ekosistem jual beli online di Indonesia dengan tata kelola hak akses yang aman dan terstruktur.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Peran Pembeli (Buyer) -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Peran Pembeli (Buyer)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Jelajahi berbagai kategori produk unggulan, kelola keranjang belanja multi-vendor, dan bayar pesanan secara aman melalui gateway Midtrans Snap.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700">
                                Mulai Belanja Sekarang &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Peran Penjual (Seller) -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Peran Penjual (Seller)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Daftarkan toko resmi Anda, kelola katalog produk, pantau stok barang, dan proses pemenuhan pesanan masuk melalui Seller Center profesional.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('buyer.shop.apply') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700">
                                Buka Toko Mitra &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Peran Administrator -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-800 flex items-center justify-center font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Peran Administrator</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pengawasan penuh operasional platform, verifikasi dan approval toko mitra baru, pemantauan transaksi gateway, dan laporan analitik bisnis.
                        </p>
                        <div class="pt-2">
                            <span class="text-xs font-semibold text-slate-500">
                                Perlindungan Sistem Terpusat
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-marketplace-layout>
