@extends('layouts.marketplace', ['title' => 'Keranjang Belanja — NexaMarket'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Keranjang Belanja</h1>
        <p class="mt-2 text-sm text-gray-500">Kelola daftar produk pilihan Anda sebelum melanjutkan ke proses pembayaran.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
            <ul class="list-disc list-inside text-sm font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($hasUnavailableItems)
        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm">
                <p class="font-bold">Perhatian: Ketersediaan Produk Berubah</p>
                <p class="mt-0.5 text-amber-800">Beberapa produk di keranjang Anda mengalami perubahan stok atau status toko. Harap sesuaikan kuantitas atau hapus produk yang tidak tersedia.</p>
            </div>
        </div>
    @endif

    @if($items->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm max-w-lg mx-auto">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Keranjang belanja Anda masih kosong</h3>
            <p class="text-sm text-gray-500 mb-6">Jelajahi berbagai produk terbaik dari toko-toko terpercaya di NexaMarket sekarang.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition shadow-sm">
                Mulai Berbelanja
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Items List -->
            <div class="lg:col-span-8 space-y-4">
                @foreach($items as $item)
                    @php
                        $isAvailable = $item->isAvailable();
                        $hasStockIssue = $item->product && $item->product->stock < $item->quantity;
                        $hasStatusIssue = $item->product && ! $item->product->isPublished();
                        $hasShopIssue = $item->product && ($item->product->shop->isSuspended() || ! $item->product->shop->isApproved());
                    @endphp
                    <div class="bg-white rounded-2xl border {{ $isAvailable ? 'border-gray-200' : 'border-amber-300 bg-amber-50/20' }} p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-xl border border-gray-100 shrink-0">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full inline-block">
                                        {{ $item->product->shop->name ?? 'Toko' }}
                                    </span>
                                    @if(! $isAvailable)
                                        @if($hasStockIssue)
                                            <span class="text-[11px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-md inline-block">
                                                Stok Tidak Mencukupi (Tersedia {{ $item->product->stock }} unit)
                                            </span>
                                        @elseif($hasStatusIssue)
                                            <span class="text-[11px] font-bold text-rose-800 bg-rose-100 px-2 py-0.5 rounded-md inline-block">
                                                Produk Tidak Aktif
                                            </span>
                                        @elseif($hasShopIssue)
                                            <span class="text-[11px] font-bold text-rose-800 bg-rose-100 px-2 py-0.5 rounded-md inline-block">
                                                Toko Tidak Aktif
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <h3 class="font-bold text-gray-900 line-clamp-1 hover:text-emerald-600 transition">
                                    <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                </h3>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $item->product->formattedPrice() }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                            <!-- Update Quantity Form -->
                            <form action="{{ route('cart.items.update', $item) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input
                                    type="number"
                                    name="quantity"
                                    value="{{ $item->quantity }}"
                                    min="1"
                                    max="{{ max(1, $item->product->stock) }}"
                                    class="w-16 text-center text-sm font-semibold rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                >
                                <button type="submit" class="text-xs font-medium text-emerald-600 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1.5 rounded-lg transition">
                                    Ubah
                                </button>
                            </form>

                            <div class="text-right">
                                <div class="text-xs text-gray-400">Subtotal</div>
                                <div class="text-base font-extrabold text-gray-900">
                                    Rp {{ number_format($item->subtotal(), 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Delete Form -->
                            <form action="{{ route('cart.items.destroy', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-rose-600 p-2 rounded-lg hover:bg-rose-50 transition" title="Hapus produk">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm sticky top-28">
                <h2 class="text-lg font-bold text-gray-900 pb-4 border-b border-gray-100">Ringkasan Belanja</h2>
                
                <div class="py-4 space-y-3 text-sm border-b border-gray-100">
                    <div class="flex justify-between text-gray-600">
                        <span>Total Kuantitas</span>
                        <span class="font-semibold text-gray-900">{{ $totalQuantity }} barang</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Total Harga Produk</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="py-4 flex justify-between items-center">
                    <span class="text-base font-bold text-gray-900">Total Sementara</span>
                    <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <div class="mt-4">
                    @if($hasUnavailableItems)
                        <button type="button" disabled class="w-full py-3.5 px-4 rounded-xl bg-gray-200 text-gray-400 font-bold text-center cursor-not-allowed">
                            Lanjut ke Checkout
                        </button>
                        <p class="text-xs text-center text-amber-700 mt-2 font-medium">Harap perbaiki item yang tidak tersedia untuk melanjutkan ke checkout.</p>
                    @else
                        <a href="{{ route('checkout.create') }}" class="block w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-center transition shadow-sm">
                            Lanjut ke Checkout &rarr;
                        </a>
                        <p class="text-xs text-center text-gray-400 mt-2">Periksa rincian per toko dan selesaikan pesanan Anda.</p>
                    @endif
                </div>

            </div>
        </div>
    @endif
</div>
@endsection
