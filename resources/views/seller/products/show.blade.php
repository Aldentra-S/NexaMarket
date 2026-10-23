<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-600 transition">Seller Center</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('seller.products.index') }}" class="hover:text-emerald-600 transition">Produk</a>
                    <span>&rsaquo;</span>
                    <span class="text-gray-800 font-medium">Detail Produk</span>
                </div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    {{ $product->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Rincian produk pada etalase toko Anda</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('seller.products.index') }}" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 transition">
                    &larr; Daftar Produk
                </a>
                <a href="{{ route('seller.products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    Edit Produk
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Main Product Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Produk Resmi Toko</span>
                            <h3 class="text-2xl font-black text-gray-900 mt-1">{{ $product->name }}</h3>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">Slug: {{ $product->slug }}</p>
                        </div>
                        <div class="flex items-center flex-wrap gap-2">
                            @if ($product->category)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    Kategori: {{ $product->category->name }}
                                </span>
                            @endif
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $product->status->badgeColor() }}">
                                Status: {{ $product->status->label() }}
                            </span>
                        </div>
                    </div>

                    <!-- Price & Stock Highlights -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-[11px] font-bold text-gray-400 uppercase">Harga Jual</span>
                            <div class="text-xl font-black text-emerald-600 mt-1">{{ $product->formattedPrice() }}</div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-[11px] font-bold text-gray-400 uppercase">Ketersediaan Stok</span>
                            <div class="text-xl font-black text-gray-900 mt-1">
                                {{ number_format($product->stock) }} <span class="text-xs font-normal text-gray-500">unit</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-[11px] font-bold text-gray-400 uppercase">Terdaftar Pada</span>
                            <div class="text-sm font-bold text-gray-800 mt-1.5">{{ $product->created_at->format('d M Y') }}</div>
                            <div class="text-[10px] text-gray-400">{{ $product->created_at->format('H:i') }} WIB</div>
                        </div>
                    </div>

                    <!-- Images Gallery Section -->
                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Foto Produk ({{ $product->images->count() }})</h4>
                            <a href="{{ route('seller.products.edit', $product) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">
                                + Kelola Foto
                            </a>
                        </div>
                        @if ($product->images->isNotEmpty())
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                @foreach ($product->images as $img)
                                    <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-50 border {{ $img->is_primary ? 'border-emerald-500 ring-2 ring-emerald-200' : 'border-gray-200' }}">
                                        <img src="{{ $img->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                        @if ($img->is_primary)
                                            <span class="absolute bottom-1.5 left-1.5 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-xs">
                                                Utama
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-xl border border-gray-100 text-xs text-gray-500">
                                <img src="{{ asset('images/placeholder-product.svg') }}" alt="Placeholder" class="w-12 h-12 rounded-lg object-cover" />
                                <div>
                                    <p class="font-medium text-gray-700">Belum ada foto produk</p>
                                    <p class="text-[11px] text-gray-400">Gunakan tombol edit untuk mengunggah foto produk etalase.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Produk</h4>
                        <div class="p-5 rounded-xl bg-gray-50 border border-gray-100 text-xs text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $product->description }}
                        </div>
                    </div>

                    <!-- Public Link if Published -->
                    @if ($product->isPublished())
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-gray-500">Produk ini aktif ditayangkan di katalog publik NexaMarket.</span>
                            <a href="{{ route('products.show', $product->slug) }}" class="font-bold text-emerald-600 hover:text-emerald-700 transition" target="_blank">
                                Buka Halaman Publik Produk &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
