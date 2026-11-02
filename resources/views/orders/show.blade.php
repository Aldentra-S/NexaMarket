<x-marketplace-layout>
    <x-slot name="title">
        Detail Pesanan {{ $order->order_number }} | NexaMarket
    </x-slot>

    <div class="py-8 sm:py-10 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumbs -->
            <nav class="flex items-center text-xs text-slate-500 gap-2">
                <a href="{{ url('/') }}" class="hover:text-orange-600 transition">Beranda</a>
                <span class="text-slate-400">&rsaquo;</span>
                <a href="{{ route('orders.index') }}" class="hover:text-orange-600 transition">Riwayat Pesanan</a>
                <span class="text-slate-400">&rsaquo;</span>
                <span class="font-semibold text-slate-900">{{ $order->order_number }}</span>
            </nav>

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Detail Pesanan</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $order->status->badgeColor() }}">
                            {{ $order->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">
                        No. Order: <span class="font-semibold text-slate-800">{{ $order->order_number }}</span> &bull; Dibuat pada {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:text-orange-600 hover:border-orange-300 text-slate-700 font-semibold text-xs transition">
                        <span>&larr;</span> Riwayat Pesanan
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
                <!-- Left Column: Shipping Address & Child Shop Orders -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Shipping Information Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 space-y-3">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm sm:text-base">Informasi Pengiriman</h3>
                        </div>

                        <div class="space-y-1.5 text-xs sm:text-sm">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-slate-900">{{ $order->shipping_recipient_name ?: $order->user->name }}</span>
                                @if($order->shipping_phone)
                                    <span class="text-xs text-slate-500 font-medium">({{ $order->shipping_phone }})</span>
                                @endif
                            </div>
                            <p class="text-slate-700 leading-relaxed text-xs sm:text-sm">
                                {{ $order->hasShippingAddress() ? $order->formattedShippingAddress() : ($order->shipping_address ?? 'Alamat standar profil pembeli') }}
                            </p>
                            @if($order->shipping_delivery_notes)
                                <p class="text-xs text-amber-800 bg-amber-50/80 rounded-lg px-2.5 py-1 mt-2 inline-block border border-amber-200/60">
                                    <span class="font-medium">Catatan Kurir:</span> {{ $order->shipping_delivery_notes }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @foreach($order->shopOrders as $shopOrder)
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden" x-data="{ copied: false }">
                            <!-- Shop Header -->
                            <div class="bg-orange-50/60 px-5 sm:px-6 py-3.5 border-b border-orange-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <span class="text-xs text-slate-500 block">Child Order: {{ $shopOrder->order_number }}</span>
                                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mt-0.5">
                                        {{ $shopOrder->shop_name }}
                                        <span class="text-[11px] bg-orange-100 text-orange-700 font-semibold px-2 py-0.5 rounded-full border border-orange-200">Toko Mitra</span>
                                    </h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $shopOrder->status->badgeColor() }}">
                                        {{ $shopOrder->status->label() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tracking Info Bar (If Shipped or Completed) -->
                            @if($shopOrder->isShipped() || $shopOrder->courier || $shopOrder->tracking_number)
                                <div class="bg-sky-50/70 border-b border-sky-100 px-5 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2 text-sky-900">
                                        <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                        </svg>
                                        <span class="font-semibold">Kurir:</span>
                                        <span class="font-bold bg-white px-2 py-0.5 rounded border border-sky-200">{{ $shopOrder->courier ?? 'Ekspedisi' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-600 font-semibold">No. Resi:</span>
                                        <div class="flex items-center gap-1.5 font-mono font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">
                                            <span>{{ $shopOrder->tracking_number ?? '-' }}</span>
                                            @if($shopOrder->tracking_number)
                                                <button
                                                    type="button"
                                                    @click="navigator.clipboard.writeText('{{ $shopOrder->tracking_number }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="p-0.5 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition"
                                                    title="Salin No. Resi"
                                                >
                                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Order Items -->
                            <div class="divide-y divide-slate-100 px-5 sm:px-6">
                                @foreach($shopOrder->items as $item)
                                    <div class="py-4 flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-4 min-w-0">
                                            <div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 font-bold shrink-0 overflow-hidden">
                                                @if($item->product && $item->product->thumbnail_url)
                                                    <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-slate-900 text-sm truncate">
                                                    {{ $item->product_name }}
                                                </h4>
                                                <div class="text-xs text-slate-500 mt-0.5">
                                                    {{ $item->quantity }} unit x {{ $item->formattedUnitPrice() }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-sm font-black text-slate-900">
                                                {{ $item->formattedSubtotal() }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Shop Subtotal & Shipping Footer -->
                            <div class="bg-slate-50/70 px-5 sm:px-6 py-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2 text-slate-600">
                                    <span>Ongkos Kirim:</span>
                                    <span class="font-bold text-slate-900">{{ $shopOrder->formattedShippingCost() }}</span>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-slate-500">Subtotal Toko:</span>
                                    <span class="font-bold text-orange-600 ml-1">Rp {{ number_format((float) $shopOrder->subtotal + (float) $shopOrder->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($order->notes)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 space-y-2">
                            <h4 class="text-xs font-bold text-slate-700">Catatan Pembeli</h4>
                            <p class="text-xs sm:text-sm text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-200">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Order Summary & Payment Status -->
                <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 sticky top-28 space-y-5">
                    <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                        <span>Ringkasan Tagihan</span>
                        <span class="text-xs font-normal text-slate-500">{{ $order->shopOrders->count() }} Toko</span>
                    </h2>

                    <div class="space-y-3 text-xs sm:text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Status Transaksi</span>
                            <span class="font-semibold text-slate-900">{{ $order->status->label() }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Jumlah Toko Mitra</span>
                            <span class="font-semibold text-slate-900">{{ $order->shopOrders->count() }} toko</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Total Kuantitas</span>
                            <span class="font-semibold text-slate-900">{{ $order->totalQuantity() }} barang</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold text-slate-900">{{ $order->formattedItemsSubtotal() }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Total Ongkos Kirim</span>
                            <span class="font-semibold {{ (float) $order->shipping_cost_total > 0 ? 'text-slate-900' : 'text-emerald-600' }}">
                                {{ $order->formattedShippingCost() }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <span class="text-xs text-slate-500 block">Total Pembayaran</span>
                        <span class="text-2xl sm:text-3xl font-black text-orange-600 block mt-0.5">{{ $order->formattedTotal() }}</span>
                    </div>

                    <!-- Payment Simulation Container (Phase 5B Checkpoint B4) -->
                    <div id="payment-action-container" class="pt-2">
                        @if($order->isPending())
                            @php
                                $isReturningFromGateway = request()->has('transaction_status') 
                                    || request()->has('status_code') 
                                    || request()->query('status') === 'verifying'
                                    || request()->query('status') === 'pending';
                                $pendingPayment = $order->payments->where('payment_method', 'midtrans')->where('status', \App\Enums\PaymentStatus::PENDING)->first();
                            @endphp

                            <div class="space-y-4">
                                @if($isReturningFromGateway)
                                    <!-- Waiting for Verification Banner (When returning from Midtrans) -->
                                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-xs text-amber-900 space-y-2">
                                        <div class="flex items-center gap-2 font-bold text-amber-900 text-sm">
                                            <svg class="w-5 h-5 text-amber-600 animate-pulse shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Menunggu Verifikasi Pembayaran</span>
                                        </div>
                                        <p class="leading-relaxed text-amber-800">
                                            Transaksi pembayaran Anda telah diterima oleh gateway dan sedang menunggu konfirmasi verifikasi tepercaya sistem. Status pesanan akan diperbarui secara otomatis setelah notifikasi resmi terverifikasi.
                                        </p>
                                        @if($pendingPayment)
                                            <div class="pt-1.5 border-t border-amber-200/80 text-[11px] text-amber-700 flex items-center justify-between">
                                                <span>No. Referensi: <strong class="font-mono text-amber-900">{{ $pendingPayment->payment_number }}</strong></span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200/60 text-amber-900">Diproses</span>
                                            </div>
                                        @endif
                                        <div class="pt-1">
                                            <a href="{{ route('orders.show', $order) }}?status=verifying" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                <span>Periksa Status Pembayaran</span>
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                                        <div class="flex items-center gap-1.5 font-bold text-amber-900">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Menunggu Pembayaran
                                        </div>
                                        <p class="leading-relaxed text-amber-800">Selesaikan pembayaran untuk memverifikasi pesanan Anda ke mitra toko.</p>
                                    </div>
                                @endif

                                <!-- Midtrans Snap Sandbox Payment Button -->
                                <div x-data="midtransPayment()" class="space-y-2">
                                    <template x-if="errorMessage">
                                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                                            <span x-text="errorMessage"></span>
                                        </div>
                                    </template>

                                    <button type="button"
                                            @click="payWithMidtrans"
                                            :disabled="isLoading"
                                            class="w-full py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white font-bold text-sm transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                                        <template x-if="isLoading">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                        <template x-if="!isLoading">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </template>
                                        <span x-text="isLoading ? 'Menyiapkan Pembayaran...' : 'Bayar via Midtrans Snap (Sandbox)'"></span>
                                    </button>
                                </div>
                            </div>
                        @elseif($order->isPaid())
                            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-bold text-emerald-900">Pembayaran Berhasil Diverifikasi</p>
                                    @php
                                        $successfulPayment = $order->payments->firstWhere('status', \App\Enums\PaymentStatus::SUCCESS);
                                        $methodLabel = $successfulPayment?->payment_method === 'midtrans' ? 'Midtrans Snap' : 'Simulasi Pembayaran Lokal';
                                    @endphp
                                    <p class="mt-0.5 text-emerald-800">Lunas pada {{ $order->paid_at ? $order->paid_at->translatedFormat('d M Y, H:i') : '-' }} via {{ $methodLabel }}.</p>
                                </div>
                            </div>
                        @elseif($order->isCancelled())
                            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                                <p class="font-bold text-rose-900">Pesanan Dibatalkan</p>
                                <p class="text-rose-800">Pembayaran tidak berhasil diselesaikan. Seluruh stok produk pesanan ini telah dipulihkan secara otomatis.</p>
                            </div>
                        @elseif($order->isExpired())
                            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-xs text-slate-800 space-y-1">
                                <p class="font-bold text-slate-900">Pesanan Kedaluwarsa</p>
                                <p class="text-slate-600">Batas waktu pembayaran telah habis. Seluruh stok produk pesanan ini telah dipulihkan secara otomatis.</p>
                            </div>
                        @else
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                                Status pesanan: <span class="font-semibold">{{ $order->status->label() }}</span>.
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 text-center">
                        <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-600 transition">
                            &larr; Belanja Produk Lainnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($order->isPending())
        @php
            $snapUrl = config('services.midtrans.is_production')
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js';
            $clientKey = config('services.midtrans.client_key');
        @endphp
        @if($clientKey)
            <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
        @endif
        <script>
            function midtransPayment() {
                return {
                    isLoading: false,
                    errorMessage: '',
                    async payWithMidtrans() {
                        this.errorMessage = '';
                        this.isLoading = true;

                        try {
                            const response = await fetch('{{ route('orders.payment.snap', $order) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                },
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                const errorMsg = data.errors?.order?.[0] || data.errors?.gateway?.[0] || data.message || 'Gagal memproses pembayaran Midtrans.';
                                throw new Error(errorMsg);
                            }

                            if (window.snap && typeof window.snap.pay === 'function') {
                                window.snap.pay(data.snap_token, {
                                    onSuccess: function(result) {
                                        window.location.href = '{{ route('orders.show', $order) }}?status=verifying';
                                    },
                                    onPending: function(result) {
                                        window.location.href = '{{ route('orders.show', $order) }}?status=verifying';
                                    },
                                    onError: function(result) {
                                        window.location.href = '{{ route('orders.show', $order) }}?status=error';
                                    },
                                    onClose: function() {
                                        // User closed popup without completing
                                    }
                                });
                            } else if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                throw new Error('Snap.js belum siap atau Client Key Midtrans belum dikonfigurasi di .env.');
                            }
                        } catch (err) {
                            this.errorMessage = err.message || 'Terjadi kesalahan gateway pembayaran.';
                        } finally {
                            this.isLoading = false;
                        }
                    }
                };
            }
        </script>
    @endif
</x-marketplace-layout>
