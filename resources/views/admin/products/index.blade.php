<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Katalog Produk Seluruh Penjual
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Awasi etalase dan peredaran barang yang dipublikasikan oleh mitra penjual NexaMarket</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Search & Filters Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search & Category Form -->
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-2 max-w-xl">
                    @if ($currentStatus)
                        <input type="hidden" name="status" value="{{ $currentStatus }}" />
                    @endif
                    <div class="relative flex-1 w-full">
                        <input 
                            type="search" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari nama produk atau deskripsi..." 
                            class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-xl focus:border-emerald-700 focus:ring-emerald-700 text-slate-900" 
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <select name="category_id" onchange="this.form.submit()" class="w-full sm:w-auto text-xs border border-slate-200 rounded-xl py-2 px-3 focus:border-emerald-700 focus:ring-emerald-700 text-slate-700">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string)$currentCategory === (string)$category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                        Filter
                    </button>
                </form>

                <!-- Status Filter Tabs -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('admin.products.index', ['search' => $search, 'category_id' => $currentCategory]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ empty($currentStatus) ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Semua ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.products.index', ['status' => 'published', 'search' => $search, 'category_id' => $currentCategory]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'published' ? 'bg-emerald-800 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Ditayangkan ({{ $publishedCount }})
                    </a>
                    <a href="{{ route('admin.products.index', ['status' => 'draft', 'search' => $search, 'category_id' => $currentCategory]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'draft' ? 'bg-amber-700 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Draf ({{ $draftCount }})
                    </a>
                    <a href="{{ route('admin.products.index', ['status' => 'archived', 'search' => $search, 'category_id' => $currentCategory]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'archived' ? 'bg-slate-700 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Diarsipkan ({{ $archivedCount }})
                    </a>
                </div>
            </div>

            <!-- Products Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[700px] w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Produk</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Toko Mitra</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Kategori</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Harga Satuan</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Stok</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($products as $product)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                                @if($product->primaryImage)
                                                    <img src="{{ Storage::url($product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm max-w-xs truncate">{{ $product->name }}</div>
                                                <div class="text-[11px] text-slate-400">ID: #{{ $product->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->shop)
                                            <a href="{{ route('admin.shops.show', $product->shop) }}" class="font-semibold text-slate-800 hover:text-emerald-700 transition">
                                                {{ $product->shop->name }}
                                            </a>
                                            <div class="text-[11px] text-slate-400">Owner: {{ $product->shop->user->name ?? '-' }}</div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px] font-medium">
                                            {{ $product->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-slate-900">
                                        {{ $product->formattedPrice() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($product->stock <= 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                                Habis (0)
                                            </span>
                                        @elseif($product->stock <= 5)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                Sisa {{ $product->stock }}
                                            </span>
                                        @else
                                            <span class="text-slate-700 font-medium">
                                                {{ $product->stock }} unit
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $product->status->badgeColor() }}">
                                            {{ $product->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                        @if($product->status->value === 'published')
                                            <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                                                Lihat di Toko &rarr;
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">Tidak Tayang</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <p class="text-sm font-medium">Tidak ada produk yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
