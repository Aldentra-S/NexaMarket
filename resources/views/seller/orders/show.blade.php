<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-600 transition">Seller Center</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('seller.orders.index') }}" class="hover:text-emerald-600 transition">Pesanan Toko</a>
                    <span>&rsaquo;</span>
                    <span class="text-gray-800 font-mono font-medium">{{ $shopOrder->order_number }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-xl text-gray-900 leading-tight">
                        Detail Pemenuhan Pesanan
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $shopOrder->status->badgeColor() }}">
                        {{ $shopOrder->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1 font-mono">
                    No. Sub-Order: <span class="font-bold text-gray-800">{{ $shopOrder->order_number }}</span> &bull; Toko: {{ $shopOrder->shop_name }}
                </p>
            </div>
            <div>
                <a href="{{ route('seller.orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Warning if Unpaid -->
            @if($shopOrder->status->isPending())
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-medium flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <strong class="font-bold text-amber-950 block text-sm">Pesanan Belum Siap Diproses</strong>
                        <p class="mt-0.5 text-amber-800">
                            Pesanan ini belum dibayar oleh pembeli dan belum dapat diproses. Jangan mengirim barang sebelum status berubah menjadi sudah dibayar.
                        </p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Items & Buyer Info -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Products Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-900 text-sm">Daftar Produk Pesanan</h3>
                            <span class="text-xs text-gray-500 font-mono">{{ $shopOrder->items->count() }} Produk ({{ $shopOrder->totalQuantity() }} item)</span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($shopOrder->items as $item)
                                <div class="p-6 flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                            @if($item->product && $item->product->primaryImage)
                                                <img src="{{ Storage::url($item->product->primaryImage->image_path) }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm">{{ $item->product_name }}</h4>
                                            <div class="text-xs text-gray-500 mt-1 space-x-2">
                                                <span>Harga Satuan: <strong class="font-semibold text-gray-700">{{ $item->formattedUnitPrice() }}</strong></span>
                                                <span>&bull;</span>
                                                <span>Kuantitas: <strong class="font-semibold text-gray-700">{{ $item->quantity }}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs text-gray-400 block">Subtotal Item</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $item->formattedSubtotal() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Subtotal summary -->
                        <div class="p-6 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Total Subtotal Toko</span>
                            <span class="text-lg font-black text-gray-900">{{ $shopOrder->formattedSubtotal() }}</span>
                        </div>
                    </div>

                    <!-- Buyer & Notes Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
                        <h3 class="font-bold text-gray-900 text-sm">Informasi Pembeli & Catatan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                                <span class="text-gray-400 font-medium block">Nama Pembeli</span>
                                <span class="font-bold text-gray-900 mt-1 block text-sm">{{ $shopOrder->order->user->name ?? 'Pembeli' }}</span>
                                <span class="text-gray-500 mt-0.5 block">{{ $shopOrder->order->user->email ?? '-' }}</span>
                            </div>
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                                <span class="text-gray-400 font-medium block">Catatan Pesanan dari Pembeli</span>
                                <p class="text-gray-800 mt-1 italic font-medium">
                                    {{ $shopOrder->order->notes ?: 'Tidak ada catatan khusus.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary & Fulfillment Action Card -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Fulfillment Control Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
                        <h3 class="font-bold text-gray-900 text-sm">Status Pemenuhan (Fulfillment)</h3>
                        
                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Status Saat Ini:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold border {{ $shopOrder->status->badgeColor() }}">
                                    {{ $shopOrder->status->label() }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">No. Parent Order:</span>
                                <span class="font-mono font-bold text-gray-900">{{ $shopOrder->order->order_number ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Tanggal Checkout:</span>
                                <span class="text-gray-700 font-medium">{{ $shopOrder->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-gray-500">Waktu Pembayaran:</span>
                                <span class="text-gray-700 font-medium">{{ $shopOrder->order->paid_at?->translatedFormat('d M Y, H:i') ?? 'Belum Dibayar' }}</span>
                            </div>
                        </div>

                        <!-- Action Controls (Placeholder for 6B / 6C) -->
                        <div id="fulfillment-actions" class="pt-4 border-t border-gray-100">
                            @if($shopOrder->status->isPaid())
                                <p class="text-xs text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-100 mb-3">
                                    Pembayaran terverifikasi! Pesanan siap diproses oleh penjual.
                                </p>
                            @elseif($shopOrder->status->isPending())
                                <p class="text-xs text-amber-700 bg-amber-50 p-3 rounded-xl border border-amber-100">
                                    Menunggu Pembayaran. Aksi pemrosesan akan aktif secara otomatis setelah pembayaran pembeli diverifikasi.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
