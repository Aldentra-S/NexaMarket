<x-marketplace-layout>
    <x-slot name="title">
        Detail Pesanan {{ $order->order_number }} | NexaMarket
    </x-slot>

    <div class="py-8 sm:py-10 bg-slate-50 min-h-[calc(100vh-18rem)]">
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
                <!-- Left Column: Child Shop Orders & Items -->
                <div class="lg:col-span-8 space-y-6">
                    @foreach($order->shopOrders as $shopOrder)
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                            <!-- Shop Header -->
                            <div class="bg-orange-50/60 px-5 sm:px-6 py-3.5 border-b border-orange-100 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-slate-500 block">Child Order: {{ $shopOrder->order_number }}</span>
                                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mt-0.5">
                                        {{ $shopOrder->shop_name }}
                                        <span class="text-[11px] bg-orange-100 text-orange-700 font-semibold px-2 py-0.5 rounded-full border border-orange-200">Toko Mitra</span>
                                    </h3>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-slate-500 block">Subtotal Toko</span>
                                    <span class="font-bold text-orange-600 text-sm">{{ $shopOrder->formattedSubtotal() }}</span>
                                </div>
                            </div>

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
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <span class="text-xs text-slate-500 block">Total Pembayaran</span>
                        <span class="text-2xl sm:text-3xl font-black text-orange-600 block mt-0.5">{{ $order->formattedTotal() }}</span>
                    </div>

                    <!-- Payment Simulation Container (Phase 5B Checkpoint B4) -->
                    <div id="payment-action-container" class="pt-2">
                        @if($order->isPending())
                            <div class="space-y-4">
                                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                                    <div class="flex items-center gap-1.5 font-bold text-amber-900">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Menunggu Pembayaran
                                    </div>
                                    <p class="leading-relaxed text-amber-800">Selesaikan pembayaran untuk memverifikasi pesanan Anda ke mitra toko.</p>
                                </div>

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

                                @if(! app()->isProduction() && config('app.env') !== 'production' && ! config('services.midtrans.is_production'))
                                    <!-- Success Simulation -->
                                    <form action="{{ route('orders.payment.simulate', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="result" value="success">
                                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-semibold text-xs transition flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Simulasi Cepat Sukses (Lokal QA)
                                        </button>
                                    </form>

                                    <!-- Failure and Expired Testing Controls -->
                                    <div class="pt-2 border-t border-slate-100 space-y-2">
                                        <span class="text-xs font-semibold text-slate-500 block text-center">
                                            Opsi Uji Coba Skenario (QA Mock)
                                        </span>
                                        <div class="grid grid-cols-2 gap-2">
                                            <form action="{{ route('orders.payment.simulate', $order) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="result" value="failed">
                                                <button type="submit" class="w-full py-2 px-3 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 text-xs font-medium transition text-center">
                                                    Simulasi Gagal
                                                </button>
                                            </form>
                                            <form action="{{ route('orders.payment.simulate', $order) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="result" value="expired">
                                                <button type="submit" class="w-full py-2 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 text-xs font-medium transition text-center">
                                                    Simulasi Expired
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-slate-400 text-center leading-tight">
                                        * Simulasi pembayaran lokal NexaMarket (tanpa gateway uang riil).
                                    </p>
                                @endif
                            </div>
                        @elseif($order->isPaid())
                            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-bold text-emerald-900">Pembayaran Berhasil Diverifikasi</p>
                                    <p class="mt-0.5 text-emerald-800">Lunas pada {{ $order->paid_at ? $order->paid_at->translatedFormat('d M Y, H:i') : '-' }} via Simulasi Pembayaran Lokal.</p>
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
                                    onSuccess: function() {
                                        window.location.reload();
                                    },
                                    onPending: function() {
                                        window.location.reload();
                                    },
                                    onError: function() {
                                        window.location.reload();
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
