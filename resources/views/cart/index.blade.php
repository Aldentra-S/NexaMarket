<x-marketplace-layout>
    <x-slot name="title">
        Keranjang Belanja | NexaMarket
    </x-slot>

    <div class="py-6 sm:py-10 bg-slate-50 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Page Title -->
            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-orange-50 text-orange-700 text-xs font-bold border border-orange-200">
                    <span>Keranjang Belanja NexaMarket</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Keranjang Belanja Anda</h1>
                <p class="text-xs sm:text-sm text-slate-500">Kelola daftar produk pilihan Anda sebelum menyelesaikan pembayaran terpadu.</p>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 flex items-center justify-between text-xs sm:text-sm shadow-2xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
                    <ul class="list-disc list-inside font-medium space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($hasUnavailableItems)
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 text-xs sm:text-sm shadow-2xs">
                    <svg class="w-5 h-5 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold">Perhatian: Ketersediaan Produk Berubah</p>
                        <p class="mt-0.5 text-amber-800">Beberapa produk di keranjang Anda mengalami perubahan stok atau status toko. Harap sesuaikan kuantitas atau hapus produk yang tidak tersedia.</p>
                    </div>
                </div>
            @endif

            @if($items->isEmpty())
                <div class="bg-white rounded-3xl border border-slate-200/90 p-8 sm:p-14 text-center max-w-lg mx-auto space-y-4 shadow-2xs">
                    <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mx-auto shadow-2xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">Keranjang belanja Anda masih kosong</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">Jelajahi berbagai produk terbaik dari toko mitra terverifikasi di NexaMarket sekarang.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-2.5 rounded-xl bg-orange-600 text-white font-semibold text-xs hover:bg-orange-700 transition shadow-2xs">
                            Mulai Berbelanja
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Items List (Span 8) -->
                    <div class="lg:col-span-8 space-y-4">
                        @foreach($items as $item)
                            @php
                                $isAvailable = $item->isAvailable();
                                $hasStockIssue = $item->product && $item->product->stock < $item->quantity;
                                $hasStatusIssue = $item->product && ! $item->product->isPublished();
                                $hasShopIssue = $item->product && ($item->product->shop->isSuspended() || ! $item->product->shop->isApproved());
                            @endphp
                            <div class="bg-white rounded-3xl border {{ $isAvailable ? 'border-slate-200/90 shadow-2xs' : 'border-amber-300 bg-amber-50/20' }} p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4 min-w-0">
                                    <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-20 h-20 sm:w-22 sm:h-22 object-cover rounded-2xl border border-slate-100 shrink-0">
                                    <div class="min-w-0 space-y-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <span class="text-xs font-semibold text-orange-700 bg-orange-50 border border-orange-200 px-2.5 py-0.5 rounded-full inline-block truncate">
                                                {{ $item->product->shop->name ?? 'Toko Mitra' }}
                                            </span>
                                            @if(! $isAvailable)
                                                @if($hasStockIssue)
                                                    <span class="text-xs font-medium text-amber-800 bg-amber-100 border border-amber-200 px-2 py-0.5 rounded-md inline-block">
                                                        Stok Tidak Mencukupi (Tersedia {{ $item->product->stock }} unit)
                                                    </span>
                                                @elseif($hasStatusIssue)
                                                    <span class="text-xs font-medium text-rose-800 bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-md inline-block">
                                                        Produk Tidak Aktif
                                                    </span>
                                                @elseif($hasShopIssue)
                                                    <span class="text-xs font-medium text-rose-800 bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-md inline-block">
                                                        Toko Tidak Aktif
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        <h3 class="font-bold text-slate-900 text-sm sm:text-base line-clamp-1 hover:text-orange-600 transition">
                                            <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                        </h3>
                                        <p class="text-xs font-black text-orange-600">
                                            Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Action Buttons & Quantity Stepper -->
                                <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                    @if($isAvailable)
                                        <form action="{{ route('cart.items.update', $item) }}" method="POST" class="inline-flex items-center border border-slate-300 rounded-xl overflow-hidden bg-white">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="px-2.5 py-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold text-sm transition" title="Kurangi">
                                                &minus;
                                            </button>
                                            <span class="w-10 text-center font-bold text-xs text-slate-900 select-none">
                                                {{ $item->quantity }}
                                            </span>
                                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" @disabled($item->quantity >= $item->product->stock) class="px-2.5 py-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold text-sm transition disabled:opacity-40 disabled:cursor-not-allowed" title="Tambah">
                                                +
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete Item Button -->
                                    <form action="{{ route('cart.items.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Hapus dari keranjang">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Right Column: Sticky Summary Card (Span 4) -->
                    <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-4">
                        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 space-y-5 shadow-2xs">
                            <h2 class="text-base font-bold text-slate-900">Ringkasan Belanja</h2>

                            <div class="space-y-3 text-xs">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Total Item:</span>
                                    <span class="font-bold text-slate-900">{{ $totalQuantity }} barang</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Subtotal Produk:</span>
                                    <span class="font-bold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-sm font-bold text-slate-900">Total Tagihan:</span>
                                    <span class="text-xl font-black text-orange-600">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2">
                                @if($hasUnavailableItems)
                                    <button disabled class="w-full py-3.5 px-4 rounded-xl bg-slate-200 text-slate-400 font-bold text-xs sm:text-sm cursor-not-allowed">
                                        Perbaiki Item Bermasalah
                                    </button>
                                @else
                                    <a href="{{ route('checkout.create') }}" class="w-full inline-flex items-center justify-center py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm transition shadow-2xs">
                                        Lanjut ke Checkout &rarr;
                                    </a>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-slate-100 text-center">
                                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-600 transition">
                                    &larr; Lanjutkan Memilih Produk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-marketplace-layout>
