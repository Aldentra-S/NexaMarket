<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight">
                    Pusat Pembayaran Gateway
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Audit log transaksi Midtrans Snap, status settlement perbankan, dan rekonsiliasi gateway</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Payment Metrics Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-soft">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Settlement Berhasil</div>
                    <div class="text-xl sm:text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-2">
                        Rp {{ number_format($totalSettled, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Total dana masuk terverifikasi</div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-soft">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Transaksi Sukses</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-2">
                        {{ $successCount }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">transaksi</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Pembayaran terkonfirmasi sistem</div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-soft">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Menunggu Pembayaran</div>
                    <div class="text-xl sm:text-2xl font-bold text-amber-600 dark:text-amber-400 mt-2">
                        {{ $pendingCount }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">transaksi</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Tagihan aktif di Midtrans</div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-soft">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Gagal / Dibatalkan</div>
                    <div class="text-xl sm:text-2xl font-bold text-rose-600 dark:text-rose-400 mt-2">
                        {{ $failedCount }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">transaksi</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Kedaluwarsa atau ditolak</div>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-soft flex flex-col md:flex-row md:items-center md:justify-between gap-4">
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
                            class="w-full pl-9 pr-4 py-2 text-xs border border-[#E8EAF0] dark:border-slate-700 bg-white dark:bg-slate-900 rounded-xl focus:border-coral-500 focus:ring-2 focus:ring-coral-500/20 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 outline-none transition shadow-2xs" 
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('admin.payments.index', ['search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ empty($currentStatus) ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-2xs' : 'bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Semua ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'success', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'success' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-2xs' : 'bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Sukses ({{ $successCount }})
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'pending', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'pending' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-2xs' : 'bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Pending ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'failed', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentStatus === 'failed' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-2xs' : 'bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Gagal ({{ $failedCount }})
                    </a>
                </div>
            </div>

            <!-- Payments Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-[#E8EAF0] dark:border-slate-800 shadow-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[700px] w-full divide-y divide-[#E8EAF0] dark:divide-slate-800 text-xs">
                        <thead class="bg-[#F7F8FA] dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-semibold border-b border-[#E8EAF0] dark:border-slate-800">
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
                        <tbody class="divide-y divide-[#E8EAF0] dark:divide-slate-800 bg-white dark:bg-slate-900">
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-[#F7F8FA]/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900 dark:text-white">
                                        {{ $payment->payment_number }}
                                        @if($payment->gateway_transaction_id)
                                            <div class="text-[10px] text-slate-400 font-normal">ID: {{ $payment->gateway_transaction_id }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->order)
                                            <a href="{{ route('admin.orders.show', $payment->order) }}" class="font-semibold text-coral-600 hover:text-coral-700 dark:text-coral-400 hover:underline">
                                                {{ $payment->order->order_number }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800 dark:text-slate-200">{{ $payment->order?->user?->name ?? 'User' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $payment->order?->user?->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700 dark:text-slate-300 font-semibold">
                                        {{ strtoupper($payment->payment_method) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-slate-900 dark:text-white">
                                        {{ $payment->formattedAmount() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $payment->status->badgeColor() }}">
                                            {{ $payment->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                        {{ $payment->created_at->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        <p class="text-sm font-medium">Tidak ada data transaksi pembayaran yang tercatat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($payments->hasPages())
                    <div class="p-4 border-t border-[#E8EAF0] dark:border-slate-800">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
