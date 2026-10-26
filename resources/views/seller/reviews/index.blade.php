<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-700 transition">Seller Center</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800 font-medium">Ulasan Produk</span>
                </div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Ulasan & Penilaian Produk') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantau kepuasan pembeli dan umpan balik ulasan untuk produk-produk toko {{ $shop->name }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('seller.dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-700 transition">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Review Overview Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Pusat Reputasi Toko
                        </span>
                        <h3 class="text-lg font-bold text-slate-900 mt-1">Kepuasan Pelanggan & Rating Etalase</h3>
                        <p class="text-xs text-slate-500">
                            Ulasan dan bintang rating dari pembeli terverifikasi akan langsung ditampilkan di etalase katalog publik setelah pesanan selesai.
                        </p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600 shrink-0">
                        <div>Total Produk Dimonitor: <strong class="text-slate-900">{{ $products->total() }} produk</strong></div>
                        <div class="mt-0.5 text-slate-500">Sistem Penilaian: Rating 1 - 5 Bintang</div>
                    </div>
                </div>
            </div>

            <!-- Products Review Readiness Table -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Daftar Produk & Umpan Balik Pembeli</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau status transaksi dan ulasan pada setiap produk aktif</p>
                    </div>
                    <span class="text-xs font-medium text-slate-500">
                        {{ $products->total() }} Produk Terdaftar
                    </span>
                </div>

                @if ($products->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-[640px] w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                                <tr>
                                    <th class="py-3.5 px-6">Produk</th>
                                    <th class="py-3.5 px-6">Kategori</th>
                                    <th class="py-3.5 px-6 text-center">Unit Dipesan</th>
                                    <th class="py-3.5 px-6 text-center">Status Ulasan</th>
                                    <th class="py-3.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($products as $prod)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center space-x-3.5">
                                                <img src="{{ $prod->thumbnail_url }}" alt="{{ $prod->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0 bg-slate-50" />
                                                <div>
                                                    <a href="{{ route('seller.products.show', $prod) }}" class="font-bold text-slate-900 hover:text-emerald-700 transition">
                                                        {{ $prod->name }}
                                                    </a>
                                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                                        {{ $prod->formattedPrice() }} &bull; Stok: {{ $prod->stock }} unit
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-slate-600">
                                            {{ $prod->category?->name ?? '-' }}
                                        </td>
                                        <td class="py-4 px-6 text-center font-semibold text-slate-800">
                                            {{ $prod->order_items_count }} kali
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            @if ($prod->order_items_count > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    Siap Dinilai Pembeli
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                                    Belum Ada Transaksi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <a href="{{ route('products.show', $prod->slug) }}" target="_blank" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                                                Lihat di Etalase &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($products->hasPages())
                        <div class="p-4 border-t border-slate-100">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center text-slate-500">
                        <p class="text-xs font-medium">Belum ada produk terdaftar di toko Anda.</p>
                        <div class="mt-4">
                            <a href="{{ route('seller.products.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl shadow-xs transition">
                                + Tambah Produk Sekarang
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
