<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pusat Persetujuan Toko (Shop Approval Center)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Verifikasi dan kelola permohonan pendaftaran mitra penjual baru NexaMarket</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                Administrator Portal
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Status Message -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Status Filter Tabs -->
            <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4">
                <a href="{{ route('admin.shops.index') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ empty($currentFilter) ? 'bg-purple-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    Semua Pengajuan
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    <span>Menunggu Review</span>
                    @if ($pendingCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentFilter === 'pending' ? 'bg-white text-amber-700 font-bold' : 'bg-amber-100 text-amber-800 font-bold' }}">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'approved']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ $currentFilter === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    Disetujui ({{ $approvedCount }})
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'rejected']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ $currentFilter === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    Ditolak ({{ $rejectedCount }})
                </a>
                <a href="{{ route('admin.shops.index', ['status' => 'suspended']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'suspended' ? 'bg-gray-800 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    <span>Ditangguhkan</span>
                    @if ($suspendedCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentFilter === 'suspended' ? 'bg-white text-gray-800 font-bold' : 'bg-gray-200 text-gray-800 font-bold' }}">
                            {{ $suspendedCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Shops Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Nama Toko</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Pemilik Akun</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Tanggal Pengajuan</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($shops as $shop)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 text-sm">{{ $shop->name }}</div>
                                        <div class="text-gray-400 font-mono text-[11px] truncate max-w-xs">{{ $shop->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-800">{{ $shop->user->name }}</div>
                                        <div class="text-gray-500">{{ $shop->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ $shop->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $shop->status->badgeColor() }}">
                                            {{ $shop->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                        <a href="{{ route('admin.shops.show', $shop) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-purple-50 text-purple-700 text-xs font-bold transition">
                                            Tinjau Rincian &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <p class="text-sm font-medium">Tidak ada pengajuan toko yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($shops->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $shops->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
