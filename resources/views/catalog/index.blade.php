<x-marketplace-layout>
    <x-slot name="title">
        {{ $search ? 'Hasil Pencarian "' . $search . '" — NexaMarket' : 'Katalog Produk Pilihan — NexaMarket' }}
    </x-slot>

    <div class="py-10 bg-gray-50/60 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Catalog Banner & Search Header -->
            <div class="bg-gradient-to-r from-emerald-800 via-teal-800 to-emerald-950 rounded-3xl p-6 sm:p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10 max-w-2xl">
                    <span class="text-[11px] font-bold tracking-widest uppercase bg-white/15 px-3 py-1 rounded-full text-emerald-200">
                        Multi-Vendor Catalog
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-black mt-3 tracking-tight">
                        Jelajahi Produk Berkualitas dari Mitra Terpercaya
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100 mt-2 leading-relaxed">
                        Temukan ribuan produk pilihan dari berbagai penjual resmi terverifikasi di seluruh Indonesia dengan jaminan kepuasan transaksi.
                    </p>

                    <!-- Search Box Inside Banner -->
                    <form method="GET" action="{{ route('products.index') }}" class="mt-6 flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <input type="search" 
                                   name="q" 
                                   value="{{ $search }}" 
                                   placeholder="Cari produk impian Anda..." 
                                   class="w-full pl-10 pr-4 py-3 bg-white text-gray-900 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 placeholder-gray-400 shadow-sm" />
                            <svg class="w-5 h-5 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        @if ($currentSort !== 'terbaru')
                            <input type="hidden" name="sort" value="{{ $currentSort }}">
                        @endif
                        <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-bold text-xs sm:text-sm rounded-xl transition shadow-sm">
                            Cari Produk
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filter Controls & Result Count Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-gray-200">
                <div class="text-xs text-gray-500">
                    @if ($search)
                        Menampilkan hasil pencarian untuk: <strong class="text-gray-900">"{{ $search }}"</strong>
                        <a href="{{ route('products.index') }}" class="text-emerald-600 hover:underline ml-2">(Hapus filter)</a>
                    @else
                        Menampilkan produk terverifikasi dari mitra penjual aktif
                    @endif
                </div>

                <!-- Sort Control -->
                <div class="flex items-center space-x-2 text-xs">
                    <span class="text-gray-500 font-medium">Urutkan:</span>
                    <div class="flex items-center bg-white border border-gray-200 rounded-xl p-0.5 shadow-xs">
                        <a href="{{ route('products.index', array_filter(['q' => $search, 'sort' => 'terbaru'])) }}" 
                           class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'terbaru' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100' }}">
                            Terbaru
                        </a>
                        <a href="{{ route('products.index', array_filter(['q' => $search, 'sort' => 'termurah'])) }}" 
                           class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'termurah' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100' }}">
                            Harga Termurah
                        </a>
                        <a href="{{ route('products.index', array_filter(['q' => $search, 'sort' => 'termahal'])) }}" 
                           class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'termahal' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100' }}">
                            Harga Termahal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                        <div>
                            <!-- Product Thumbnail Image -->
                            <a href="{{ route('products.show', $product->slug) }}" class="block aspect-square w-full bg-gray-50 overflow-hidden relative border-b border-gray-100">
                                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy" />
                            </a>

                            <div class="p-5">
                                <!-- Shop Affiliation Badge -->
                                <div class="flex items-center justify-between text-[11px] text-gray-500 mb-2">
                                <span class="inline-flex items-center text-gray-600 font-medium truncate max-w-[150px]">
                                    <svg class="w-3.5 h-3.5 mr-1 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $product->shop->name }}
                                </span>

                                @if ($product->stock > 0)
                                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        Stok: {{ number_format($product->stock) }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-semibold text-red-700 bg-red-50 px-2 py-0.5 rounded-md">
                                        Habis
                                    </span>
                                @endif
                            </div>

                            <!-- Product Title -->
                            <h3 class="font-bold text-gray-900 text-sm group-hover:text-emerald-600 transition line-clamp-2 min-h-[2.5rem]">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <!-- Description Snippet -->
                            <p class="text-xs text-gray-500 mt-2 line-clamp-2 leading-relaxed">
                                {{ $product->description }}
                            </p>
                        </div>
                    </div>

                    <div class="p-5 pt-0 border-t border-gray-50 mt-4">
                            <div class="flex items-baseline justify-between pt-3">
                                <div>
                                    <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Harga</span>
                                    <span class="text-base font-extrabold text-emerald-600">
                                        {{ $product->formattedPrice() }}
                                    </span>
                                </div>

                                <a href="{{ route('products.show', $product->slug) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-gray-50 hover:bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl transition">
                                    Detail &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 p-8">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Tidak ada produk yang sesuai</h3>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                            Coba ubah kata kunci pencarian atau bersihkan filter untuk menemukan produk yang Anda cari.
                        </p>
                        @if ($search)
                            <div class="mt-4">
                                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                                    Lihat Seluruh Produk
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($products->hasPages())
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-marketplace-layout>
