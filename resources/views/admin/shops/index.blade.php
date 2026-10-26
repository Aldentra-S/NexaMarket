<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Pusat Persetujuan Toko Mitra
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Verifikasi dan kelola permohonan pendaftaran mitra penjual baru NexaMarket</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-300">
                    Administrator Portal
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Status Message -->
            @if (session('status') || session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium">
                    {{ session('status') ?? session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Status Filter Tabs -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.shops.index') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ empty($currentFilter) ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    Semua Pengajuan
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'pending']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'pending' ? 'bg-amber-700 text-white' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    <span>Menunggu Review</span>
                    @if ($pendingCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentFilter === 'pending' ? 'bg-white text-amber-800 font-bold' : 'bg-amber-100 text-amber-800 font-bold' }}">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'approved']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ $currentFilter === 'approved' ? 'bg-emerald-800 text-white' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    Disetujui ({{ $approvedCount }})
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'rejected']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ $currentFilter === 'rejected' ? 'bg-rose-700 text-white' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    Ditolak ({{ $rejectedCount }})
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'suspended']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'suspended' ? 'bg-slate-800 text-white' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    <span>Ditangguhkan</span>
                    @if ($suspendedCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentFilter === 'suspended' ? 'bg-white text-slate-800 font-bold' : 'bg-slate-200 text-slate-800 font-bold' }}">
                            {{ $suspendedCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Shops Table -->
            <div class="bg-white overflow-hidden rounded-2xl border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-[650px] w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Nama Toko</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Pemilik Akun</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Tanggal Pengajuan</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($shops as $shop)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 text-sm">{{ $shop->name }}</div>
                                        <div class="text-slate-500 text-[11px] truncate max-w-xs">/shop/{{ $shop->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ $shop->user->name ?? 'User' }}</div>
                                        <div class="text-slate-500">{{ $shop->user->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                        {{ $shop->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $shop->status->badgeColor() }}">
                                            {{ $shop->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                        <a href="{{ route('admin.shops.show', $shop) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition">
                                            Tinjau Rincian &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        <p class="text-sm font-medium">Tidak ada pengajuan toko yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($shops->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $shops->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
