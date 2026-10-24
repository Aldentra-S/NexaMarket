@extends('layouts.marketplace', ['title' => 'Riwayat Pesanan Saya — NexaMarket'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat Pesanan Saya</h1>
            <p class="mt-2 text-sm text-gray-500">Pantau dan kelola seluruh transaksi pembelian Anda di NexaMarket.</p>
        </div>
        <div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs rounded-xl transition">
                &larr; Lanjut Belanja
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

    @if($orders->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm max-w-lg mx-auto">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Pesanan</h3>
            <p class="text-sm text-gray-500 mb-6">Anda belum pernah melakukan checkout pesanan. Temukan produk impian Anda sekarang.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition shadow-sm">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:border-emerald-300 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono font-bold text-gray-700 bg-gray-100 px-3 py-1 rounded-lg">
                                {{ $order->order_number }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                            </span>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->status->badgeColor() }}">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                    </div>

                    <div class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-xs text-gray-500">
                                Terdiri dari <span class="font-bold text-gray-800">{{ $order->shopOrders->count() }} Toko Mitra</span>:
                                {{ $order->shopOrders->pluck('shop_name')->join(', ') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Total Kuantitas: <span class="font-bold text-gray-900">{{ $order->totalQuantity() }} unit barang</span>
                            </p>
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-6 pt-2 md:pt-0">
                            <div class="text-left md:text-right">
                                <span class="text-xs text-gray-400 block">Total Tagihan</span>
                                <span class="text-xl font-black text-emerald-600">{{ $order->formattedTotal() }}</span>
                            </div>
                            <div>
                                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-900 hover:bg-emerald-600 text-white font-semibold text-xs transition shadow-xs">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
