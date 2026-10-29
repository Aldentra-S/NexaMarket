<x-marketplace-layout>
    <x-slot name="title">
        {{ $product->name }} | NexaMarket
    </x-slot>

    <div class="py-6 sm:py-10 bg-slate-50 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumbs -->
            <nav class="flex items-center flex-wrap text-xs text-slate-500 gap-1.5 sm:gap-2">
                <a href="{{ url('/') }}" class="hover:text-orange-600 transition">Beranda</a>
                <span class="text-slate-300">&rsaquo;</span>
                <a href="{{ route('products.index') }}" class="hover:text-orange-600 transition">Katalog</a>
                @if ($product->category)
                    <span class="text-slate-300">&rsaquo;</span>
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-orange-600 transition font-medium text-slate-700">
                        {{ $product->category->name }}
                    </a>
                @endif
                <span class="text-slate-300">&rsaquo;</span>
                <span class="text-slate-900 font-medium truncate max-w-xs sm:max-w-md">{{ $product->name }}</span>
            </nav>

            <!-- Main Product Grid: 2 Columns for Desktop (Gallery on Left, Purchase Details on Right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Column: Gallery & Core Details (Span 7) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xs">
                        <!-- Image Gallery Component -->
                        <div x-data="{ activeImage: '{{ $product->thumbnail_url ?? asset('images/placeholder-product.svg') }}' }" class="space-y-4">
                            <!-- Main Active Image -->
                            <div class="aspect-square w-full rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 flex items-center justify-center relative">
                                <img
                                    :src="activeImage"
                                    src="{{ $product->thumbnail_url ?? asset('images/placeholder-product.svg') }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover transition-opacity duration-200"
                                />

                                @unless ($product->isInStock())
                                    <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center p-2 z-10">
                                        <span class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl">
                                            Stok Habis
                                        </span>
                                    </div>
                                @endunless
                            </div>

                            <!-- Thumbnail Strip -->
                            @if ($product->images->count() > 1)
                                <div class="flex items-center gap-3 overflow-x-auto pb-1 scrollbar-thin">
                                    @foreach ($product->images as $img)
                                        <button
                                            type="button"
                                            @click="activeImage = '{{ $img->url }}'"
                                            :class="activeImage === '{{ $img->url }}' ? 'ring-2 ring-orange-500 border-transparent shadow-xs' : 'border-slate-200 opacity-70 hover:opacity-100'"
                                            class="w-16 h-16 rounded-xl overflow-hidden bg-slate-50 border shrink-0 transition focus:outline-none"
                                            title="Lihat foto produk"
                                        >
                                            <img src="{{ $img->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Product Overview -->
                        <div class="space-y-4 pt-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-orange-50 text-orange-700 text-xs font-bold border border-orange-200">
                                    Mitra Resmi Terverifikasi
                                </span>

                                @if ($product->category)
                                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                                            {{ $product->category->name }}
                                        </span>
                                    </a>
                                @endif
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-snug">
                                {{ $product->name }}
                            </h1>

                            <!-- Rating & Reviews Bar (Rendered Only When Authentic Data Exists) -->
                            @if (!empty($product->rating) && (float) $product->rating > 0)
                                <div class="flex items-center gap-3 text-xs text-slate-500">
                                    <div class="flex items-center text-amber-500 font-bold">
                                        <svg class="w-4 h-4 fill-current mr-1" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span>{{ number_format((float) $product->rating, 1) }}</span>
                                    </div>
                                    @if (!empty($product->reviews_count) && $product->reviews_count > 0)
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="font-medium text-slate-600">{{ $product->reviews_count }} Ulasan</span>
                                    @endif
                                </div>
                            @endif

                            <!-- Price Container (Matching Mobile Visual Style) -->
                            <div class="bg-orange-50/60 border border-orange-200/80 rounded-2xl p-5 space-y-3">
                                <div>
                                    <span class="text-xs font-semibold text-slate-500 block">Harga Spesial Marketplace</span>
                                    <span class="text-3xl sm:text-4xl font-black text-orange-600 tracking-tight block mt-1">
                                        {{ $product->formattedPrice() }}
                                    </span>
                                </div>

                                <div class="pt-3 border-t border-orange-200/60 flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-600">Ketersediaan Stok:</span>
                                    @if ($product->stock > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white text-emerald-700 border border-emerald-200 shadow-2xs">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>Tersedia {{ number_format($product->stock) }} unit</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <span>Stok Habis</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Product Description -->
                        <div class="pt-6 border-t border-slate-100 space-y-3">
                            <h2 class="text-base font-bold text-slate-900">Deskripsi Lengkap Produk</h2>
                            <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed whitespace-pre-line text-xs sm:text-sm">
                                {{ $product->description ?: 'Belum ada deskripsi lengkap untuk produk ini.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Shop Attribution & Purchase Controls (Span 5) -->
                <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
                    <!-- Shop Attribution Card -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">Informasi Penjual</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                Toko Resmi
                            </span>
                        </div>

                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-orange-600 text-white font-black flex items-center justify-center text-lg shrink-0">
                                {{ strtoupper(substr($product->shop->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-900 text-base truncate">
                                    {{ $product->shop->name }}
                                </h3>
                                <p class="text-xs text-slate-500 truncate">
                                    Pemilik: {{ $product->shop->user->name ?? 'Mitra NexaMarket' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-xs text-slate-600 space-y-2 pt-3 border-t border-slate-100">
                            @if ($product->shop->address)
                                <div>
                                    <strong class="text-slate-700 block">Lokasi Toko:</strong>
                                    <p class="text-slate-500 mt-0.5">{{ $product->shop->address }}</p>
                                </div>
                            @endif
                            <div class="flex items-center justify-between pt-1">
                                <span class="font-medium text-slate-600">Status Toko:</span>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200">
                                    {{ $product->shop->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase / Cart Action Box (Matching Image 2 Screen 3 & Image 3 Screen 2) -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-700">Atur Pembelian</h3>
                            <span class="text-xs text-slate-500">Checkout Terpadu</span>
                        </div>

                        @if ($product->isInStock())
                            <form
                                action="{{ route('cart.items.store') }}"
                                method="POST"
                                x-data="{ qty: 1, max: {{ $product->stock }}, price: {{ $product->price }} }"
                                class="space-y-4"
                            >
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <div>
                                    <label for="quantity" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Jumlah Pembelian (Maks. {{ $product->stock }} unit)
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="inline-flex items-center border border-slate-300 rounded-xl overflow-hidden bg-white focus-within:ring-2 focus-within:ring-orange-500 focus-within:border-orange-500">
                                            <button
                                                type="button"
                                                @click="if (qty > 1) qty--"
                                                class="px-3.5 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold text-sm transition focus:outline-none"
                                                aria-label="Kurangi kuantitas"
                                            >
                                                &minus;
                                            </button>
                                            <input
                                                type="number"
                                                id="quantity"
                                                name="quantity"
                                                x-model.number="qty"
                                                min="1"
                                                max="{{ $product->stock }}"
                                                class="w-16 text-center font-bold text-sm text-slate-900 border-0 focus:ring-0 p-0 py-2 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                required
                                            />
                                            <button
                                                type="button"
                                                @click="if (qty < max) qty++"
                                                class="px-3.5 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold text-sm transition focus:outline-none"
                                                aria-label="Tambah kuantitas"
                                            >
                                                +
                                            </button>
                                        </div>
                                        <span class="text-xs text-slate-500">unit barang</span>
                                    </div>
                                </div>

                                <!-- Subtotal Preview -->
                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-medium">Subtotal Produk:</span>
                                    <span class="text-lg font-black text-orange-600">
                                        Rp <span x-text="(qty * price).toLocaleString('id-ID')"></span>
                                    </span>
                                </div>

                                <!-- Dual Action CTA Buttons (Inspired by Image 2 & Image 3) -->
                                <div class="pt-2 space-y-2.5">
                                    <button
                                        type="submit"
                                        class="w-full py-3.5 px-4 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm rounded-xl transition flex items-center justify-center gap-2 focus:ring-2 focus:ring-orange-500"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        + Tambah ke Keranjang
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-2">
                                <p class="text-xs font-semibold text-slate-600">Stok produk ini sedang habis dari pihak penjual.</p>
                                <button type="button" disabled class="w-full py-3 px-4 bg-slate-200 text-slate-400 font-bold text-xs rounded-xl cursor-not-allowed">
                                    Stok Habis
                                </button>
                            </div>
                        @endif

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <a href="{{ route('cart.index') }}" class="font-bold text-orange-600 hover:text-orange-700 transition">
                                Buka Keranjang Belanja &rarr;
                            </a>
                            <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-slate-700 transition">
                                Katalog Produk
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-marketplace-layout>
