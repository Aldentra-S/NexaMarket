@extends('layouts.marketplace', ['title' => 'Checkout Pesanan — NexaMarket'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb -->
    <nav class="flex text-xs text-gray-500 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li><a href="{{ url('/') }}" class="hover:text-emerald-600 transition">Beranda</a></li>
            <li>/</li>
            <li><a href="{{ route('cart.index') }}" class="hover:text-emerald-600 transition">Keranjang</a></li>
            <li>/</li>
            <li class="font-bold text-gray-800">Checkout</li>
        </ol>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Konfirmasi & Checkout Pesanan</h1>
        <p class="mt-2 text-sm text-gray-500">Periksa kembali rincian produk dari masing-masing toko mitra sebelum menyelesaikan pembuatan pesanan.</p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm font-medium">{{ session('error') }}</div>
        </div>
    @endif

    @if($hasUnavailableItems)
        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm">
                <p class="font-bold">Beberapa item tidak memenuhi syarat checkout</p>
                <p class="mt-0.5 text-amber-800">Terdapat produk yang stoknya habis atau status tokonya tidak aktif. Silakan kembali ke <a href="{{ route('cart.index') }}" class="underline font-semibold text-emerald-700">Keranjang Belanja</a> untuk menyesuaikan.</p>
            </div>
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left Column: Multi-Vendor Grouped Orders -->
            <div class="lg:col-span-8 space-y-6">
                @foreach($groupedItems as $shopId => $vendorItems)
                    @php
                        $shop = $vendorItems->first()->product->shop;
                        $vendorSubtotal = $vendorItems->sum(fn ($i) => (float) bcmul((string) $i->product->price, (string) $i->quantity, 2));
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                        <!-- Shop Header -->
                        <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-extrabold flex items-center justify-center text-sm shadow-xs">
                                    {{ strtoupper(substr($shop->name ?? 'T', 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                        {{ $shop->name ?? 'Toko Mitra' }}
                                        <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">Mitra Resmi</span>
                                    </h3>
                                    <p class="text-xs text-gray-500 font-mono">{{ $shop->slug ?? '' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Subtotal Toko:</span>
                                <span class="font-bold text-gray-900 text-sm ml-1">Rp {{ number_format($vendorSubtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Shop Items -->
                        <div class="divide-y divide-gray-100 px-6">
                            @foreach($vendorItems as $item)
                                @php
                                    $itemSubtotal = (float) bcmul((string) $item->product->price, (string) $item->quantity, 2);
                                @endphp
                                <div class="py-4 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-xl border border-gray-100 shrink-0">
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm line-clamp-1">
                                                {{ $item->product->name }}
                                            </h4>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                {{ $item->quantity }} x {{ $item->product->formattedPrice() }}
                                            </div>
                                            @if($item->product->stock < $item->quantity)
                                                <span class="inline-block mt-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                                                    Stok sisa {{ $item->product->stock }} unit
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="text-sm font-extrabold text-gray-900">
                                            Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Notes Box -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <label for="notes" class="block text-sm font-bold text-gray-900 mb-2">
                        Catatan Pesanan (Opsional)
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        placeholder="Contoh: Harap kemas dengan bubble wrap tebal, titipkan di pos satpam jika tidak ada orang."
                        class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 placeholder-gray-400"
                    >{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Right Column: Final Order Summary -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm sticky top-28 space-y-6">
                <h2 class="text-lg font-bold text-gray-900 pb-4 border-b border-gray-100">Ringkasan Pembayaran</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Total Barang</span>
                        <span class="font-semibold text-gray-900">{{ $totalQuantity }} unit</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Jumlah Toko Mitra</span>
                        <span class="font-semibold text-gray-900">{{ $groupedItems->count() }} toko</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Harga Database Terkini</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <span class="text-xs text-gray-500 block">Total Tagihan Final</span>
                        <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    @if($hasUnavailableItems)
                        <button type="button" disabled class="w-full py-3.5 px-4 rounded-xl bg-gray-200 text-gray-400 font-bold text-center cursor-not-allowed">
                            Tidak Dapat Melanjutkan
                        </button>
                        <p class="text-xs text-center text-rose-600 mt-2">Perbaiki item di keranjang sebelum checkout.</p>
                    @else
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-center transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Buat Pesanan & Bayar
                        </button>
                    @endif
                </div>

                <div class="text-center">
                    <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-gray-500 hover:text-emerald-600 transition">
                        &larr; Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
