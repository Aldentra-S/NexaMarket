@extends('layouts.marketplace', ['title' => 'Detail Pesanan ' . $order->order_number . ' — NexaMarket'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb -->
    <nav class="flex text-xs text-gray-500 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li><a href="{{ url('/') }}" class="hover:text-emerald-600 transition">Beranda</a></li>
            <li>/</li>
            <li><a href="{{ route('orders.index') }}" class="hover:text-emerald-600 transition">Riwayat Pesanan</a></li>
            <li>/</li>
            <li class="font-bold text-gray-800">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Detail Pesanan</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $order->status->badgeColor() }}">
                    {{ $order->status->label() }}
                </span>
            </div>
            <p class="mt-2 text-sm text-gray-500 font-mono">
                No. Order: <span class="font-bold text-gray-800">{{ $order->order_number }}</span> &bull; Dibuat pada {{ $order->created_at->translatedFormat('d F Y, H:i') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition">
                &larr; Riwayat Pesanan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Column: Child Shop Orders & Items -->
        <div class="lg:col-span-8 space-y-6">
            @foreach($order->shopOrders as $shopOrder)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                    <!-- Shop Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-gray-400 font-mono block">Child Order: {{ $shopOrder->order_number }}</span>
                            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2 mt-0.5">
                                {{ $shopOrder->shop_name }}
                            </h3>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400 block">Subtotal Toko</span>
                            <span class="font-bold text-gray-900 text-sm">{{ $shopOrder->formattedSubtotal() }}</span>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="divide-y divide-gray-100 px-6">
                        @foreach($shopOrder->items as $item)
                            <div class="py-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 font-bold shrink-0">
                                        @if($item->product && $item->product->thumbnail_url)
                                            <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover rounded-xl">
                                        @else
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm">
                                            {{ $item->product_name }}
                                        </h4>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $item->quantity }} unit x {{ $item->formattedUnitPrice() }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-extrabold text-gray-900">
                                        {{ $item->formattedSubtotal() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if($order->notes)
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Catatan Pembeli</h4>
                    <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-xl">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Right Column: Order Summary & Payment Status -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm sticky top-28 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 pb-4 border-b border-gray-100">Ringkasan Tagihan</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Status Transaksi</span>
                    <span class="font-semibold text-gray-900">{{ $order->status->label() }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Jumlah Toko Mitra</span>
                    <span class="font-semibold text-gray-900">{{ $order->shopOrders->count() }} toko</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Total Kuantitas</span>
                    <span class="font-semibold text-gray-900">{{ $order->totalQuantity() }} barang</span>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                <div>
                    <span class="text-xs text-gray-500 block">Total Pembayaran</span>
                    <span class="text-2xl font-black text-emerald-600">{{ $order->formattedTotal() }}</span>
                </div>
            </div>

            <!-- Payment Simulation Container (Phase 5B Checkpoint B4) -->
            <div id="payment-action-container" class="pt-4 border-t border-gray-100">
                @if($order->isPending())
                    <div class="space-y-4">
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 space-y-1.5">
                            <div class="flex items-center gap-1.5 font-bold text-amber-900">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Menunggu Pembayaran
                            </div>
                            <p class="leading-relaxed">Selesaikan pembayaran lokal untuk memverifikasi pesanan Anda ke mitra toko.</p>
                        </div>

                        <!-- Success Simulation -->
                        <form action="{{ route('orders.payment.simulate', $order) }}" method="POST">
                            @csrf
                            <input type="hidden" name="result" value="success">
                            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-sm flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Bayar Sekarang (Simulasi Sukses)
                            </button>
                        </form>

                        <!-- Failure and Expired Testing Controls -->
                        <div class="pt-2 border-t border-gray-100 space-y-2">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400 block text-center">
                                Opsi Uji Coba Skenario (QA Mock)
                            </span>
                            <div class="grid grid-cols-2 gap-2">
                                <form action="{{ route('orders.payment.simulate', $order) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="result" value="failed">
                                    <button type="submit" class="w-full py-2 px-3 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition text-center">
                                        Simulasi Gagal
                                    </button>
                                </form>
                                <form action="{{ route('orders.payment.simulate', $order) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="result" value="expired">
                                    <button type="submit" class="w-full py-2 px-3 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 text-xs font-semibold transition text-center">
                                        Simulasi Expired
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="text-[11px] text-gray-400 text-center leading-tight">
                            * Simulasi pembayaran lokal NexaMarket (tanpa gateway uang riil).
                        </p>
                    </div>
                @elseif($order->isPaid())
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-bold text-emerald-900">Pembayaran Berhasil Diverifikasi</p>
                            <p class="mt-0.5 text-emerald-700">Lunas pada {{ $order->paid_at ? $order->paid_at->translatedFormat('d M Y, H:i') : '-' }} via Simulasi Pembayaran Lokal.</p>
                        </div>
                    </div>
                @elseif($order->isCancelled())
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                        <p class="font-bold text-rose-900">Pesanan Dibatalkan</p>
                        <p class="text-rose-700">Pembayaran tidak berhasil diselesaikan. Seluruh stok produk pesanan ini telah dipulihkan secara otomatis.</p>
                    </div>
                @elseif($order->isExpired())
                    <div class="p-4 rounded-xl bg-gray-100 border border-gray-200 text-xs text-gray-700 space-y-1">
                        <p class="font-bold text-gray-900">Pesanan Kedaluwarsa</p>
                        <p class="text-gray-600">Batas waktu pembayaran telah habis. Seluruh stok produk pesanan ini telah dipulihkan secara otomatis.</p>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-600">
                        Status pesanan: <span class="font-semibold">{{ $order->status->label() }}</span>.
                    </div>
                @endif
            </div>


            <div class="pt-2 text-center">
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    &larr; Belanja Produk Lainnya
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
