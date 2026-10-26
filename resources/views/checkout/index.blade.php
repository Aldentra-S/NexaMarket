<x-marketplace-layout>
    <x-slot name="title">
        Checkout Pesanan | NexaMarket
    </x-slot>

    <div class="py-8 sm:py-10 bg-slate-50 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumbs -->
            <nav class="flex items-center text-xs text-slate-500 gap-2">
                <a href="{{ url('/') }}" class="hover:text-orange-600 transition">Beranda</a>
                <span class="text-slate-400">&rsaquo;</span>
                <a href="{{ route('cart.index') }}" class="hover:text-orange-600 transition">Keranjang</a>
                <span class="text-slate-400">&rsaquo;</span>
                <span class="font-semibold text-slate-900">Checkout</span>
            </nav>

            <!-- Page Title -->
            <div class="space-y-1">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Konfirmasi & Checkout Pesanan</h1>
                <p class="text-xs sm:text-sm text-slate-600">Periksa rincian produk dari masing-masing toko mitra sebelum menyelesaikan pembuatan pesanan.</p>
            </div>

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 text-xs sm:text-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if($hasUnavailableItems)
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 text-xs sm:text-sm">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold">Beberapa item tidak memenuhi syarat checkout</p>
                        <p class="mt-0.5 text-amber-800">Terdapat produk yang stoknya habis atau status tokonya tidak aktif. Silakan kembali ke <a href="{{ route('cart.index') }}" class="underline font-semibold text-orange-600 hover:text-orange-700">Keranjang Belanja</a> untuk menyesuaikan.</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
                    <!-- Left Column: Multi-Vendor Grouped Orders -->
                    <div class="lg:col-span-8 space-y-6">
                        @foreach($groupedItems as $shopId => $vendorItems)
                            @php
                                $shop = $vendorItems->first()->product->shop;
                                $vendorSubtotal = $vendorItems->sum(fn ($i) => (float) bcmul((string) $i->product->price, (string) $i->quantity, 2));
                            @endphp
                            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                                <!-- Shop Header -->
                                <div class="bg-orange-50/60 px-4 sm:px-6 py-3.5 border-b border-orange-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-orange-600 text-white font-bold flex items-center justify-center text-sm shrink-0">
                                            {{ strtoupper(substr($shop->name ?? 'T', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-slate-900 text-sm flex flex-wrap items-center gap-2">
                                                <span class="truncate">{{ $shop->name ?? 'Toko Mitra' }}</span>
                                                <span class="text-[11px] bg-orange-100 text-orange-700 font-semibold px-2 py-0.5 rounded-full border border-orange-200 shrink-0">Mitra Resmi</span>
                                            </h3>
                                            <p class="text-xs text-slate-500 truncate">{{ $shop->slug ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right shrink-0">
                                        <span class="text-xs text-slate-500">Subtotal:</span>
                                        <span class="font-bold text-orange-600 text-sm ml-1">Rp {{ number_format($vendorSubtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Shop Items -->
                                <div class="divide-y divide-slate-100 px-4 sm:px-6">
                                    @foreach($vendorItems as $item)
                                        @php
                                            $itemSubtotal = (float) bcmul((string) $item->product->price, (string) $item->quantity, 2);
                                        @endphp
                                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-center gap-4 min-w-0">
                                                <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-xl border border-slate-200 shrink-0 bg-slate-50">
                                                <div class="min-w-0">
                                                    <h4 class="font-bold text-slate-900 text-sm truncate">
                                                        {{ $item->product->name }}
                                                    </h4>
                                                    <div class="text-xs text-slate-500 mt-0.5">
                                                        {{ $item->quantity }} x {{ $item->product->formattedPrice() }}
                                                    </div>
                                                    @if($item->product->stock < $item->quantity)
                                                        <span class="inline-block mt-1 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                                            Stok sisa {{ $item->product->stock }} unit
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="text-sm font-black text-slate-900">
                                                    Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <!-- Notes Box -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 space-y-2.5">
                            <label for="notes" class="block text-sm font-bold text-slate-900">
                                Catatan Pesanan <span class="font-normal text-xs text-slate-500">(Opsional)</span>
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="Contoh: Harap kemas dengan rapi, titipkan di pos satpam jika tidak ada orang."
                                class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 placeholder:text-slate-400"
                            >{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Right Column: Final Order Summary -->
                    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 sticky top-28 space-y-5">
                        <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                            <span>Ringkasan Pembayaran</span>
                            <span class="text-xs font-normal text-slate-500">{{ $groupedItems->count() }} Toko Mitra</span>
                        </h2>

                        <div class="space-y-3 text-xs sm:text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>Total Barang</span>
                                <span class="font-semibold text-slate-900">{{ $totalQuantity }} unit</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Jumlah Toko Mitra</span>
                                <span class="font-semibold text-slate-900">{{ $groupedItems->count() }} toko</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal Pesanan</span>
                                <span class="font-semibold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-xs text-slate-500 block">Total Tagihan Final</span>
                            <span class="text-2xl sm:text-3xl font-black text-orange-600 block mt-0.5">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-1">
                            @if($hasUnavailableItems)
                                <button type="button" disabled class="w-full py-3.5 px-4 rounded-xl bg-slate-200 text-slate-400 font-bold text-sm text-center cursor-not-allowed">
                                    Tidak Dapat Melanjutkan
                                </button>
                                <p class="text-xs text-center text-rose-600 mt-2">Perbaiki item di keranjang sebelum checkout.</p>
                            @else
                                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white font-bold text-sm text-center transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Buat Pesanan & Bayar
                                </button>
                            @endif
                        </div>

                        <div class="text-center pt-2">
                            <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-600 transition">
                                &larr; Kembali ke Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-marketplace-layout>
