<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard Admin</h1>
                <p class="text-xs text-slate-500 mt-1">Dashboard Administrasi Platform &bull; Pantau aktivitas dan performa NexaMarket.</p>
            </div>
        </div>

        <!-- 4 KPI Headline Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Pengguna -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-500">Total Pengguna Terdaftar</p>
                    <p class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                        {{ number_format($totalUsers, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span>{{ $buyerCount }} pembeli &bull; {{ $sellerCount }} penjual</span>
                    </p>
                </div>
            </div>

            <!-- 2. Toko Aktif -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-500">Toko Aktif</p>
                    <p class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                        {{ number_format($approvedShopsCount, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        @if($pendingShopsCount > 0)
                            <span class="text-amber-600">{{ $pendingShopsCount }} toko menunggu review</span>
                        @else
                            <span>Kemitraan beroperasi penuh</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- 3. Total Produk -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-500">Total Produk</p>
                    <p class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                        {{ number_format($totalProducts, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span>{{ $publishedProductsCount }} tayang di katalog</span>
                    </p>
                </div>
            </div>

            <!-- 4. GMV Platform -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-500">Gross Merchandise Value (GMV)</p>
                    <p class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                        Rp {{ number_format($totalGmv, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span>{{ $paidOrdersCount }} transaksi selesai</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Middle Section: Performa Transaksi & Status Toko -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left: Performa Transaksi (Chart) -->
            <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Performa Transaksi</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Tren riil nilai transaksi (GMV) dan volume pesanan 6 bulan terakhir</p>
                    </div>
                    <!-- Chart Legend -->
                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                            <span class="text-slate-600">GMV (Rp)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-slate-600">Pesanan</span>
                        </div>
                    </div>
                </div>

                <!-- SVG Performance Chart -->
                @php
                    $chartPoints = $monthlyPerformance ?? collect();
                    $maxGmv = $chartPoints->max('gmv') ?: 1000000;
                    $maxOrders = $chartPoints->max('orders') ?: 10;
                    $chartWidth = 600;
                    $chartHeight = 180;
                    $paddingLeft = 50;
                    $paddingRight = 30;
                    $usableWidth = $chartWidth - $paddingLeft - $paddingRight;
                    $stepX = $chartPoints->count() > 1 ? $usableWidth / ($chartPoints->count() - 1) : 0;

                    $gmvCoords = [];
                    $orderCoords = [];
                    foreach ($chartPoints as $idx => $pt) {
                        $x = $paddingLeft + ($idx * $stepX);
                        $gmvRatio = $maxGmv > 0 ? ($pt['gmv'] / $maxGmv) : 0;
                        $orderRatio = $maxOrders > 0 ? ($pt['orders'] / $maxOrders) : 0;
                        $yGmv = $chartHeight - 30 - ($gmvRatio * 110);
                        $yOrder = $chartHeight - 30 - ($orderRatio * 110);
                        $gmvCoords[] = ['x' => $x, 'y' => $yGmv, 'val' => $pt['gmv'], 'month' => $pt['month']];
                        $orderCoords[] = ['x' => $x, 'y' => $yOrder, 'val' => $pt['orders'], 'month' => $pt['month']];
                    }

                    $gmvPath = '';
                    $gmvArea = '';
                    if (count($gmvCoords) > 0) {
                        $gmvPath = 'M ' . $gmvCoords[0]['x'] . ' ' . $gmvCoords[0]['y'];
                        $gmvArea = 'M ' . $gmvCoords[0]['x'] . ' ' . ($chartHeight - 30) . ' L ' . $gmvCoords[0]['x'] . ' ' . $gmvCoords[0]['y'];
                        for ($i = 1; $i < count($gmvCoords); $i++) {
                            $gmvPath .= ' L ' . $gmvCoords[$i]['x'] . ' ' . $gmvCoords[$i]['y'];
                            $gmvArea .= ' L ' . $gmvCoords[$i]['x'] . ' ' . $gmvCoords[$i]['y'];
                        }
                        $gmvArea .= ' L ' . end($gmvCoords)['x'] . ' ' . ($chartHeight - 30) . ' Z';
                    }

                    $orderPath = '';
                    if (count($orderCoords) > 0) {
                        $orderPath = 'M ' . $orderCoords[0]['x'] . ' ' . $orderCoords[0]['y'];
                        for ($i = 1; $i < count($orderCoords); $i++) {
                            $orderPath .= ' L ' . $orderCoords[$i]['x'] . ' ' . $orderCoords[$i]['y'];
                        }
                    }
                @endphp

                <div class="relative w-full overflow-hidden pt-2">
                    <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-48 select-none" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="gmvGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#ea580c" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#ea580c" stop-opacity="0.0" />
                            </linearGradient>
                        </defs>

                        <!-- Background Horizontal Gridlines -->
                        <line x1="{{ $paddingLeft }}" y1="30" x2="{{ $chartWidth - $paddingRight }}" y2="30" stroke="#f1f5f9" stroke-width="1" />
                        <line x1="{{ $paddingLeft }}" y1="65" x2="{{ $chartWidth - $paddingRight }}" y2="65" stroke="#f1f5f9" stroke-width="1" />
                        <line x1="{{ $paddingLeft }}" y1="105" x2="{{ $chartWidth - $paddingRight }}" y2="105" stroke="#f1f5f9" stroke-width="1" />
                        <line x1="{{ $paddingLeft }}" y1="{{ $chartHeight - 30 }}" x2="{{ $chartWidth - $paddingRight }}" y2="{{ $chartHeight - 30 }}" stroke="#e2e8f0" stroke-width="1.2" />

                        <!-- Left Y-Axis Scale Values -->
                        <text x="{{ $paddingLeft - 8 }}" y="34" font-size="9" fill="#94a3b8" text-anchor="end">
                            {{ $maxGmv >= 1000000 ? round($maxGmv / 1000000, 1) . ' jt' : number_format($maxGmv, 0) }}
                        </text>
                        <text x="{{ $paddingLeft - 8 }}" y="109" font-size="9" fill="#94a3b8" text-anchor="end">
                            {{ $maxGmv >= 1000000 ? round(($maxGmv / 2) / 1000000, 1) . ' jt' : number_format($maxGmv / 2, 0) }}
                        </text>
                        <text x="{{ $paddingLeft - 8 }}" y="{{ $chartHeight - 27 }}" font-size="9" fill="#94a3b8" text-anchor="end">0</text>

                        @if(!empty($gmvArea))
                            <!-- GMV Area Under Curve -->
                            <path d="{{ $gmvArea }}" fill="url(#gmvGradient)" />
                            <!-- GMV Line -->
                            <path d="{{ $gmvPath }}" fill="none" stroke="#ea580c" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                        @endif

                        @if(!empty($orderPath))
                            <!-- Orders Line -->
                            <path d="{{ $orderPath }}" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        @endif

                        <!-- Point Dots & Labels -->
                        @foreach($gmvCoords as $pt)
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3.5" fill="#ffffff" stroke="#ea580c" stroke-width="2" />
                            <text x="{{ $pt['x'] }}" y="{{ $chartHeight - 12 }}" font-size="10" fill="#64748b" text-anchor="middle" font-weight="500">
                                {{ $pt['month'] }}
                            </text>
                        @endforeach

                        @foreach($orderCoords as $pt)
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3" fill="#ffffff" stroke="#0284c7" stroke-width="2" />
                        @endforeach
                    </svg>
                </div>
            </div>

            <!-- Right: Status Toko -->
            @php
                $pendingPercent = $totalShops > 0 ? round(($pendingShopsCount / $totalShops) * 100, 1) : 0;
                $approvedPercent = $totalShops > 0 ? round(($approvedShopsCount / $totalShops) * 100, 1) : 0;
                $suspendedPercent = $totalShops > 0 ? round(($suspendedShopsCount / $totalShops) * 100, 1) : 0;
            @endphp
            <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Status Toko</h2>
                        <a href="{{ route('admin.shops.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                            Lihat semua &rarr;
                        </a>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Komposisi kemitraan penjual platform</p>

                    <!-- Status Items -->
                    <div class="space-y-4 mt-6">
                        <!-- 1. Menunggu -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 border border-orange-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-slate-800">Menunggu</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-slate-900">{{ $pendingShopsCount }}</span>
                                    <span class="text-[10px] text-slate-400 ml-1">({{ $pendingPercent }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-orange-500 h-2 rounded-full transition-[width] duration-300" style="width: {{ $pendingPercent }}%"></div>
                            </div>
                        </div>

                        <!-- 2. Disetujui -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-slate-800">Disetujui</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-slate-900">{{ $approvedShopsCount }}</span>
                                    <span class="text-[10px] text-slate-400 ml-1">({{ $approvedPercent }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full transition-[width] duration-300" style="width: {{ $approvedPercent }}%"></div>
                            </div>
                        </div>

                        <!-- 3. Ditangguhkan / Ditolak -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-slate-800">Ditangguhkan</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-slate-900">{{ $suspendedShopsCount }}</span>
                                    <span class="text-[10px] text-slate-400 ml-1">({{ $suspendedPercent }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-rose-500 h-2 rounded-full transition-[width] duration-300" style="width: {{ $suspendedPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-6 flex items-center justify-between text-xs text-slate-500">
                    <span>Total Kemitraan Toko</span>
                    <span class="font-bold text-slate-900">{{ $totalShops }} toko</span>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Aktivitas Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Aktivitas Terbaru</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Log peristiwa sistem, permohonan toko, dan transaksi terakhir</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                    Lihat semua &rarr;
                </a>
            </div>

            @if(isset($recentActivities) && $recentActivities->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-3.5 px-6">Aktivitas</th>
                                <th class="py-3.5 px-6">Pengguna</th>
                                <th class="py-3.5 px-6">Waktu</th>
                                <th class="py-3.5 px-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentActivities as $item)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl {{ $item['type'] === 'shop' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-sky-50 text-sky-600 border border-sky-100' }} flex items-center justify-center shrink-0">
                                                @if($item['type'] === 'shop')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ $item['link'] }}" class="font-bold text-slate-800 hover:text-orange-600 transition">
                                                    {{ $item['title'] }}
                                                </a>
                                                <p class="text-[11px] text-slate-400 capitalize">{{ $item['type'] }} event</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-slate-700">
                                        <p class="font-semibold text-slate-800">{{ $item['user_name'] }}</p>
                                        <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 uppercase">
                                            {{ $item['user_role'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 text-[11px]">
                                        {{ $item['created_at']->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $item['status_badge'] }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 px-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">Belum ada aktivitas terekam</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-sm mx-auto">
                        Ketika mitra mengajukan toko baru atau pembeli menyelesaikan checkout pesanan, aktivitas sistem akan otomatis muncul di sini.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
