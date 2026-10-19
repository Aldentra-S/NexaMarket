<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-600 transition">Seller Center</a>
                    <span>&rsaquo;</span>
                    <span class="text-gray-800 font-medium">Katalog Produk</span>
                </div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    {{ __('Manajemen Produk Toko') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Kelola penawaran produk, pantau stok, dan atur status tayang di marketplace</p>
            </div>
            <div>
                <a href="{{ route('seller.products.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Produk Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Filter Tabs & Search Bar -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Status Filter Tabs -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('seller.products.index', array_filter(['search' => $search])) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ empty($currentFilter) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Semua ({{ $totalCount }})
                    </a>
                    <a href="{{ route('seller.products.index', array_filter(['status' => 'published', 'search' => $search])) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ $currentFilter === 'published' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Ditayangkan ({{ $publishedCount }})
                    </a>
                    <a href="{{ route('seller.products.index', array_filter(['status' => 'draft', 'search' => $search])) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ $currentFilter === 'draft' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Draf ({{ $draftCount }})
                    </a>
                    <a href="{{ route('seller.products.index', array_filter(['status' => 'archived', 'search' => $search])) }}" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition {{ $currentFilter === 'archived' ? 'bg-gray-700 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Diarsipkan ({{ $archivedCount }})
                    </a>
                </div>

                <!-- Search Input Form -->
                <form method="GET" action="{{ route('seller.products.index') }}" class="flex items-center gap-2">
                    @if ($currentFilter)
                        <input type="hidden" name="status" value="{{ $currentFilter }}">
                    @endif
                    <div class="relative w-full md:w-64">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nama atau deskripsi..." 
                               class="w-full pl-9 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition outline-none" />
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition">
                        Cari
                    </button>
                    @if ($search)
                        <a href="{{ route('seller.products.index', array_filter(['status' => $currentFilter])) }}" 
                           class="text-xs text-red-600 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Products Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Produk</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Harga Satuan</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Stok</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 text-sm hover:text-emerald-600 transition">
                                            <a href="{{ route('seller.products.show', $product) }}">
                                                {{ $product->name }}
                                            </a>
                                        </div>
                                        <div class="text-gray-400 font-mono text-[11px] truncate max-w-sm mt-0.5">
                                            {{ $product->slug }}
                                        </div>
                                        <div class="text-gray-400 text-[10px] mt-0.5">
                                            Diperbarui: {{ $product->updated_at->format('d M Y, H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-bold text-gray-900 text-sm">
                                            {{ $product->formattedPrice() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($product->stock > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ number_format($product->stock) }} unit
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                Habis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $product->status->badgeColor() }}">
                                            {{ $product->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                        <div class="inline-flex items-center space-x-2">
                                            <a href="{{ route('seller.products.show', $product) }}" 
                                               class="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" 
                                               title="Lihat Rincian">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('seller.products.edit', $product) }}" 
                                               class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                               title="Edit Produk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <!-- Delete Modal Trigger -->
                                            <div x-data="{ openDeleteModal: false }">
                                                <button type="button" 
                                                        @click="openDeleteModal = true" 
                                                        class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" 
                                                        title="Hapus Produk">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>

                                                <!-- Delete Modal -->
                                                <div x-show="openDeleteModal" 
                                                     class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4 text-left" 
                                                     style="display: none;">
                                                    <div @click.outside="openDeleteModal = false" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-gray-100">
                                                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center mb-4">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-base font-bold text-gray-900">Hapus Produk Ini?</h3>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            Anda yakin ingin menghapus produk <strong>"{{ $product->name }}"</strong>? Tindakan ini tidak dapat dibatalkan.
                                                        </p>

                                                        <form method="POST" action="{{ route('seller.products.destroy', $product) }}" class="mt-5 flex justify-end space-x-2">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" @click="openDeleteModal = false" class="px-3 py-1.5 text-xs text-gray-600 hover:text-gray-900 font-medium">
                                                                Batal
                                                            </button>
                                                            <button type="submit" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                                                                Ya, Hapus Produk
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800">Belum ada produk yang ditemukan</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Mulai jualan dengan menambahkan produk pertama ke etalase toko Anda.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('seller.products.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                                                + Tambah Produk Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
