<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Manajemen Pengguna Platform
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola akun pembeli, mitra penjual, dan administrator resmi ekosistem NexaMarket</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Search & Filters Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Search Box -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1 max-w-md">
                    @if ($currentRole)
                        <input type="hidden" name="role" value="{{ $currentRole }}" />
                    @endif
                    <div class="relative">
                        <input 
                            type="search" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari berdasarkan nama atau email pengguna..." 
                            class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-xl focus:border-emerald-700 focus:ring-emerald-700 text-slate-900" 
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                <!-- Role Filter Tabs -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('admin.users.index', ['search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ empty($currentRole) ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Semua ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'buyer', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentRole === 'buyer' ? 'bg-sky-700 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Pembeli ({{ $buyerCount }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'seller', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentRole === 'seller' ? 'bg-emerald-800 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Penjual ({{ $sellerCount }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition {{ $currentRole === 'admin' ? 'bg-slate-800 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        Admin ({{ $adminCount }})
                    </a>
                </div>
            </div>

            <!-- Users Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[650px] w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Pengguna</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Email Resmi</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Peran Akun</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Toko Terkait</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Terdaftar Pada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($users as $user)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm">{{ $user->name }}</div>
                                                <div class="text-[11px] text-slate-400">ID: #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($user->isAdmin())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-300">
                                                Administrator
                                            </span>
                                        @elseif ($user->isSeller())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Penjual (Seller)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                                Pembeli (Buyer)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->shop)
                                            <div>
                                                <a href="{{ route('admin.shops.show', $user->shop) }}" class="font-semibold text-emerald-700 hover:underline">
                                                    {{ $user->shop->name }}
                                                </a>
                                                <span class="block text-[11px] text-slate-400">Status: {{ $user->shop->status->label() }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Tidak ada toko</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                        {{ $user->created_at->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        <p class="text-sm font-medium">Tidak ada data pengguna yang sesuai dengan kriteria pencarian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
