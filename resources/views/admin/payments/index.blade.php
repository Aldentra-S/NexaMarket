<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Pusat Pembayaran Gateway
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Audit rekonsiliasi pembayaran digital, transaksi Midtrans Snap, dan settlement perbankan</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Payment Metrics Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Total Settlement Berhasil</div>
                    <div class="text-xl sm:text-2xl font-bold text-emerald-700 mt-2">
                        Rp {{ number_format($totalSettled, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Total dana masuk terverifikasi</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Transaksi Sukses</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ $successCount }} <span class="text-xs font-normal text-slate-500">transaksi</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Pembayaran terkonfirmasi sistem</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Menunggu Pembayaran</div>
                    <div class="text-xl sm:text-2xl font-bold text-amber-700 mt-2">
                        {{ $pendingCount }} <span class="text-xs font-normal text-slate-500">transaksi</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Tagihan aktif di Midtrans</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Gagal / Dibatalkan</div>
                    <div class="text-xl sm:text-2xl font-bold text-rose-700 mt-2">
                        {{ $failedCount }} <span class="text-xs font-normal text-slate-500">transaksi</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Kedaluwarsa atau ditolak</p>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="flex-1 max-w-md">
                    @if ($currentStatus)
                        <input type="hidden" name="status" value="{{ $currentStatus }}" />
                    @endif
                    <div class="relative">
                        <input 
                            type="search" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari no. pembayaran, order ID, gateway ID..." 
                            class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-xl focus:border-emerald-700 focus:ring-emerald-700 text-slate-900" 
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('admin.payments.index', ['search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ empty($currentStatus) ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Semua ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'success', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'success' ? 'bg-emerald-800 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Sukses ({{ $successCount }})
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'pending', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'pending' ? 'bg-amber-700 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Pending ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'failed', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'failed' ? 'bg-rose-700 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Gagal ({{ $failedCount }})
                    </a>
                </div>
            </div>

            <!-- Payments Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[700px] w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">No. Pembayaran</th>
                                <th scope="col" class="px-6 py-3.5 text-left">No. Pesanan</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Pembeli</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Metode</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Nominal</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status Gateway</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Waktu Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900">
                                        {{ $payment->payment_number }}
                                        @if($payment->gateway_transaction_id)
                                            <div class="text-[10px] text-slate-400 font-normal">ID: {{ $payment->gateway_transaction_id }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->order)
                                            <a href="{{ route('admin.orders.show', $payment->order) }}" class="font-semibold text-emerald-700 hover:underline">
                                                {{ $payment->order->order_number }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ $payment->order?->user?->name ?? 'User' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $payment->order?->user?->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700 font-semibold">
                                        {{ strtoupper($payment->payment_method) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-slate-900">
                                        {{ $payment->formattedAmount() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $payment->status->badgeColor() }}">
                                            {{ $payment->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                        {{ $payment->created_at->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <p class="text-sm font-medium">Tidak ada transaksi pembayaran yang sesuai dengan kriteria filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($payments->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
