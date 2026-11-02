@props([
    'product',
])

@php
    $price = number_format((float) $product->price, 0, ',', '.');
    $isAvailable = method_exists($product, 'isAvailable') ? $product->isAvailable() : ($product->stock > 0);
    $thumbnail = $product->thumbnail_url ?? asset('images/placeholder-product.svg');
@endphp

<div {{ $attributes->merge(['class' => 'group relative bg-white dark:bg-slate-900 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 hover:border-coral-400 dark:hover:border-coral-500 hover:shadow-card-hover transition-all duration-200 flex flex-col overflow-hidden h-full shadow-card']) }}>
    <!-- Product Thumbnail Container -->
    <div class="aspect-square w-full bg-[#F7F8FA] dark:bg-slate-800 overflow-hidden relative block border-b border-[#E8EAF0] dark:border-slate-800">
        <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
            <img
                src="{{ $thumbnail }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 ease-out"
            />
        </a>

        <!-- Floating Category Pill Badge (Top Left - Clickable Filter) -->
        @if ($product->category)
            <a
                href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 px-2 sm:px-2.5 py-0.5 bg-white/95 dark:bg-slate-900/95 hover:bg-white text-[10px] sm:text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:text-coral-600 rounded-full border border-[#E8EAF0] dark:border-slate-700 shadow-2xs z-20 transition truncate max-w-[75%] focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-500"
                title="Filter kategori {{ $product->category->name }}"
            >
                {{ $product->category->name }}
            </a>
        @endif

        @unless ($isAvailable)
            <div class="absolute inset-0 bg-slate-950/60 flex items-center justify-center p-2 z-20 backdrop-blur-2xs">
                <span class="px-2.5 sm:px-3 py-1 bg-rose-600 text-white text-[11px] sm:text-xs font-bold rounded-lg shadow-2xs">
                    Stok Habis
                </span>
            </div>
        @endunless
    </div>

    <!-- Card Body -->
    <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between gap-2.5 sm:gap-3">
        <div class="space-y-1.5">
            <!-- Shop Attribution -->
            @if ($product->shop)
                <div class="flex items-center gap-1.5 text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">
                    <svg class="w-3.5 h-3.5 text-coral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="truncate font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">{{ $product->shop->name }}</span>
                </div>
            @endif

            <!-- Product Title (Consistent 2-Line Container Height without font clipping) -->
            <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 group-hover:text-coral-600 dark:group-hover:text-coral-400 transition line-clamp-2 leading-snug min-h-[2.5rem]">
                <a href="{{ route('products.show', $product->slug) }}" class="focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-500 rounded">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Rating and Review Count (Displayed Only When Real Data Is Available) -->
            @if (!empty($product->rating) && (float) $product->rating > 0)
                <div class="flex items-center gap-1 pt-0.5 text-xs">
                    <svg class="w-3.5 h-3.5 fill-current text-amber-400 shrink-0" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-bold text-amber-500 text-xs">{{ number_format((float) $product->rating, 1) }}</span>
                    @if (!empty($product->reviews_count) && $product->reviews_count > 0)
                        <span class="text-slate-400 text-xs">({{ $product->reviews_count }})</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Price, Stock Badge & Action Button -->
        <div class="pt-2.5 border-t border-[#E8EAF0] dark:border-slate-800 space-y-2.5 sm:space-y-3">
            <div class="flex items-center justify-between gap-1.5">
                <div class="text-sm sm:text-base lg:text-lg font-black text-coral-600 dark:text-coral-400 tracking-tight truncate">
                    Rp {{ $price }}
                </div>

                @if ($product->stock > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60 shrink-0">
                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Stok {{ $product->stock }}</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60 shrink-0">
                        <span>Stok Habis</span>
                    </span>
                @endif
            </div>

            <!-- Action Button: Explicit "Lihat Detail" Action -->
            @if ($isAvailable)
                <a
                    href="{{ route('products.show', $product->slug) }}"
                    class="w-full inline-flex items-center justify-center gap-1.5 sm:gap-2 py-2 sm:py-2.5 px-3 rounded-xl border border-coral-500 dark:border-coral-600 bg-white dark:bg-slate-900 hover:bg-[#FFF0E9] dark:hover:bg-coral-950/40 text-coral-600 dark:text-coral-400 text-[11px] sm:text-xs font-semibold transition shadow-2xs focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-500 focus-visible:ring-offset-2"
                >
                    <span>Lihat Detail</span>
                    <svg class="w-3.5 h-3.5 shrink-0 text-coral-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            @else
                <div
                    class="w-full inline-flex items-center justify-center py-2 sm:py-2.5 px-3 rounded-xl border border-[#E8EAF0] dark:border-slate-800 bg-[#F7F8FA] dark:bg-slate-800 text-slate-400 text-[11px] sm:text-xs font-semibold select-none cursor-not-allowed"
                    aria-disabled="true"
                >
                    <span>Stok Habis</span>
                </div>
            @endif
        </div>
    </div>
</div>
