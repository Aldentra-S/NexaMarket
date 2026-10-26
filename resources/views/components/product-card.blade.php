@props([
    'product',
])

@php
    $price = number_format((float) $product->price, 0, ',', '.');
    $isAvailable = method_exists($product, 'isAvailable') ? $product->isAvailable() : ($product->stock > 0);
    $thumbnail = $product->thumbnail_url ?? asset('images/placeholder-product.svg');
@endphp

<div {{ $attributes->merge(['class' => 'group relative bg-white rounded-2xl border border-slate-200/90 hover:border-orange-500 transition-colors duration-200 flex flex-col overflow-hidden h-full']) }}>
    <!-- Product Thumbnail Container -->
    <div class="aspect-square w-full bg-slate-50 overflow-hidden relative block border-b border-slate-100">
        <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
            <img
                src="{{ $thumbnail }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-200 ease-out"
            />
        </a>

        <!-- Wishlist Button (Shopcart signature) -->
        <button
            type="button"
            class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-400 hover:text-rose-500 border border-slate-200/80 flex items-center justify-center transition"
            title="Simpan ke Wishlist"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>

        @unless ($isAvailable)
            <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center p-2 z-10">
                <span class="px-3 py-1 bg-rose-600 text-white text-xs font-bold rounded-lg">
                    Stok Habis
                </span>
            </div>
        @endunless

        @if ($product->category)
            <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 bg-white/95 text-[11px] font-semibold text-slate-700 rounded-md border border-slate-200">
                {{ $product->category->name }}
            </span>
        @endif
    </div>

    <!-- Card Body -->
    <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between gap-3">
        <div class="space-y-1.5">
            <!-- Shop attribution -->
            @if ($product->shop)
                <div class="flex items-center gap-1.5 text-xs text-slate-500 truncate">
                    <svg class="w-3.5 h-3.5 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="truncate font-medium text-slate-600 hover:text-slate-900">{{ $product->shop->name }}</span>
                </div>
            @endif

            <!-- Product Title -->
            <h3 class="text-sm font-semibold text-slate-900 group-hover:text-orange-600 transition line-clamp-2 leading-snug">
                <a href="{{ route('products.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Rating badge (Shopcart style ⭐ 4.8) -->
            <div class="flex items-center gap-1.5 pt-0.5 text-xs">
                <div class="flex items-center text-amber-500 font-semibold text-[11px]">
                    <svg class="w-3.5 h-3.5 fill-current text-amber-400 mr-0.5" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>4.8</span>
                </div>
                <span class="text-slate-300">&bull;</span>
                <span class="text-slate-500 text-[11px]">Terverifikasi</span>
            </div>
        </div>

        <!-- Price & Action Section -->
        <div class="pt-2.5 border-t border-slate-100 space-y-2.5">
            <div class="flex flex-wrap items-baseline justify-between gap-1.5">
                <div>
                    <span class="text-xs text-orange-600 font-semibold">Rp</span>
                    <span class="text-base sm:text-lg font-black text-orange-600 tracking-tight">{{ $price }}</span>
                </div>

                @if ($product->stock > 0 && $product->stock <= 5)
                    <span class="text-[11px] font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 shrink-0">
                        Sisa {{ $product->stock }}
                    </span>
                @elseif ($product->stock > 5)
                    <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100 shrink-0">
                        Stok Ada
                    </span>
                @endif
            </div>

            <!-- Shopcart Action Button -->
            <a
                href="{{ route('products.show', $product->slug) }}"
                class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl border border-orange-600 text-orange-600 hover:bg-orange-600 hover:text-white text-xs font-bold transition"
            >
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="hidden sm:inline">+ Tambah ke Keranjang</span>
                <span class="sm:hidden">+ Keranjang</span>
            </a>
        </div>
    </div>
</div>
