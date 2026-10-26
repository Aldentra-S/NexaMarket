<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Rekonsiliasi Refund &amp; Pembatalan
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantau pesanan yang dibatalkan atau kedaluwarsa serta kelayakan pengembalian dana pembeli</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Refund Summary Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Estimasi Refund Terbayar</div>
                    <div class="text-xl sm:text-2xl font-bold text-rose-700 mt-2">
                        Rp {{ number_format($paidAndCancelledAmount, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Pesanan berstatus batal yang telah terbayar</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Total Pesanan Dibatalkan</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ $totalCancelledCount }} <span class="text-xs font-normal text-slate-500">pesanan</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Dibatalkan pembeli atau sistem</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Tagihan Kedaluwarsa</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ $totalExpiredCount }} <span class="text-xs font-normal text-slate-500">pesanan</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Batas waktu pembayaran Midtrans habis</p>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <form method="GET" action="{{ route('admin.refunds.index') }}" class="flex-1 max-w-md">
                    @if ($currentStatus)
                        <input type="hidden" name="status" value="{{ $currentStatus }}" />
                    @endif
                    <div class="relative">
                        <input 
                            type="search" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari no. pesanan atau nama pembeli..." 
                            class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-xl focus:border-emerald-700 focus:ring-emerald-700 text-slate-900" 
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('admin.refunds.index', ['status' => 'cancelled', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'cancelled' ? 'bg-rose-700 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Pesanan Dibatalkan ({{ $totalCancelledCount }})
                    </a>
                    <a href="{{ route('admin.refunds.index', ['status' => 'expired', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'expired' ? 'bg-slate-800 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Pesanan Kedaluwarsa ({{ $totalExpiredCount }})
                    </a>
                </div>
            </div>

            <!-- Refunds Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[650px] w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">No. Pesanan</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Pembeli</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Toko Terkait</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Total Transaksi</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status Pembayaran</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Waktu Pembatalan</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ $order->user->name ?? 'User' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $order->user->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                        @foreach($order->shopOrders as $so)
                                            <span class="inline-block px-2 py-0.5 bg-slate-100 rounded text-[11px] mr-1">
                                                {{ $so->shop_name ?? ($so->shop->name ?? 'Toko') }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-slate-900">
                                        {{ $order->formattedTotal() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($order->paid_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Sudah Terbayar (Perlu Refund)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                Belum Terbayar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                        {{ $order->cancelled_at ? $order->cancelled_at->format('d M Y, H:i') : $order->updated_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition">
                                            Periksa &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <p class="text-sm font-medium">Tidak ada pesanan batal atau kedaluwarsa yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
