<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Manajemen Kategori Produk') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Kelola taksonomi dan pengelompokan produk marketplace NexaMarket</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.shops.index') }}" class="text-xs font-semibold text-gray-600 hover:text-purple-600 transition">
                    &larr; Approval Toko
                </a>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    + Tambah Kategori
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Status Messages -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('category'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
                    {{ $errors->first('category') }}
                </div>
            @endif

            <!-- Search & Stats Bar -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex-1 max-w-md">
                    <div class="relative">
                        <input 
                            type="search" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari kategori berdasarkan nama atau slug..." 
                            class="w-full pl-9 pr-4 py-2 text-xs border border-gray-200 rounded-xl focus:border-purple-500 focus:ring-purple-500" 
                        />
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                <div class="flex items-center space-x-4 text-xs text-gray-500">
                    <span>Total Kategori: <strong class="text-gray-900">{{ $totalCount }}</strong></span>
                    <span>Aktif: <strong class="text-emerald-600">{{ $activeCount }}</strong></span>
                </div>
            </div>

            <!-- Categories Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-xs">
                        <thead class="bg-gray-50/75 text-gray-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Nama Kategori</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Slug</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Deskripsi</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Jumlah Produk</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($categories as $cat)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        {{ $cat->name }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-500">
                                        {{ $cat->slug }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 max-w-xs truncate">
                                        {{ $cat->description ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cat->products_count > 0 ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $cat->products_count }} produk
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($cat->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium">
                                        <div class="inline-flex items-center space-x-2">
                                            <a href="{{ route('admin.categories.edit', $cat) }}" 
                                               class="px-2.5 py-1 text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori {{ $cat->name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        Belum ada kategori yang terdaftar. Klik "+ Tambah Kategori" untuk membuat kategori baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
