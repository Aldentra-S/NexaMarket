<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-700 transition">Seller Center</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('seller.products.index') }}" class="hover:text-emerald-700 transition">Produk</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800 font-medium">Detail Produk</span>
                </div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ $product->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Rincian produk pada etalase toko Anda</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('seller.products.index') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    &larr; Daftar Produk
                </a>
                <a href="{{ route('seller.products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition">
                    Edit Produk
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Main Product Card -->
            <div class="bg-white overflow-hidden rounded-2xl border border-slate-200">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <span class="text-xs font-semibold text-emerald-800">Produk Resmi Toko</span>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $product->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Slug: {{ $product->slug }}</p>
                        </div>
                        <div class="flex items-center flex-wrap gap-2">
                            @if ($product->category)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                    Kategori: {{ $product->category->name }}
                                </span>
                            @endif
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $product->status->badgeColor() }}">
                                Status: {{ $product->status->label() }}
                            </span>
                        </div>
                    </div>

                    <!-- Price & Stock Highlights -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-500">Harga Jual Satuan</span>
                            <div class="text-xl font-bold text-emerald-700 mt-1">{{ $product->formattedPrice() }}</div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-500">Ketersediaan Stok</span>
                            <div class="text-xl font-bold text-slate-900 mt-1">
                                {{ number_format($product->stock) }} <span class="text-xs font-normal text-slate-500">unit</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-500">Waktu Pendaftaran</span>
                            <div class="text-sm font-semibold text-slate-800 mt-1.5">{{ $product->created_at->format('d M Y') }}</div>
                            <div class="text-[11px] text-slate-400">{{ $product->created_at->format('H:i') }} WIB</div>
                        </div>
                    </div>

                    <!-- Images Gallery Section -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold text-slate-700">Foto Produk ({{ $product->images->count() }})</h4>
                            <a href="{{ route('seller.products.edit', $product) }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold">
                                + Kelola Foto
                            </a>
                        </div>
                        @if ($product->images->isNotEmpty())
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                @foreach ($product->images as $img)
                                    <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50 border {{ $img->is_primary ? 'border-emerald-600 ring-2 ring-emerald-200' : 'border-slate-200' }}">
                                        <img src="{{ $img->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                        @if ($img->is_primary)
                                            <span class="absolute bottom-1.5 left-1.5 bg-emerald-700 text-white text-[10px] font-semibold px-1.5 py-0.5 rounded">
                                                Foto Utama
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-500">
                                <div class="w-12 h-12 rounded-lg bg-slate-200 flex items-center justify-center text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700">Belum ada foto produk</p>
                                    <p class="text-[11px] text-slate-400">Gunakan tombol edit produk untuk mengunggah foto etalase.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-700 mb-2">Deskripsi Produk</h4>
                        <div class="p-5 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $product->description }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
