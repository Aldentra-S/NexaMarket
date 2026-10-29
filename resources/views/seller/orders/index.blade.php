<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Daftar Pesanan Toko') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantau pesanan masuk untuk {{ $shop->name }}, periksa status pembayaran, dan kelola proses pemenuhan</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Filter Tabs -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('seller.orders.index') }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ empty($currentStatus) ? 'bg-orange-600 text-white' : 'bg-slate-50 text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        Semua Status
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'paid']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $currentStatus === 'paid' ? 'bg-orange-600 text-white' : 'bg-slate-50 text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        Perlu Diproses
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'processing']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $currentStatus === 'processing' ? 'bg-sky-700 text-white' : 'bg-slate-50 text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        Sedang Diproses
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'shipped']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $currentStatus === 'shipped' ? 'bg-slate-800 text-white' : 'bg-slate-50 text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        Sedang Dikirim
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'completed']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $currentStatus === 'completed' ? 'bg-emerald-800 text-white' : 'bg-slate-50 text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        Selesai
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'pending']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $currentStatus === 'pending' ? 'bg-amber-700 text-white' : 'bg-slate-50 text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        Belum Dibayar
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'cancelled']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 {{ $currentStatus === 'cancelled' ? 'bg-rose-700 text-white' : 'bg-slate-50 text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        Dibatalkan
                    </a>
                </div>
            </div>

            <!-- Orders Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                @if($orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-[700px] w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 font-semibold">
                                    <th class="py-4 px-6">No. Pesanan Toko</th>
                                    <th class="py-4 px-6">Tanggal & Pembeli</th>
                                    <th class="py-4 px-6">Item Produk</th>
                                    <th class="py-4 px-6 text-right">Subtotal</th>
                                    <th class="py-4 px-6 text-center">Status</th>
                                    <th class="py-4 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($orders as $shopOrder)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-4 px-6 font-bold text-slate-900">
                                            <div>{{ $shopOrder->order_number }}</div>
                                            <div class="text-[11px] text-slate-400 font-normal">
                                                Parent: {{ $shopOrder->order->order_number ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-medium text-slate-900">
                                                {{ $shopOrder->order->user->name ?? 'Pembeli' }}
                                            </div>
                                            <div class="text-slate-400 text-[11px]">
                                                {{ $shopOrder->created_at->translatedFormat('d M Y, H:i') }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="space-y-1">
                                                @foreach($shopOrder->items as $item)
                                                    <div class="text-slate-700">
                                                        <span class="font-semibold">{{ $item->quantity }}x</span> {{ $item->product_name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-right font-bold text-slate-900">
                                            {{ $shopOrder->formattedSubtotal() }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $shopOrder->status->badgeColor() }}">
                                                {{ $shopOrder->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="{{ route('seller.orders.show', $shopOrder) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                                                Kelola &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="p-4 border-t border-slate-100">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center text-slate-500">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-800">Belum Ada Pesanan</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Belum ada pesanan yang sesuai dengan filter yang dipilih saat ini. Tingkatkan transaksi toko Anda dengan mengelola produk atau melihat katalog publik.</p>
                        <div class="mt-4 flex flex-wrap items-center justify-center gap-2.5">
                            <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition shadow-xs focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2">
                                <span>Kelola Produk Toko</span>
                            </a>
                            <a href="{{ route('products.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                                <span>Lihat Katalog Publik</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
