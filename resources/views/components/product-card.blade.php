@props([
    'product',
])

@php
    $price = number_format((float) $product->price, 0, ',', '.');
    $isAvailable = method_exists($product, 'isAvailable') ? $product->isAvailable() : ($product->stock > 0);
    $thumbnail = $product->thumbnail_url ?? asset('images/placeholder-product.svg');
@endphp

<div {{ $attributes->merge(['class' => 'group relative bg-white rounded-2xl border border-slate-200/90 hover:border-orange-500 hover:shadow-md transition-all duration-200 flex flex-col overflow-hidden h-full shadow-2xs']) }}>
    <!-- Product Thumbnail Container -->
    <div class="aspect-square w-full bg-slate-50 overflow-hidden relative block border-b border-slate-100">
        <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
            <img
                src="{{ $thumbnail }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 ease-out"
            />
        </a>

        <!-- Floating Category Pill Badge (Top Left) -->
        @if ($product->category)
            <span class="absolute top-3 left-3 px-3 py-1 bg-white/95 backdrop-blur-xs text-[11px] font-semibold text-slate-700 rounded-full border border-slate-200/80 shadow-2xs z-10 pointer-events-none truncate max-w-[70%]">
                {{ $product->category->name }}
            </span>
        @endif

        <!-- Floating Wishlist Button (Top Right) -->
        <button
            type="button"
            class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/95 hover:bg-white text-slate-400 hover:text-rose-500 border border-slate-200/80 flex items-center justify-center transition shadow-2xs z-10"
            title="Simpan ke Favorit"
            aria-label="Simpan ke Favorit"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>

        @unless ($isAvailable)
            <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center p-2 z-20">
                <span class="px-3 py-1 bg-rose-600 text-white text-xs font-bold rounded-lg shadow-2xs">
                    Stok Habis
                </span>
            </div>
        @endunless
    </div>

    <!-- Card Body -->
    <div class="p-4 flex-1 flex flex-col justify-between gap-3">
        <div class="space-y-1.5">
            <!-- Shop Attribution -->
            @if ($product->shop)
                <div class="flex items-center gap-1.5 text-xs text-slate-500 truncate">
                    <svg class="w-3.5 h-3.5 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="truncate font-medium text-slate-600 hover:text-slate-900">{{ $product->shop->name }}</span>
                </div>
            @endif

            <!-- Product Title (Consistent 2-Line Container Height) -->
            <h3 class="text-sm font-bold text-slate-900 group-hover:text-orange-600 transition line-clamp-2 leading-snug h-10">
                <a href="{{ route('products.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Rating and Review Count (Displayed Only When Real Data Is Available) -->
            @if (!empty($product->rating))
                <div class="flex items-center gap-1 pt-0.5 text-xs">
                    <svg class="w-3.5 h-3.5 fill-current text-amber-400 shrink-0" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-bold text-amber-500 text-xs">{{ number_format((float) $product->rating, 1) }}</span>
                    @if (!empty($product->reviews_count))
                        <span class="text-slate-400 text-xs">({{ $product->reviews_count }})</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Price, Stock Badge & Action Button -->
        <div class="pt-2 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <div class="text-base sm:text-lg font-black text-orange-600 tracking-tight">
                    Rp {{ $price }}
                </div>

                @if ($product->stock > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shrink-0">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Stok {{ $product->stock }}</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                        <span>Stok Habis</span>
                    </span>
                @endif
            </div>

            <!-- Action Button -->
            @if ($isAvailable)
                <a
                    href="{{ route('products.show', $product->slug) }}"
                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-orange-500 bg-white hover:bg-orange-50 text-orange-600 text-xs font-bold transition shadow-2xs"
                >
                    <svg class="w-4 h-4 shrink-0 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Tambah ke Keranjang</span>
                </a>
            @else
                <button
                    type="button"
                    disabled
                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed"
                >
                    <span>Stok Habis</span>
                </button>
            @endif
        </div>
    </div>
</div>
