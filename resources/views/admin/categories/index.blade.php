<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Manajemen Kategori Produk') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola taksonomi dan pengelompokan produk katalog marketplace NexaMarket</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                    + Tambah Kategori
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Status Messages -->
            @if (session('status') || session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium">
                    {{ session('status') ?? session('success') }}
                </div>
            @endif

            @if ($errors->has('category'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium">
                    {{ $errors->first('category') }}
                </div>
            @endif

            <!-- Search & Stats Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex-1 max-w-md">
                    <div class="relative">
                        <input 
                            type="search" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari kategori berdasarkan nama atau slug..." 
                            class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-xl focus:border-emerald-700 focus:ring-emerald-700" 
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                <div class="flex items-center space-x-4 text-xs text-slate-500">
                    <span>Total Kategori: <strong class="text-slate-900">{{ $totalCount }}</strong></span>
                    <span>&bull;</span>
                    <span>Aktif: <strong class="text-emerald-700">{{ $activeCount }}</strong></span>
                </div>
            </div>

            <!-- Categories Table Card -->
            <div class="bg-white overflow-hidden rounded-2xl border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-[600px] w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Nama Kategori</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Slug</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Deskripsi</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Jumlah Produk</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($categories as $cat)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        {{ $cat->name }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 text-[11px]">
                                        {{ $cat->slug }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 max-w-xs truncate">
                                        {{ $cat->description ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cat->products_count > 0 ? 'bg-slate-100 text-slate-800' : 'bg-slate-50 text-slate-500' }}">
                                            {{ $cat->products_count }} produk
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($cat->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium">
                                        <div class="inline-flex items-center space-x-2">
                                            <a href="{{ route('admin.categories.edit', $cat) }}" 
                                               class="px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori {{ $cat->name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        Belum ada kategori yang terdaftar. Klik "+ Tambah Kategori" untuk membuat kategori baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
