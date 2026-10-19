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
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $product->status->badgeColor() }}">
                            Status: {{ $product->status->label() }}
                        </span>
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
