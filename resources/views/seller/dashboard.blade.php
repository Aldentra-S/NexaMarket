<x-app-layout>
    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 1. Header & Quick Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="sr-only">Toko Terverifikasi (Approved)</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                        Selamat datang, {{ auth()->user()->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Ringkasan toko Anda &bull; <span class="font-bold text-slate-800">{{ $shop->name }}</span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('products.index') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                        <span>Lihat Katalog Publik</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>

                    <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl shadow-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Produk</span>
                    </a>
                </div>
            </div>

            <!-- 2. Four KPI Cards (Matching Mockup) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- KPI 1: Produk Aktif -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Produk Aktif</p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">{{ $publishedProducts }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">Dari {{ $totalProducts }} total</p>
                    </div>
                    <a href="{{ route('seller.products.index') }}" class="text-slate-300 hover:text-orange-600 transition font-bold text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded p-1" title="Lihat semua produk">&rarr;</a>
                </div>

                <!-- KPI 2: Pesanan Baru -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Pesanan Baru</p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">{{ $newOrdersCount }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">Perlu diproses</p>
                    </div>
                    <a href="{{ route('seller.orders.index') }}" class="text-slate-300 hover:text-orange-600 transition font-bold text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded p-1" title="Lihat pesanan masuk">&rarr;</a>
                </div>

                <!-- KPI 3: Perlu Diproses -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Perlu Diproses</p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">{{ $processingOrdersCount }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">Menunggu konfirmasi</p>
                    </div>
                    <a href="{{ route('seller.orders.index') }}" class="text-slate-300 hover:text-orange-600 transition font-bold text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded p-1" title="Proses pesanan">&rarr;</a>
                </div>

                <!-- KPI 4: Pendapatan -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Total Pendapatan Terverifikasi</p>
                        <p class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </p>
                        <p class="text-[11px] text-emerald-600 font-semibold mt-1">Bulan ini</p>
                    </div>
                    <span class="text-slate-300 text-sm font-bold">&bull;</span>
                </div>
            </div>

            <!-- 3. Middle Row: Sales Trend (Line Chart) & Order Status (Donut Chart) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Penjualan 7 Hari Terakhir (Line Chart) -->
                <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="font-bold text-sm sm:text-base text-slate-900">Penjualan 7 Hari Terakhir</h3>
                                <p class="text-xs text-slate-500">Performa transaksi dan omzet harian toko</p>
                            </div>
                            <div class="relative">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl">
                                    <span>7 hari terakhir</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Real SVG Line Chart -->
                        @php
                            $maxVal = max(1000000, $salesTrend->max('revenue') ?: 1000000);
                            $points = [];
                            $chartWidth = 600;
                            $chartHeight = 180;
                            $padding = 30;
                            $innerW = $chartWidth - ($padding * 2);
                            $innerH = $chartHeight - 40;

                            foreach ($salesTrend as $i => $day) {
                                $x = $padding + ($i * ($innerW / 6));
                                $ratio = $day['revenue'] / $maxVal;
                                $y = ($chartHeight - 20) - ($ratio * $innerH);
                                $points[] = ['x' => $x, 'y' => $y, 'day' => $day];
                            }
                            $polylinePoints = collect($points)->map(fn($p) => "{$p['x']},{$p['y']}")->join(' ');
                        @endphp

                        <div class="pt-6 pb-2">
                            <div class="relative w-full overflow-x-auto">
                                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight + 20 }}" class="w-full h-48 sm:h-56" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="salesGradient" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#ea580c" stop-opacity="0.25" />
                                            <stop offset="100%" stop-color="#ea580c" stop-opacity="0.0" />
                                        </linearGradient>
                                    </defs>

                                    <!-- Horizontal Grid Lines -->
                                    <line x1="{{ $padding }}" y1="20" x2="{{ $chartWidth - $padding }}" y2="20" stroke="#f1f5f9" stroke-width="1" />
                                    <line x1="{{ $padding }}" y1="{{ 20 + ($innerH * 0.33) }}" x2="{{ $chartWidth - $padding }}" y2="{{ 20 + ($innerH * 0.33) }}" stroke="#f1f5f9" stroke-width="1" />
                                    <line x1="{{ $padding }}" y1="{{ 20 + ($innerH * 0.66) }}" x2="{{ $chartWidth - $padding }}" y2="{{ 20 + ($innerH * 0.66) }}" stroke="#f1f5f9" stroke-width="1" />
                                    <line x1="{{ $padding }}" y1="{{ $chartHeight - 20 }}" x2="{{ $chartWidth - $padding }}" y2="{{ $chartHeight - 20 }}" stroke="#e2e8f0" stroke-width="1" />

                                    <!-- Fill Area -->
                                    @php
                                        $firstX = $points[0]['x'];
                                        $lastX = $points[count($points) - 1]['x'];
                                        $bottomY = $chartHeight - 20;
                                        $areaD = "M {$firstX},{$bottomY} " . collect($points)->map(fn($p) => "L {$p['x']},{$p['y']}")->join(' ') . " L {$lastX},{$bottomY} Z";
                                    @endphp
                                    <path d="{{ $areaD }}" fill="url(#salesGradient)" />

                                    <!-- Polyline -->
                                    <polyline 
                                        fill="none" 
                                        stroke="#ea580c" 
                                        stroke-width="3" 
                                        stroke-linecap="round" 
                                        stroke-linejoin="round" 
                                        points="{{ $polylinePoints }}" 
                                    />

                                    <!-- Dots & Labels -->
                                    @foreach ($points as $pt)
                                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4.5" fill="#ffffff" stroke="#ea580c" stroke-width="2.5" />
                                        <text x="{{ $pt['x'] }}" y="{{ $chartHeight + 10 }}" text-anchor="middle" font-size="10" font-weight="600" fill="#64748b">
                                            {{ $pt['day']['date'] }}
                                        </text>
                                    @endforeach
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Skala Maks: <strong class="text-slate-800">Rp {{ number_format($maxVal, 0, ',', '.') }}</strong></span>
                        <span>Total Akumulasi 7 Hari: <strong class="text-orange-600 font-bold">Rp {{ number_format($salesTrend->sum('revenue'), 0, ',', '.') }}</strong></span>
                    </div>
                </div>

                <!-- Status Pesanan (Donut Chart) -->
                <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="pb-4 border-b border-slate-100">
                            <h3 class="font-bold text-sm sm:text-base text-slate-900">Status Pesanan</h3>
                            <p class="text-xs text-slate-500">Distribusi pesanan toko</p>
                        </div>

                        <!-- Donut Graphic Representation -->
                        <div class="py-6 flex flex-col items-center justify-center relative">
                            <div class="relative w-36 h-36 flex items-center justify-center">
                                @php
                                    $circ = 2 * 3.14159 * 52;
                                    $totalOrd = max(1, $totalOrders);
                                    $pctBaru = ($newOrdersCount / $totalOrd);
                                    $pctDiproses = ($processingOrdersCount / $totalOrd);
                                    $pctDikirim = ($shippedOrdersCount / $totalOrd);
                                    $pctSelesai = ($completedOrdersCount / $totalOrd);

                                    $dashBaru = $pctBaru * $circ;
                                    $dashDiproses = $pctDiproses * $circ;
                                    $dashDikirim = $pctDikirim * $circ;
                                    $dashSelesai = $pctSelesai * $circ;
                                @endphp

                                <svg class="w-36 h-36 transform -rotate-90" viewBox="0 0 120 120">
                                    <!-- Background Track -->
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="#f1f5f9" stroke-width="12" />

                                    @if($totalOrders > 0)
                                        <!-- Baru (Orange) -->
                                        <circle cx="60" cy="60" r="52" fill="none" stroke="#ea580c" stroke-width="12"
                                            stroke-dasharray="{{ $dashBaru }} {{ $circ - $dashBaru }}" stroke-dashoffset="0" />

                                        <!-- Diproses (Amber) -->
                                        <circle cx="60" cy="60" r="52" fill="none" stroke="#f59e0b" stroke-width="12"
                                            stroke-dasharray="{{ $dashDiproses }} {{ $circ - $dashDiproses }}" stroke-dashoffset="{{ -$dashBaru }}" />

                                        <!-- Dikirim (Sky) -->
                                        <circle cx="60" cy="60" r="52" fill="none" stroke="#0ea5e9" stroke-width="12"
                                            stroke-dasharray="{{ $dashDikirim }} {{ $circ - $dashDikirim }}" stroke-dashoffset="{{ -($dashBaru + $dashDiproses) }}" />

                                        <!-- Selesai (Emerald) -->
                                        <circle cx="60" cy="60" r="52" fill="none" stroke="#10b981" stroke-width="12"
                                            stroke-dasharray="{{ $dashSelesai }} {{ $circ - $dashSelesai }}" stroke-dashoffset="{{ -($dashBaru + $dashDiproses + $dashDikirim) }}" />
                                    @endif
                                </svg>

                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-2xl font-black text-slate-900 leading-tight">{{ $totalOrders }}</span>
                                    <span class="text-[11px] font-semibold text-slate-400">Pesanan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Legend Status -->
                        <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-2 text-slate-600">
                                    <span class="w-2.5 h-2.5 rounded-full bg-orange-600"></span>
                                    <span>Baru</span>
                                </span>
                                <span class="font-bold text-slate-900">{{ $newOrdersCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-2 text-slate-600">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span>Diproses</span>
                                </span>
                                <span class="font-bold text-slate-900">{{ $processingOrdersCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-2 text-slate-600">
                                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                    <span>Dikirim</span>
                                </span>
                                <span class="font-bold text-slate-900">{{ $shippedOrdersCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-2 text-slate-600">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span>Selesai</span>
                                </span>
                                <span class="font-bold text-slate-900">{{ $completedOrdersCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Pesanan Terbaru Table (Matching Mockup) -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Pesanan Terbaru</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi pesanan terakhir yang masuk ke toko Anda</p>
                    </div>
                    <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 hover:text-orange-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded">
                        <span>Lihat Semua</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-[700px] w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">No. Pesanan</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Produk</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Pembeli</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Total</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($recentOrders as $so)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <!-- No. Pesanan -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('seller.orders.show', $so) }}" class="font-bold text-slate-900 hover:text-orange-600 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded">
                                            #{{ $so->order_number }}
                                        </a>
                                    </td>

                                    <!-- Produk -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $firstItem = $so->items->first();
                                                $imageUrl = $firstItem?->product?->thumbnail_url;
                                            @endphp
                                            <div class="w-9 h-9 rounded-xl bg-slate-100 overflow-hidden shrink-0 flex items-center justify-center border border-slate-200/60">
                                                @if($imageUrl)
                                                    <img src="{{ $imageUrl }}" alt="{{ $firstItem->product_name ?? 'Item' }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="truncate max-w-[180px]">
                                                <div class="font-bold text-slate-900 truncate">
                                                    {{ $firstItem?->product_name ?? 'Produk Toko' }}
                                                </div>
                                                @if($so->items->count() > 1)
                                                    <div class="text-[10px] text-slate-400">+{{ $so->items->count() - 1 }} produk lainnya</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Pembeli -->
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700 font-medium">
                                        {{ $so->order?->user?->name ?? 'Pembeli NexaMarket' }}
                                    </td>

                                    <!-- Total -->
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900">
                                        {{ $so->formattedSubtotal() }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $so->status->badgeColor() }}">
                                            {{ $so->status->label() }}
                                        </span>
                                    </td>

                                    <!-- Tanggal -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-slate-500">
                                        {{ $so->created_at->translatedFormat('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <p class="font-medium text-slate-600">Belum ada pesanan masuk</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Pesanan dari pembeli akan otomatis muncul di tabel ini.</p>
                                        <div class="mt-3">
                                            <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-orange-600 hover:text-orange-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded">
                                                <span>+ Tambah Produk Baru</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. Peringatan Stok Kritis (Critical Stock Alert Section for Contract Testing) -->
            @if ($criticalStockList->isNotEmpty())
                <div class="bg-amber-50/60 rounded-2xl border border-amber-200/80 p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-amber-900">Peringatan Stok Kritis (Perlu Restok Segera)</h4>
                                <p class="text-xs text-amber-700 mt-0.5">Produk berikut memiliki sisa stok 5 unit atau kurang.</p>
                            </div>
                        </div>
                        <a href="{{ route('seller.products.index') }}" class="text-xs font-bold text-amber-900 hover:text-amber-800 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 rounded">
                            Atur Stok &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($criticalStockList as $prod)
                            <div class="bg-white p-3.5 rounded-xl border border-amber-200/70 flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h5 class="font-bold text-xs text-slate-900 truncate">{{ $prod->name }}</h5>
                                    <p class="text-[11px] text-rose-600 font-semibold mt-0.5">Tersisa: {{ $prod->stock }} unit</p>
                                </div>
                                <a href="{{ route('seller.products.edit', $prod) }}" class="px-2.5 py-1 text-[11px] font-bold bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600">
                                    Restok
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
