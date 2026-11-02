<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight">
                    Pusat Persetujuan Toko Mitra
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Verifikasi dan kelola permohonan pendaftaran mitra penjual baru NexaMarket</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700">
                    Administrator Portal
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Status Message -->
            @if (session('status') || session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm font-medium">
                    {{ session('status') ?? session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-800 dark:text-rose-300 text-xs sm:text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Status Filter Tabs -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-soft flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.shops.index') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ empty($currentFilter) ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    Semua Pengajuan
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'pending']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'pending' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <span>Menunggu Review</span>
                    @if ($pendingCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentFilter === 'pending' ? 'bg-amber-400 text-amber-950 font-bold' : 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 font-bold' }}">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'approved']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ $currentFilter === 'approved' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    Disetujui ({{ $approvedCount }})
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'rejected']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ $currentFilter === 'rejected' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    Ditolak ({{ $rejectedCount }})
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'suspended']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'suspended' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <span>Ditangguhkan</span>
                    @if ($suspendedCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentFilter === 'suspended' ? 'bg-slate-300 text-slate-900 font-bold' : 'bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300 font-bold' }}">
                            {{ $suspendedCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Shops Table -->
            <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-3xl border border-[#E8EAF0] dark:border-slate-800 shadow-card">
                <div class="overflow-x-auto">
                    <table class="min-w-[650px] w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Nama Toko</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Pemilik Akun</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Tanggal Pengajuan</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                            @forelse ($shops as $shop)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $shop->name }}</div>
                                        <div class="text-slate-500 dark:text-slate-400 text-[11px] truncate max-w-xs">/shop/{{ $shop->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800 dark:text-slate-200">{{ $shop->user->name ?? 'User' }}</div>
                                        <div class="text-slate-500 dark:text-slate-400">{{ $shop->user->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                        {{ $shop->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $shop->status->badgeColor() }}">
                                            {{ $shop->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                        <a href="{{ route('admin.shops.show', $shop) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold transition">
                                            Tinjau Rincian &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        <p class="text-sm font-medium">Tidak ada pengajuan toko yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($shops->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $shops->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
