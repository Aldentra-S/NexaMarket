<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ShopStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Common data aggregation for business intelligence reporting.
     */
    private function getReportData(): array
    {
        $paidStatuses = [
            OrderStatus::PAID->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::COMPLETED->value,
        ];

        // Overall GMV & totals
        $totalGmv = (float) Order::whereIn('status', $paidStatuses)->sum('total_amount');
        $totalOrdersCount = Order::count();
        $completedOrdersCount = Order::where('status', OrderStatus::COMPLETED->value)->count();
        $totalSoldUnits = (int) OrderItem::whereHas('shopOrder', function ($q) use ($paidStatuses) {
            $q->whereIn('status', $paidStatuses);
        })->sum('quantity');

        // Top Performing Shops by completed/paid order revenue
        $topShops = Shop::withCount(['products', 'shopOrders' => function ($q) use ($paidStatuses) {
            $q->whereIn('status', $paidStatuses);
        }])
        ->where('status', ShopStatus::APPROVED->value)
        ->take(15)
        ->get();

        // Categories distribution and products count
        $categoriesReport = Category::withCount('products')
            ->orderByDesc('products_count')
            ->take(15)
            ->get();

        // Recent completed transactions
        $recentSettlements = Order::with(['user', 'shopOrders.shop'])
            ->whereIn('status', $paidStatuses)
            ->latest('paid_at')
            ->take(20)
            ->get();

        return [
            'totalGmv' => $totalGmv,
            'totalOrdersCount' => $totalOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'totalSoldUnits' => $totalSoldUnits,
            'topShops' => $topShops,
            'categoriesReport' => $categoriesReport,
            'recentSettlements' => $recentSettlements,
            'generatedAt' => now(),
        ];
    }

    /**
     * Display comprehensive business and financial intelligence reports.
     */
    public function index(Request $request): View
    {
        $data = $this->getReportData();

        return view('admin.reports.index', $data);
    }

    /**
     * Export platform business intelligence report to CSV format.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $data = $this->getReportData();
        $filename = 'laporan-performa-bisnis-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Write UTF-8 BOM for Microsoft Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Report Header
            fputcsv($handle, ['NEXAMARKET - LAPORAN PERFORMA BISNIS PLATFORM']);
            fputcsv($handle, ['Waktu Ekspor', now()->format('d/m/Y H:i:s') . ' WIB']);
            fputcsv($handle, ['Diekspor Oleh', (auth()->user()?->name ?? 'Admin') . ' (' . (auth()->user()?->email ?? '-') . ')']);
            fputcsv($handle, []);

            // 1. Ringkasan Metrik KPI
            fputcsv($handle, ['=== 1. RINGKASAN METRIK PLATFORM ===']);
            fputcsv($handle, ['Metrik', 'Nilai', 'Keterangan']);
            fputcsv($handle, ['Gross Merchandise Value (GMV)', 'Rp ' . number_format($data['totalGmv'], 0, ',', '.'), 'Total akumulasi transaksi berbayar']);
            fputcsv($handle, ['Barang Terjual (Unit)', number_format($data['totalSoldUnits'], 0, ',', '.'), 'Kuantitas produk sukses terjual']);
            fputcsv($handle, ['Pesanan Selesai', number_format($data['completedOrdersCount'], 0, ',', '.'), 'Pesanan telah diterima pembeli']);
            fputcsv($handle, ['Total Akumulasi Pesanan', number_format($data['totalOrdersCount'], 0, ',', '.'), 'Seluruh pesanan masuk di sistem']);
            fputcsv($handle, []);

            // 2. Mitra Penjual Aktif
            fputcsv($handle, ['=== 2. MITRA PENJUAL AKTIF TERVERIFIKASI ===']);
            fputcsv($handle, ['No', 'Nama Toko', 'Slug Toko', 'Status Approval', 'Jumlah Produk Aktif', 'Akumulasi Pesanan Masuk']);
            foreach ($data['topShops'] as $index => $shop) {
                fputcsv($handle, [
                    $index + 1,
                    $shop->name,
                    $shop->slug,
                    $shop->status->value ?? 'approved',
                    $shop->products_count,
                    $shop->shop_orders_count,
                ]);
            }
            fputcsv($handle, []);

            // 3. Distribusi Produk per Kategori
            fputcsv($handle, ['=== 3. DISTRIBUSI PRODUK PER KATEGORI ===']);
            fputcsv($handle, ['No', 'Kategori Produk', 'Slug Kategori', 'Jumlah Produk Terdaftar']);
            foreach ($data['categoriesReport'] as $index => $category) {
                fputcsv($handle, [
                    $index + 1,
                    $category->name,
                    $category->slug,
                    $category->products_count,
                ]);
            }
            fputcsv($handle, []);

            // 4. Transaksi Berhasil Terbaru
            fputcsv($handle, ['=== 4. TRANSAKSI BERHASIL TERBARU ===']);
            fputcsv($handle, ['No. Pesanan', 'Tanggal Pembayaran', 'Nama Pembeli', 'Email Pembeli', 'Toko Mitra', 'Total Nilai (Rp)', 'Status']);
            foreach ($data['recentSettlements'] as $order) {
                $shopNames = $order->shopOrders->pluck('shop.name')->filter()->unique()->implode(', ');
                fputcsv($handle, [
                    $order->order_number ?? '#' . $order->id,
                    $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i'),
                    $order->user->name ?? '-',
                    $order->user->email ?? '-',
                    $shopNames ?: '-',
                    $order->total_amount,
                    strtoupper($order->status->value ?? (string) $order->status),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Export platform business intelligence report to PDF document.
     */
    public function exportPdf(Request $request): Response
    {
        $data = $this->getReportData();
        $filename = 'laporan-performa-bisnis-' . now()->format('Y-m-d_His') . '.pdf';

        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        if ($request->boolean('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
