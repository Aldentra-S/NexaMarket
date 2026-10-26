<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Performa Bisnis Platform - NexaMarket</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        body {
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            padding: 28px 32px;
            background: #ffffff;
        }
        .header {
            border-bottom: 2px solid #ea580c;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #ea580c;
            letter-spacing: -0.5px;
        }
        .brand-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .meta-cell {
            text-align: right;
            font-size: 10px;
            color: #475569;
        }
        .meta-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #0f172a;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .report-heading {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .report-subheading {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 16px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 20px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            vertical-align: top;
            width: 25%;
        }
        .kpi-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }
        .kpi-value.highlight {
            color: #047857;
        }
        .kpi-desc {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 3px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 10px;
        }
        .data-table th {
            background: #f1f5f9;
            color: #475569;
            text-align: left;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .data-table tr:nth-child(even) td {
            background: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .footer {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
        }
        .footer-table {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand-title">NexaMarket</div>
                    <div class="brand-subtitle">Platform Multi-Vendor E-Commerce &bull; Admin Center</div>
                </td>
                <td class="meta-cell">
                    <span class="meta-badge">Laporan Resmi</span>
                    <div>Waktu Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                    <div>Dicetak oleh: {{ auth()->user()?->name ?? 'Administrator' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-heading">Laporan Performa Bisnis Platform</div>
    <div class="report-subheading">Ringkasan analitik perputaran transaksi, volume penjualan produk, dan kinerja mitra toko</div>

    <!-- KPI Summary Grid -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <div class="kpi-label">Gross Merchandise Value</div>
                <div class="kpi-value highlight">Rp {{ number_format($totalGmv, 0, ',', '.') }}</div>
                <div class="kpi-desc">Akumulasi transaksi sukses</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Barang Terjual</div>
                <div class="kpi-value">{{ number_format($totalSoldUnits, 0, ',', '.') }} unit</div>
                <div class="kpi-desc">Volume kuantitas terjual</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Pesanan Selesai</div>
                <div class="kpi-value">{{ number_format($completedOrdersCount, 0, ',', '.') }} pesanan</div>
                <div class="kpi-desc">Diterima oleh pembeli</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Total Akumulasi</div>
                <div class="kpi-value">{{ number_format($totalOrdersCount, 0, ',', '.') }} pesanan</div>
                <div class="kpi-desc">Seluruh pesanan terdaftar</div>
            </td>
        </tr>
    </table>

    <!-- Section 1: Mitra Penjual Aktif -->
    <div class="section-title">Mitra Penjual Aktif Terverifikasi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th>Nama Toko</th>
                <th>Slug / Domain</th>
                <th class="text-center" style="width: 90px;">Total Produk</th>
                <th class="text-center" style="width: 90px;">Pesanan Masuk</th>
                <th class="text-center" style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topShops as $index => $shop)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $shop->name }}</strong></td>
                    <td>{{ $shop->slug }}</td>
                    <td class="text-center">{{ $shop->products_count }} produk</td>
                    <td class="text-center">{{ $shop->shop_orders_count }} order</td>
                    <td class="text-center"><span class="badge badge-success">Approved</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 12px; color: #94a3b8;">Belum ada mitra toko yang disetujui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Section 2: Distribusi Produk per Kategori -->
    <div class="section-title">Distribusi Katalog per Kategori Produk</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th>Kategori Produk</th>
                <th>Slug Kategori</th>
                <th class="text-center" style="width: 120px;">Jumlah Produk Aktif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categoriesReport as $index => $category)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td>{{ $category->slug }}</td>
                    <td class="text-center">{{ $category->products_count }} produk</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 12px; color: #94a3b8;">Belum ada data kategori.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Section 3: Transaksi Berhasil Terbaru -->
    <div class="section-title">Transaksi Berbayar &amp; Selesai Terbaru</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 80px;">No. Pesanan</th>
                <th style="width: 100px;">Waktu Pembayaran</th>
                <th>Pembeli</th>
                <th>Toko Mitra Terlibat</th>
                <th class="text-right" style="width: 110px;">Nilai Transaksi</th>
                <th class="text-center" style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentSettlements as $order)
                <tr>
                    <td><strong>{{ $order->order_number ?? '#' . $order->id }}</strong></td>
                    <td>{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        {{ $order->user->name ?? 'User #' . $order->user_id }}
                        <div style="font-size: 8px; color: #64748b;">{{ $order->user->email ?? '' }}</div>
                    </td>
                    <td>
                        @php
                            $shopNames = $order->shopOrders->pluck('shop.name')->filter()->unique()->implode(', ');
                        @endphp
                        {{ $shopNames ?: '-' }}
                    </td>
                    <td class="text-right"><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                    <td class="text-center"><span class="badge badge-success">{{ strtoupper($order->status->value ?? (string) $order->status) }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 12px; color: #94a3b8;">Belum ada transaksi berbayar yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>&copy; {{ date('Y') }} NexaMarket Platform Administrasi &bull; Dokumen Rahasia Perusahaan</td>
                <td class="text-right">Halaman 1 dari 1</td>
            </tr>
        </table>
    </div>
</body>
</html>
