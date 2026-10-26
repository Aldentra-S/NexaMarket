<x-marketplace-layout>
    <x-slot name="title">
        Riwayat Pesanan Saya | NexaMarket
    </x-slot>

    <div class="py-8 sm:py-10 bg-slate-50 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Riwayat Pesanan Saya</h1>
                    <p class="text-xs sm:text-sm text-slate-600">Pantau status transaksi belanja dan pelacakan pesanan Anda di NexaMarket.</p>
                </div>
                <div>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:text-orange-600 hover:border-orange-300 font-semibold text-xs rounded-xl transition">
                        <span>&larr;</span> Lanjut Belanja
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($orders->isEmpty())
                <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 text-center max-w-md mx-auto space-y-4">
                    <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">Belum Ada Pesanan</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">Anda belum pernah melakukan checkout pesanan. Temukan produk pilihan Anda sekarang.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-orange-600 text-white font-bold text-xs hover:bg-orange-700 transition">
                            Mulai Belanja
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 hover:border-orange-300 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="text-xs font-bold text-slate-900 bg-slate-100 px-3 py-1 rounded-lg">
                                        {{ $order->order_number }}
                                    </span>
                                    <span class="text-xs text-slate-500">
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
                                    <p class="text-xs text-slate-500">
                                        Terdiri dari <span class="font-semibold text-slate-800">{{ $order->shopOrders->count() }} Toko Mitra</span>:
                                        {{ $order->shopOrders->pluck('shop_name')->join(', ') }}
                                    </p>
                                    <p class="text-xs sm:text-sm text-slate-600">
                                        Total Kuantitas: <span class="font-semibold text-slate-900">{{ $order->totalQuantity() }} unit barang</span>
                                    </p>
                                </div>

                                <div class="flex items-center justify-between md:justify-end gap-6 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                                    <div class="text-left md:text-right">
                                        <span class="text-[11px] text-slate-400 block font-medium">Total Tagihan</span>
                                        <span class="text-lg sm:text-xl font-black text-orange-600">{{ $order->formattedTotal() }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-orange-600 text-white font-bold text-xs transition">
                                            <span>Lihat Detail</span> &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($orders->hasPages())
                        <div class="bg-white p-4 rounded-2xl border border-slate-200">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-marketplace-layout>
