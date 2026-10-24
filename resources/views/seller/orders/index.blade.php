<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-600 transition">Seller Center</a>
                    <span>&rsaquo;</span>
                    <span class="text-gray-800 font-medium">Pesanan Toko</span>
                </div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    {{ __('Daftar Pesanan Toko') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Pantau pesanan masuk untuk {{ $shop->name }}, periksa status pembayaran, dan kelola proses pemenuhan</p>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    {{ $shop->name }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Filter Tabs -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('seller.orders.index') }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ empty($currentStatus) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Semua Status
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'paid']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ $currentStatus === 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Perlu Diproses
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'pending']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ $currentStatus === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Belum Dibayar
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'completed']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ $currentStatus === 'completed' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Selesai
                    </a>
                    <a href="{{ route('seller.orders.index', ['status' => 'cancelled']) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ $currentStatus === 'cancelled' ? 'bg-rose-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Dibatalkan
                    </a>
                </div>
            </div>

            <!-- Orders Table Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                @if($orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 uppercase tracking-wider font-bold">
                                    <th class="py-4 px-6">No. Pesanan Toko</th>
                                    <th class="py-4 px-6">Tanggal & Pembeli</th>
                                    <th class="py-4 px-6">Item Produk</th>
                                    <th class="py-4 px-6 text-right">Subtotal</th>
                                    <th class="py-4 px-6 text-center">Status</th>
                                    <th class="py-4 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($orders as $shopOrder)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-4 px-6 font-mono font-bold text-gray-900">
                                            <div>{{ $shopOrder->order_number }}</div>
                                            <div class="text-[11px] text-gray-400 font-normal">
                                                Parent: {{ $shopOrder->order->order_number ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-medium text-gray-900">
                                                {{ $shopOrder->order->user->name ?? 'Pembeli' }}
                                            </div>
                                            <div class="text-gray-400 text-[11px]">
                                                {{ $shopOrder->created_at->translatedFormat('d M Y, H:i') }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="space-y-1">
                                                @foreach($shopOrder->items as $item)
                                                    <div class="text-gray-800">
                                                        <span class="font-semibold">{{ $item->product_name }}</span>
                                                        <span class="text-gray-500 font-mono">(&times;{{ $item->quantity }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-right font-bold text-gray-900 text-sm">
                                            {{ $shopOrder->formattedSubtotal() }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $shopOrder->status->badgeColor() }}">
                                                {{ $shopOrder->status->label() }}
                                            </span>
                                            @if($shopOrder->status->isPending())
                                                <div class="text-[10px] text-amber-600 font-medium mt-1">
                                                    Belum siap diproses
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="{{ route('seller.orders.show', $shopOrder) }}" 
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition">
                                                Detail Pesanan &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($orders->hasPages())
                        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @else
                    <div class="py-16 text-center">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 mb-1">Belum Ada Pesanan</h3>
                        <p class="text-xs text-gray-500 max-w-sm mx-auto">
                            Pesanan dari pembeli untuk toko Anda akan muncul di halaman ini setelah proses checkout selesai.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
