<x-marketplace-layout>
    <x-slot name="title">
        {{ $search ? 'Hasil Pencarian "' . $search . '" | NexaMarket' : 'Katalog Belanja Resmi | NexaMarket' }}
    </x-slot>

    @php
        $activeCategory = null;
        if ($currentCategory) {
            $activeCategory = $categories->firstWhere('slug', $currentCategory) 
                ?? $categories->firstWhere('id', $currentCategory);
        }
    @endphp

    <div class="py-6 sm:py-8 bg-slate-50 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Catalog Header & Controls Bar -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Left Title & Subtitle -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Jelajahi Produk Berkualitas
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Temukan produk terpercaya dari toko mitra resmi.
                    </p>
                </div>

                <!-- Right Controls: Count & Sort Pills -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <!-- Products Count -->
                    <span class="text-xs sm:text-sm text-slate-500 whitespace-nowrap">
                        Menampilkan <strong class="font-bold text-slate-900">{{ $products->total() }}</strong> produk
                    </span>

                    <!-- Sort Filter Pills -->
                    <div class="inline-flex items-center rounded-xl bg-white border border-slate-200/90 p-1 shadow-2xs">
                        <a
                            href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'terbaru'])) }}"
                            class="px-3 py-1.5 rounded-lg text-xs transition {{ ($currentSort === 'terbaru' || empty($currentSort)) ? 'bg-orange-50/70 text-orange-600 font-bold border border-orange-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium' }}"
                        >
                            Terbaru
                        </a>
                        <a
                            href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'termurah'])) }}"
                            class="px-3 py-1.5 rounded-lg text-xs transition {{ $currentSort === 'termurah' ? 'bg-orange-50/70 text-orange-600 font-bold border border-orange-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium' }}"
                        >
                            Harga Terendah
                        </a>
                        <a
                            href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'termahal'])) }}"
                            class="px-3 py-1.5 rounded-lg text-xs transition {{ $currentSort === 'termahal' ? 'bg-orange-50/70 text-orange-600 font-bold border border-orange-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium' }}"
                        >
                            Harga Tertinggi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active Filter Badges (Category & Search) -->
            @if ($currentCategory || $search)
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-slate-400 font-medium">Filter aktif:</span>

                    @if ($activeCategory)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-semibold bg-orange-50 text-orange-700 border border-orange-200 shadow-2xs">
                            <span>Kategori: <strong>{{ $activeCategory->name }}</strong></span>
                            <a
                                href="{{ route('products.index', array_filter(['q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                class="text-orange-700 hover:text-orange-900 font-black ml-0.5"
                                title="Hapus filter kategori"
                            >&times;</a>
                        </span>
                    @endif

                    @if ($search)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-semibold bg-slate-100 text-slate-800 border border-slate-200 shadow-2xs">
                            <span>Pencarian: <strong>"{{ $search }}"</strong></span>
                            <a
                                href="{{ route('products.index', array_filter(['category' => $currentCategory, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                class="text-slate-600 hover:text-slate-950 font-black ml-0.5"
                                title="Hapus pencarian"
                            >&times;</a>
                        </span>
                    @endif

                    <a
                        href="{{ route('products.index') }}"
                        class="text-xs font-semibold text-rose-600 hover:text-rose-700 underline ml-2 transition"
                    >
                        Reset Semua
                    </a>
                </div>
            @endif

            <!-- Full-Width Responsive Products Grid (Exactly 4 Cards Per Row on Desktop) -->
            @if ($products->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <!-- Pagination Container -->
                <div class="pt-6">
                    {{ $products->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 sm:p-14 text-center max-w-lg mx-auto space-y-4 shadow-2xs">
                    <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mx-auto shadow-2xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">
                            Tidak Ada Produk Ditemukan
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-xs mx-auto">
                            @if ($search || $currentCategory)
                                Tidak ada produk yang cocok dengan kriteria filter saat ini. Coba kata kunci lain atau reset filter.
                            @else
                                Belum ada produk aktif yang tersedia di katalog belanja saat ini.
                            @endif
                        </p>
                    </div>
                    @if ($search || $currentCategory)
                        <div class="pt-2">
                            <a
                                href="{{ route('products.index') }}"
                                class="inline-flex items-center px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs transition shadow-2xs"
                            >
                                Reset Filter Pencarian
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-marketplace-layout>
