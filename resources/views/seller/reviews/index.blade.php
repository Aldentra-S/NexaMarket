<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight">
                    {{ __('Ulasan & Penilaian Produk') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pantau kepuasan pembeli dan umpan balik ulasan untuk produk-produk toko {{ $shop->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Review Overview Card -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-soft">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/60">
                            Pusat Reputasi Toko
                        </span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-1">Kepuasan Pelanggan & Rating Etalase</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Ulasan dan bintang rating dari pembeli terverifikasi akan langsung ditampilkan di etalase katalog publik setelah pesanan selesai.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Products Review Readiness Table -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-[#E8EAF0] dark:border-slate-800 overflow-hidden shadow-card">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Daftar Produk & Umpan Balik Pembeli</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pantau status transaksi dan ulasan pada setiap produk aktif</p>
                    </div>
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                        {{ $products->total() }} Produk Terdaftar
                    </span>
                </div>

                @if ($products->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-[640px] w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="py-3.5 px-6">Produk</th>
                                    <th class="py-3.5 px-6">Kategori</th>
                                    <th class="py-3.5 px-6 text-center">Unit Dipesan</th>
                                    <th class="py-3.5 px-6 text-center">Status Ulasan</th>
                                    <th class="py-3.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach ($products as $prod)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center space-x-3.5">
                                                <img src="{{ $prod->thumbnail_url }}" alt="{{ $prod->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-50 dark:bg-slate-800" />
                                                <div>
                                                    <a href="{{ route('seller.products.show', $prod) }}" class="font-bold text-slate-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded">
                                                        {{ $prod->name }}
                                                    </a>
                                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                        {{ $prod->formattedPrice() }} &bull; Stok: {{ $prod->stock }} unit
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-slate-600 dark:text-slate-400">
                                            {{ $prod->category?->name ?? '-' }}
                                        </td>
                                        <td class="py-4 px-6 text-center font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $prod->order_items_count }} kali
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            @if ($prod->order_items_count > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/60">
                                                    Siap Dinilai Pembeli
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                                    Belum Ada Transaksi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <a href="{{ route('products.show', $prod->slug) }}" target="_blank" class="text-xs font-semibold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded">
                                                Lihat di Etalase &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($products->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center text-slate-500 dark:text-slate-400">
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
