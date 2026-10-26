<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Seller Center | ') }} {{ $shop->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Pusat kendali operasional, performa penjualan, dan pengelolaan inventaris toko</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    Toko Terverifikasi
                </span>
                <a href="{{ route('seller.shop.settings') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                    Pengaturan Toko
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('status') || session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium">
                    {{ session('status') ?? session('success') }}
                </div>
            @endif

            <!-- Shop Hero Banner -->
            <div class="bg-orange-600 rounded-3xl p-6 sm:p-8 text-white">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-md">
                            Toko Terverifikasi (Approved)
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-bold mt-2">{{ $shop->name }}</h3>
                        <p class="text-xs text-orange-100">
                            {{ $shop->description }}
                        </p>
                    </div>

                    <div class="bg-black/15 border border-white/20 p-4 rounded-2xl text-xs space-y-1 shrink-0">
                        <div class="text-orange-200 text-xs">Terverifikasi Sejak:</div>
                        <div class="font-bold text-white text-sm">{{ $shop->approved_at?->format('d M Y') ?? 'Terverifikasi' }}</div>
                        <div class="text-[11px] text-orange-200">ID Toko: #{{ $shop->id }}</div>
                    </div>
                </div>
            </div>

            <!-- Financial & Sales Performance Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Revenue Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Total Pendapatan Terverifikasi</div>
                    <div class="text-xl sm:text-2xl font-bold text-emerald-700 mt-2">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Akumulasi transaksi pesanan berbayar & selesai</p>
                </div>

                <!-- Needs Processing Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500">Perlu Diproses Segera</span>
                        @if ($processingOrdersCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                Butuh Aksi
                            </span>
                        @endif
                    </div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ $processingOrdersCount }} <span class="text-xs font-normal text-slate-500">pesanan</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Pesanan berstatus berbayar atau sedang dikemas</p>
                </div>

                <!-- Total Orders Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Pesanan Selesai</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ $completedOrdersCount }} <span class="text-xs font-normal text-slate-500">/ {{ $totalOrders }} total</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Tingkat pemenuhan pesanan pembeli</p>
                </div>

                <!-- Products Sold Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Total Produk Terjual</div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                        {{ number_format($totalSoldUnits, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">unit</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Unit barang yang berhasil dipesan pembeli</p>
                </div>
            </div>

            <!-- Quick Navigation & Inventory Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Catalog & Inventory Status Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Etalase & Inventaris Produk</h3>
                            <a href="{{ route('seller.products.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                                Lihat Semua &rarr;
                            </a>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="block text-xl font-bold text-slate-900">{{ $totalProducts }}</span>
                                <span class="block text-[11px] font-semibold text-slate-500 mt-0.5">Total</span>
                            </div>
                            <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-100">
                                <span class="block text-xl font-bold text-emerald-700">{{ $publishedProducts }}</span>
                                <span class="block text-[11px] font-semibold text-emerald-600 mt-0.5">Tayang</span>
                            </div>
                            <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-100">
                                <span class="block text-xl font-bold text-amber-700">{{ $draftProducts }}</span>
                                <span class="block text-[11px] font-semibold text-amber-600 mt-0.5">Draf</span>
                            </div>
                        </div>

                        @if ($lowStockProducts > 0)
                            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-center justify-between">
                                <span class="font-medium">Perhatian: Ada <strong>{{ $lowStockProducts }}</strong> produk dengan stok menipis (&le; 5 unit).</span>
                                <a href="{{ route('seller.products.index') }}" class="text-amber-800 font-bold hover:underline">Cek</a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <span class="text-xs text-slate-500">Manajemen Produk</span>
                        <a href="{{ route('seller.products.create') }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl shadow-xs transition">
                            + Tambah Produk Baru
                        </a>
                    </div>
                </div>

                <!-- Orders Overview Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Alur Pesanan Masuk</h3>
                            <a href="{{ route('seller.orders.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700">
                                Semua Pesanan &rarr;
                            </a>
                        </div>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Pantau pesanan yang masuk ke toko Anda, periksa status pembayaran digital Midtrans, dan kirimkan barang tepat waktu.
                        </p>
                        <div class="mt-4 space-y-2 text-xs">
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                                <span class="text-slate-600">Pesanan Perlu Diproses:</span>
                                <span class="font-bold text-slate-900">{{ $processingOrdersCount }} pesanan</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                                <span class="text-slate-600">Pesanan Selesai Terkirim:</span>
                                <span class="font-bold text-slate-900">{{ $completedOrdersCount }} pesanan</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-500">Pemenuhan Pesanan</span>
                        <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl shadow-xs transition">
                            Kelola Pesanan Masuk &rarr;
                        </a>
                    </div>
                </div>

                <!-- Shop Profile & Review Shortcuts -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Pusat Informasi Toko</h3>
                        <div class="mt-3 space-y-2 text-xs">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-500 block">Pemilik Toko:</span>
                                <span class="font-semibold text-slate-900">{{ $user->name }} ({{ $user->email }})</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-500 block">Kontak & Alamat Toko:</span>
                                <span class="font-semibold text-slate-900">{{ $shop->phone ?: '-' }} &bull; {{ Str::limit($shop->address ?: 'Alamat belum diatur', 45) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('seller.reviews.index') }}" class="text-xs font-semibold text-slate-700 hover:text-emerald-700">
                            Ulasan Produk &rarr;
                        </a>
                        <a href="{{ route('seller.shop.settings') }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            Edit Profil Toko
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Pesanan Masuk Terbaru</h3>
                        <p class="text-xs text-slate-500 mt-0.5">5 pesanan terbaru yang diterima oleh toko Anda</p>
                    </div>
                    <a href="{{ route('seller.orders.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                        Lihat Semua Pesanan &rarr;
                    </a>
                </div>

                @if ($recentOrders->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-[640px] w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-5">No. Pesanan</th>
                                    <th class="py-3 px-5">Pembeli</th>
                                    <th class="py-3 px-5">Waktu</th>
                                    <th class="py-3 px-5 text-right">Subtotal</th>
                                    <th class="py-3 px-5 text-center">Status</th>
                                    <th class="py-3 px-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($recentOrders as $order)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5 font-bold text-slate-900">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-700">
                                            {{ $order->order->user->name ?? 'Pembeli' }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-500">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-3 px-5 text-right font-semibold text-slate-900">
                                            Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-5 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $order->status->badgeColor() }}">
                                                {{ $order->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-5 text-right">
                                            <a href="{{ route('seller.orders.show', $order) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                                                Kelola &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-slate-500">
                        <p class="text-xs font-medium">Belum ada pesanan terbaru untuk toko Anda saat ini.</p>
                    </div>
                @endif
            </div>

            <!-- Critical Stock Products Alert Section (if any) -->
            @if ($criticalStockList->isNotEmpty())
                <div class="bg-white rounded-2xl border border-amber-200 overflow-hidden">
                    <div class="p-5 border-b border-amber-100 bg-amber-50/50 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-sm text-amber-950">Peringatan: Stok Produk Menipis (&le; 5 Unit)</h3>
                            <p class="text-xs text-amber-800 mt-0.5">Segera perbarui jumlah stok agar produk tidak otomatis berstatus habis di marketplace</p>
                        </div>
                        <a href="{{ route('seller.products.index') }}" class="text-xs font-semibold text-amber-900 hover:underline">
                            Kelola Stok Produk &rarr;
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-[600px] w-full text-left text-xs">
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($criticalStockList as $prod)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3 px-5 font-medium text-slate-900">
                                            {{ $prod->name }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-600">
                                            Harga: {{ $prod->formattedPrice() }}
                                        </td>
                                        <td class="py-3 px-5 text-center">
                                            @if ($prod->stock === 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Stok Habis (0)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                    Sisa {{ $prod->stock }} unit
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-5 text-right">
                                            <a href="{{ route('seller.products.edit', $prod) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                                                Update Stok &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
