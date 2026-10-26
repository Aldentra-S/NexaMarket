<x-marketplace-layout>
    <x-slot name="title">
        {{ $search ? 'Hasil Pencarian "' . $search . '" | NexaMarket' : 'Katalog Produk Pilihan | NexaMarket' }}
    </x-slot>

    <div class="py-6 sm:py-10 bg-slate-50 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Catalog Header Banner -->
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 space-y-5 shadow-2xs">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-orange-50 text-orange-700 text-xs font-bold border border-orange-200">
                            <span>Katalog Belanja Resmi</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Jelajahi Produk Berkualitas
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Temukan ribuan produk terpercaya dari toko mitra resmi dengan jaminan pembayaran aman.
                        </p>
                    </div>

                    @if ($search || $currentCategory)
                        <div>
                            <a
                                href="{{ route('products.index') }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Reset Filter</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Horizontal Category Pills -->
                @if (isset($categories) && $categories->isNotEmpty())
                    <div class="pt-4 border-t border-slate-100 space-y-2.5">
                        <div class="flex items-center gap-2 overflow-x-auto pb-1.5 scrollbar-thin">
                            <a
                                href="{{ route('products.index', array_filter(['q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ empty($currentCategory) ? 'bg-orange-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900' }}"
                            >
                                Semua Kategori
                            </a>
                            @foreach ($categories as $cat)
                                @php
                                     $isActive = (string) $currentCategory === (string) $cat->slug || (string) $currentCategory === (string) $cat->id;
                                @endphp
                                <a
                                    href="{{ route('products.index', array_filter(['category' => $cat->slug, 'q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $isActive ? 'bg-orange-600 text-white font-bold shadow-2xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900' }}"
                                >
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Shopcart 2-Column Catalog Grid (Sidebar on Left, Products on Right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Sidebar Filters (Desktop Only) -->
                <aside class="hidden lg:block lg:col-span-3 space-y-6 lg:sticky lg:top-24">
                    <!-- Categories Filter Box -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 space-y-4 shadow-2xs">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center justify-between">
                            <span>Departemen Kategori</span>
                            <span class="text-xs text-orange-600 font-semibold">{{ $categories->count() }}</span>
                        </h3>
                        <div class="space-y-1 max-h-80 overflow-y-auto pr-1 scrollbar-thin">
                            <a
                                href="{{ route('products.index', array_filter(['q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ empty($currentCategory) ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                            >
                                <span>Semua Kategori</span>
                                @if(empty($currentCategory))
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span>
                                @endif
                            </a>
                            @foreach ($categories as $cat)
                                @php
                                    $isActive = (string) $currentCategory === (string) $cat->slug || (string) $currentCategory === (string) $cat->id;
                                @endphp
                                <a
                                    href="{{ route('products.index', array_filter(['category' => $cat->slug, 'q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                    class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition {{ $isActive ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                                >
                                    <span class="truncate">{{ $cat->name }}</span>
                                    @if($isActive)
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-600 shrink-0"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sort Filter Box -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 space-y-3 shadow-2xs">
                        <h3 class="font-bold text-slate-900 text-sm">Urutkan Produk</h3>
                        <div class="space-y-1.5 text-xs">
                            <a
                                href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'terbaru'])) }}"
                                class="flex items-center justify-between px-3 py-2 rounded-xl transition {{ $currentSort === 'terbaru' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}"
                            >
                                <span>Produk Terbaru</span>
                                <input type="radio" disabled {{ $currentSort === 'terbaru' ? 'checked' : '' }} class="text-orange-600 focus:ring-orange-500 h-3.5 w-3.5 pointer-events-none" />
                            </a>
                            <a
                                href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'termurah'])) }}"
                                class="flex items-center justify-between px-3 py-2 rounded-xl transition {{ $currentSort === 'termurah' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}"
                            >
                                <span>Harga Terendah</span>
                                <input type="radio" disabled {{ $currentSort === 'termurah' ? 'checked' : '' }} class="text-orange-600 focus:ring-orange-500 h-3.5 w-3.5 pointer-events-none" />
                            </a>
                            <a
                                href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'termahal'])) }}"
                                class="flex items-center justify-between px-3 py-2 rounded-xl transition {{ $currentSort === 'termahal' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}"
                            >
                                <span>Harga Tertinggi</span>
                                <input type="radio" disabled {{ $currentSort === 'termahal' ? 'checked' : '' }} class="text-orange-600 focus:ring-orange-500 h-3.5 w-3.5 pointer-events-none" />
                            </a>
                        </div>
                    </div>

                    <!-- Trust Assurance Box (Shopcart style) -->
                    <div class="bg-orange-50/60 rounded-2xl border border-orange-200/80 p-5 space-y-3">
                        <div class="flex items-center gap-2 text-orange-700 font-bold text-xs">
                            <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Jaminan NexaMarket</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Semua transaksi dilindungi gateway pembayaran Midtrans dengan jaminan dana aman sampai pesanan tiba.
                        </p>
                    </div>
                </aside>

                <!-- Right Main Content: Toolbar, Products Grid, & Pagination (Span 9) -->
                <main class="lg:col-span-9 space-y-6">
                    <!-- Toolbar: Results Count, Active Tags, & Sorting -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl px-5 py-3.5 shadow-2xs">
                        <!-- Meta Details -->
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600">
                            <span class="font-medium">
                                Menampilkan <strong class="text-slate-900 font-bold">{{ $products->total() }}</strong> produk
                            </span>

                            @if ($currentCategory)
                                @php
                                    $activeCategoryModel = $categories->firstWhere('slug', $currentCategory) ?? $categories->firstWhere('id', $currentCategory);
                                    $catDisplayName = $activeCategoryModel ? $activeCategoryModel->name : $currentCategory;
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                                    <span>Kategori: <strong>{{ $catDisplayName }}</strong></span>
                                    <a
                                        href="{{ route('products.index', array_filter(['q' => $search, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                        class="text-orange-700 hover:text-orange-900 font-black"
                                        title="Hapus filter kategori"
                                    >&times;</a>
                                </span>
                            @endif

                            @if ($search)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                    <span>Pencarian: <strong>"{{ $search }}"</strong></span>
                                    <a
                                        href="{{ route('products.index', array_filter(['category' => $currentCategory, 'sort' => $currentSort !== 'terbaru' ? $currentSort : null])) }}"
                                        class="text-slate-600 hover:text-slate-950 font-black"
                                        title="Hapus kata kunci"
                                    >&times;</a>
                                </span>
                            @endif
                        </div>

                        <!-- Sort Control (Shopcart Pills) -->
                        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-between sm:justify-end text-xs">
                            <span class="text-slate-500 font-medium">Urutkan:</span>
                            <div class="inline-flex max-w-full overflow-x-auto rounded-xl bg-slate-100 p-1 border border-slate-200">
                                <a
                                    href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'terbaru'])) }}"
                                    class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'terbaru' ? 'bg-white text-orange-600 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    Terbaru
                                </a>
                                <a
                                    href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'termurah'])) }}"
                                    class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'termurah' ? 'bg-white text-orange-600 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    Harga Terendah
                                </a>
                                <a
                                    href="{{ route('products.index', array_filter(['category' => $currentCategory, 'q' => $search, 'sort' => 'termahal'])) }}"
                                    class="px-3 py-1.5 rounded-lg font-semibold transition {{ $currentSort === 'termahal' ? 'bg-white text-orange-600 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    Harga Tertinggi
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Product Cards Grid -->
                    @if ($products->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4 sm:gap-6">
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
                                <h3 class="text-base sm:text-lg font-bold text-slate-900">
                                    Tidak Ada Produk Ditemukan
                                </h3>
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
                </main>
            </div>
        </div>
    </div>
</x-marketplace-layout>
