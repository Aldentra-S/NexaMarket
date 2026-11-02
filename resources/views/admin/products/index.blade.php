<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight">
                    Katalog Produk Seluruh Penjual
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar seluruh etalase produk toko mitra terdaftar di ekosistem marketplace NexaMarket</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Search & Filters Bar -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-soft flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                    @if ($currentStatus)
                        <input type="hidden" name="status" value="{{ $currentStatus }}" />
                    @endif
                    <div class="relative flex-1 w-full">
                        <input 
                            type="search" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari nama produk atau deskripsi..." 
                            class="w-full pl-9 pr-4 py-2 text-xs border border-[#E8EAF0] dark:border-slate-700 bg-white dark:bg-slate-900 rounded-xl focus:border-coral-500 focus:ring-2 focus:ring-coral-500/20 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 outline-none transition shadow-2xs" 
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <select name="category_id" onchange="this.form.submit()" class="w-full sm:w-auto text-xs border border-[#E8EAF0] dark:border-slate-700 bg-white dark:bg-slate-900 rounded-xl py-2 px-3 focus:border-coral-500 focus:ring-2 focus:ring-coral-500/20 text-slate-700 dark:text-slate-200 outline-none transition shadow-2xs">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string)$currentCategory === (string)$category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 dark:bg-white hover:bg-slate-800 dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold rounded-xl transition shadow-2xs">
                        Filter
                    </button>
                </form>

                <!-- Status Filter Tabs -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('admin.products.index', ['search' => $search, 'category_id' => $currentCategory]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ empty($currentStatus) ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-2xs' : 'bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Semua ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.products.index', ['status' => 'published', 'search' => $search, 'category_id' => $currentCategory]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'published' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-2xs' : 'bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Ditayangkan ({{ $publishedCount }})
                    </a>
                    <a href="{{ route('admin.products.index', ['status' => 'draft', 'search' => $search, 'category_id' => $currentCategory]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'draft' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-2xs' : 'bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Draft ({{ $draftCount }})
                    </a>
                    <a href="{{ route('admin.products.index', ['status' => 'archived', 'search' => $search, 'category_id' => $currentCategory]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'archived' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-2xs' : 'bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Diarsipkan ({{ $archivedCount }})
                    </a>
                </div>
            </div>

            <!-- Products Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-[#E8EAF0] dark:border-slate-800 shadow-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[750px] w-full divide-y divide-[#E8EAF0] dark:divide-slate-800 text-xs">
                        <thead class="bg-[#F7F8FA] dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-semibold border-b border-[#E8EAF0] dark:border-slate-800">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Produk</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Toko Mitra</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Kategori</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Harga</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Stok</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E8EAF0] dark:divide-slate-800 bg-white dark:bg-slate-900">
                            @forelse ($products as $product)
                                <tr class="hover:bg-[#F7F8FA]/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-[#F7F8FA] dark:bg-slate-800 overflow-hidden border border-[#E8EAF0] dark:border-slate-700 shrink-0">
                                                <img 
                                                    src="{{ $product->thumbnail_url }}" 
                                                    alt="{{ $product->name }}" 
                                                    class="w-full h-full object-cover" 
                                                    loading="lazy"
                                                />
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white text-sm max-w-xs truncate">{{ $product->name }}</div>
                                                <div class="text-[11px] text-slate-400">ID: #{{ $product->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->shop)
                                            <a href="{{ route('admin.shops.show', $product->shop) }}" class="font-semibold text-slate-800 dark:text-slate-200 hover:text-coral-600 dark:hover:text-coral-400 transition">
                                                {{ $product->shop->name }}
                                            </a>
                                            <div class="text-[11px] text-slate-400">Owner: {{ $product->shop->user->name ?? '-' }}</div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">
                                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-[11px] font-medium">
                                            {{ $product->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-slate-900 dark:text-white">
                                        {{ $product->formattedPrice() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($product->stock <= 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60">
                                                Habis (0)
                                            </span>
                                        @elseif($product->stock <= 5)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60">
                                                Sisa {{ $product->stock }}
                                            </span>
                                        @else
                                            <span class="text-slate-700 dark:text-slate-300 font-medium">
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
                                            <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-xs font-semibold text-coral-600 hover:text-coral-700 dark:text-coral-400">
                                                Lihat di Toko &rarr;
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">Tidak Tayang</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        <p class="text-sm font-medium">Tidak ada produk yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="p-4 border-t border-[#E8EAF0] dark:border-slate-800">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
