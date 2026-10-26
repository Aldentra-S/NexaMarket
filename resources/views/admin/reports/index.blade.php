<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Laporan Performa Bisnis Platform
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Ringkasan analitik perputaran transaksi, volume penjualan produk, dan kinerja mitra toko</p>
            </div>
            <div class="flex items-center flex-wrap gap-2.5">
                <a href="{{ route('admin.reports.export.csv') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-2xs transition">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Ekspor CSV</span>
                </a>

                <a href="{{ route('admin.reports.export.pdf') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-2xs transition">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Ekspor PDF</span>
                </a>

            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Headline Financial & Operational KPIs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Gross Merchandise Value (GMV)</div>
                    <div class="text-xl sm:text-2xl font-bold text-emerald-700 mt-2">
                        Rp {{ number_format($totalGmv, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Total nilai transaksi berbayar</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Barang Terjual</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ number_format($totalSoldUnits, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">unit</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Akumulasi seluruh pesanan sukses</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Pesanan Terselesaikan</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ $completedOrdersCount }} <span class="text-xs font-normal text-slate-500">pesanan</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Telah diterima oleh pembeli</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Total Akumulasi Pesanan</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ $totalOrdersCount }} <span class="text-xs font-normal text-slate-500">pesanan</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Seluruh pesanan masuk di sistem</p>
                </div>
            </div>

            <!-- Two-Column Analytics Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left: Categories & Distribution (6 cols) -->
                <div class="lg:col-span-6 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Distribusi Produk per Kategori</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Taksonomi katalog produk dengan keterisian terbanyak</p>
                            </div>
                            <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                                Kelola &rarr;
                            </a>
                        </div>

                        @if($categoriesReport->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($categoriesReport as $category)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-bold text-slate-900">{{ $category->name }}</span>
                                            <span class="text-[11px] text-slate-400 block mt-0.5">Slug: {{ $category->slug }}</span>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            {{ $category->products_count }} produk
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-500 text-center py-6">Belum ada data kategori.</p>
                        @endif
                    </div>
                </div>

                <!-- Right: Top Active Shops (6 cols) -->
                <div class="lg:col-span-6 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Mitra Penjual Aktif</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Toko terverifikasi dengan aktivitas etalase &amp; pemenuhan pesanan</p>
                            </div>
                            <a href="{{ route('admin.shops.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                                Semua Toko &rarr;
                            </a>
                        </div>

                        @if($topShops->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($topShops as $shop)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                        <div>
                                            <a href="{{ route('admin.shops.show', $shop) }}" class="font-bold text-slate-900 hover:text-emerald-700 transition">
                                                {{ $shop->name }}
                                            </a>
                                            <span class="text-[11px] text-slate-500 block mt-0.5">
                                                {{ $shop->products_count }} produk etalase
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-200 text-slate-800">
                                                {{ $shop->shop_orders_count }} pesanan
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-500 text-center py-6">Belum ada mitra toko yang disetujui.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Settled Transactions Table -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Transaksi Berhasil Terbaru</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi yang telah terbayar dan masuk ke sistem</p>
                    </div>
                    <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                        Audit Pembayaran &rarr;
                    </a>
                </div>

                @if($recentSettlements->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-[650px] w-full divide-y divide-slate-100 text-xs">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 text-left">No. Pesanan</th>
                                    <th scope="col" class="px-6 py-3.5 text-left">Pembeli</th>
                                    <th scope="col" class="px-6 py-3.5 text-left">Toko Terkait</th>
                                    <th scope="col" class="px-6 py-3.5 text-right">Nilai GMV</th>
                                    <th scope="col" class="px-6 py-3.5 text-left">Waktu Pembayaran</th>
                                    <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($recentSettlements as $order)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-slate-800">{{ $order->user->name ?? 'User' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $order->user->email ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                            @foreach($order->shopOrders as $so)
                                                <span class="inline-block px-2 py-0.5 bg-slate-100 rounded text-[11px] mr-1">
                                                    {{ $so->shop_name ?? ($so->shop->name ?? 'Toko') }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-emerald-700">
                                            {{ $order->formattedTotal() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                            {{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                                                Detail &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-slate-500">
                        <p class="text-xs font-medium">Belum ada transaksi sukses yang tercatat.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
