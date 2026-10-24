<x-marketplace-layout>
    <x-slot name="title">
        {{ $product->name }} — NexaMarket
    </x-slot>

    <div class="py-10 bg-gray-50/60 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumbs -->
            <nav class="flex items-center flex-wrap text-xs text-gray-500 gap-2">
                <a href="{{ url('/') }}" class="hover:text-emerald-600 transition">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ route('products.index') }}" class="hover:text-emerald-600 transition">Katalog Produk</a>
                @if ($product->category)
                    <span>&rsaquo;</span>
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-purple-600 transition font-medium text-purple-700">
                        {{ $product->category->name }}
                    </a>
                @endif
                <span>&rsaquo;</span>
                <span class="text-gray-900 font-medium truncate max-w-xs sm:max-w-md">{{ $product->name }}</span>
            </nav>

            <!-- Main Product Detail Card -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 sm:p-10">
                    <!-- Left: Product Core Information (Span 2) -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Image Gallery -->
                            <div x-data="{ activeImage: '{{ $product->thumbnail_url }}' }" class="space-y-3">
                                <div class="aspect-square w-full rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 flex items-center justify-center shadow-xs">
                                    <img :src="activeImage" src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-300" />
                                </div>
                                @if ($product->images->count() > 1)
                                    <div class="flex items-center gap-2.5 overflow-x-auto pb-1">
                                        @foreach ($product->images as $img)
                                            <button type="button" 
                                                    @click="activeImage = '{{ $img->url }}'" 
                                                    :class="activeImage === '{{ $img->url }}' ? 'ring-2 ring-emerald-500 border-transparent' : 'border-gray-200 opacity-70 hover:opacity-100'" 
                                                    class="w-16 h-16 rounded-xl overflow-hidden bg-gray-50 border shrink-0 transition focus:outline-none">
                                                <img src="{{ $img->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Product Summary & Price Box -->
                            <div class="space-y-5 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center flex-wrap gap-2">
                                        <div class="flex items-center space-x-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full w-fit">
                                            <span>Terverifikasi di NexaMarket</span>
                                        </div>
                                        @if ($product->category)
                                            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="inline-flex items-center text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 px-3 py-1 rounded-full transition">
                                                Kategori: {{ $product->category->name }}
                                            </a>
                                        @endif
                                    </div>
                                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight leading-snug">
                                        {{ $product->name }}
                                    </h1>
                                    <p class="text-xs text-gray-400 font-mono">Kode Slug: {{ $product->slug }}</p>
                                </div>

                                <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100 space-y-4">
                                    <div>
                                        <span class="text-xs text-gray-400 uppercase tracking-wider block">Harga Penjual</span>
                                        <span class="text-3xl font-black text-emerald-600 tracking-tight mt-0.5 block">
                                            {{ $product->formattedPrice() }}
                                        </span>
                                    </div>

                                    <div class="pt-3 border-t border-gray-200/60">
                                        <span class="text-xs text-gray-400 uppercase tracking-wider block">Ketersediaan Stok</span>
                                        <div class="mt-1">
                                            @if ($product->stock > 0)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Tersedia: {{ number_format($product->stock) }} unit
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                    Stok Habis
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Description -->
                        <div class="space-y-3 pt-4 border-t border-gray-100">
                            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Deskripsi Lengkap Produk</h2>
                            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                                {{ $product->description }}
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Shop & Purchase Readiness Card -->
                    <div class="space-y-6">
                        <!-- Shop Info Card -->
                        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-xs space-y-4">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Penjual</h3>
                            
                            <div class="flex items-center space-x-3.5 pt-1">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-extrabold flex items-center justify-center text-lg shadow-sm">
                                    {{ strtoupper(substr($product->shop->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm hover:text-emerald-600 transition">
                                        {{ $product->shop->name }}
                                    </h4>
                                    <p class="text-[11px] text-gray-400 font-mono">{{ $product->shop->slug }}</p>
                                </div>
                            </div>

                            <div class="text-xs text-gray-600 space-y-2 pt-2 border-t border-gray-100">
                                @if ($product->shop->address)
                                    <div>
                                        <strong class="text-gray-700">Lokasi Toko:</strong>
                                        <p class="text-gray-500 mt-0.5">{{ $product->shop->address }}</p>
                                    </div>
                                @endif
                                <div>
                                    <strong class="text-gray-700">Status Toko:</strong>
                                    <span class="ml-1 inline-flex items-center px-2 py-0.2 rounded text-[10px] font-semibold border {{ $product->shop->status->badgeColor() }}">
                                        {{ $product->shop->status->label() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase & Cart Action Box -->
                        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-xs space-y-4">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Beli Produk</h3>

                            @if ($product->isInStock())
                                <form action="{{ route('cart.items.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div>
                                        <label for="quantity" class="block text-xs font-bold text-gray-700 mb-1">
                                            Jumlah Pesanan (Maks. {{ $product->stock }})
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="number"
                                                id="quantity"
                                                name="quantity"
                                                value="1"
                                                min="1"
                                                max="{{ $product->stock }}"
                                                class="w-24 text-center font-bold text-sm rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                required
                                            >
                                            <span class="text-xs text-gray-500">unit</span>
                                        </div>
                                    </div>

                                    <button
                                        type="submit"
                                        class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Tambah ke Keranjang
                                    </button>
                                </form>
                            @else
                                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-center">
                                    <p class="text-xs font-semibold text-gray-500">Stok produk ini sedang habis.</p>
                                    <button type="button" disabled class="mt-2 w-full py-2.5 px-4 bg-gray-200 text-gray-400 font-bold text-xs rounded-xl cursor-not-allowed">
                                        Stok Habis
                                    </button>
                                </div>
                            @endif

                            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-xs">
                                <a href="{{ route('cart.index') }}" class="font-medium text-emerald-600 hover:text-emerald-700">
                                    Lihat Keranjang &rarr;
                                </a>
                                <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600">
                                    Katalog
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-marketplace-layout>
