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

                <!-- Right Controls: Category Filter (Mobile/Tablet), Count & Sort Pills -->
                <div class="flex flex-wrap items-center gap-2.5 sm:gap-4">
                    <!-- Mobile / Tablet Category Filter Trigger -->
                    <div class="relative lg:hidden" x-data="{ catOpen: false }" @click.outside="catOpen = false" @keydown.escape.window="catOpen = false">
                        <button
                            type="button"
                            @click="catOpen = !catOpen"
                            :aria-expanded="catOpen.toString()"
                            aria-haspopup="true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200/90 text-xs font-bold text-slate-700 hover:text-orange-600 hover:border-orange-300 transition shadow-2xs focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                        >
                            <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            <span class="truncate max-w-[130px] sm:max-w-none">
                                {{ $activeCategory ? $activeCategory->name : 'Semua Kategori' }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0" :class="{ 'rotate-180': catOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Mobile / Tablet Category Dropdown Panel -->
                        <div
                            x-show="catOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-64 bg-white rounded-2xl border border-slate-200 shadow-xl py-2 z-30 divide-y divide-slate-100"
                            style="display: none;"
                        >
                            <div class="px-3 pb-2 pt-1 flex items-center justify-between text-xs font-bold text-slate-800">
                                <span>Pilih Kategori</span>
                                <button type="button" @click="catOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500" aria-label="Tutup menu kategori">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="max-h-64 overflow-y-auto space-y-0.5 p-1 scrollbar-thin">
                                <a
                                    href="{{ route('products.index', array_filter(['q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ empty($currentCategory) ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"
                                >
                                    <span>Semua Kategori</span>
                                    @if(empty($currentCategory))
                                        <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </a>
                                @foreach($categories as $category)
                                    @php
                                        $isSelected = ($currentCategory == $category->slug || $currentCategory == $category->id);
                                    @endphp
                                    <a
                                        href="{{ route('products.index', array_filter(['category' => $category->slug, 'q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                        class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $isSelected ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"
                                    >
                                        <span class="truncate pr-2">{{ $category->name }}</span>
                                        @if($isSelected)
                                            <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Products Count -->
                    <span class="text-xs sm:text-sm text-slate-500 whitespace-nowrap">
                        Menampilkan <strong class="font-bold text-slate-900">{{ $products->total() }}</strong> produk
                    </span>

                    <!-- Sort Filter Pills -->
                    <div class="inline-flex items-center rounded-xl bg-white border border-slate-200/90 p-1 shadow-2xs">
                        <a
                            href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'terbaru'])) }}"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ ($currentSort === 'terbaru' || empty($currentSort)) ? 'bg-orange-50/70 text-orange-600 font-bold border border-orange-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium' }}"
                        >
                            Terbaru
                        </a>
                        <a
                            href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'termurah'])) }}"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $currentSort === 'termurah' ? 'bg-orange-50/70 text-orange-600 font-bold border border-orange-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium' }}"
                        >
                            Harga Terendah
                        </a>
                        <a
                            href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'termahal'])) }}"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $currentSort === 'termahal' ? 'bg-orange-50/70 text-orange-600 font-bold border border-orange-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium' }}"
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
                        <span class="inline-flex items-center gap-1.5 pl-3 pr-1 py-1 rounded-full font-semibold bg-orange-50 text-orange-700 border border-orange-200 shadow-2xs">
                            <span>Kategori: <strong>{{ $activeCategory->name }}</strong></span>
                            <a
                                href="{{ route('products.index', array_filter(['q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                class="w-6 h-6 inline-flex items-center justify-center rounded-full text-orange-700 hover:bg-orange-200/80 hover:text-orange-950 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                                title="Hapus filter kategori {{ $activeCategory->name }}"
                                aria-label="Hapus filter kategori {{ $activeCategory->name }}"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </span>
                    @endif

                    @if ($search)
                        <span class="inline-flex items-center gap-1.5 pl-3 pr-1 py-1 rounded-full font-semibold bg-slate-100 text-slate-800 border border-slate-200 shadow-2xs">
                            <span>Pencarian: <strong>"{{ $search }}"</strong></span>
                            <a
                                href="{{ route('products.index', array_filter(['category' => $currentCategory, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                class="w-6 h-6 inline-flex items-center justify-center rounded-full text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                                title="Hapus filter pencarian {{ $search }}"
                                aria-label="Hapus filter pencarian {{ $search }}"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </span>
                    @endif

                    <a
                        href="{{ route('products.index') }}"
                        class="text-xs font-semibold text-rose-600 hover:text-rose-700 underline ml-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 rounded"
                    >
                        Reset Semua
                    </a>
                </div>
            @endif

            <!-- Full-Width Responsive Products Grid (2 Cols on Phone, 3 Cols on Tablet, 4 Cols on Desktop) -->
            @if ($products->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5 lg:gap-6">
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
