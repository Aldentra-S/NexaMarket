<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-xl text-slate-900 leading-tight">
                        Rincian Pesanan: {{ $order->order_number }}
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $order->status->badgeColor() }}">
                        {{ $order->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }} WIB &bull; Pembeli: {{ $order->user->name ?? 'User' }}
                </p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-700 transition">
                &larr; Kembali ke Daftar Pesanan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Shop Orders & Items (8 cols) -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Shop Orders Breakdown (Multi-Vendor) -->
                    @foreach($order->shopOrders as $shopOrder)
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                                <div>
                                    <span class="text-xs text-slate-500">Mitra Penjual:</span>
                                    <h3 class="font-bold text-sm text-slate-900">
                                        {{ $shopOrder->shop_name ?? ($shopOrder->shop->name ?? 'Toko') }}
                                    </h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Sub-Order: <span class="font-semibold text-slate-700">{{ $shopOrder->order_number }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $shopOrder->status->badgeColor() }}">
                                        {{ $shopOrder->status->label() }}
                                    </span>
                                </div>
                            </div>

                            <div class="divide-y divide-slate-100">
                                @foreach($shopOrder->items as $item)
                                    <div class="p-5 flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                                @if($item->product && $item->product->primaryImage)
                                                    <img src="{{ Storage::url($item->product->primaryImage->image_path) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-xs sm:text-sm">{{ $item->product_name }}</h4>
                                                <div class="text-[11px] text-slate-500 mt-0.5">
                                                    {{ $item->formattedUnitPrice() }} &times; {{ $item->quantity }} unit
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs sm:text-sm font-bold text-slate-900">{{ $item->formattedSubtotal() }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center text-xs">
                                <span class="font-semibold text-slate-600">Subtotal Toko:</span>
                                <span class="font-bold text-slate-900">{{ $shopOrder->formattedSubtotal() }}</span>
                            </div>
                        </div>
                    @endforeach

                    <!-- Buyer & Shipping Info -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm">Informasi Pembeli & Pengiriman</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-500 block font-semibold">Identitas Akun</span>
                                <div class="font-bold text-slate-900 text-sm">{{ $order->user->name ?? 'Pembeli' }}</div>
                                <div class="text-slate-600">{{ $order->user->email ?? '-' }}</div>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-500 block font-semibold">Alamat Penerima</span>
                                <div class="text-slate-800 leading-relaxed">
                                    {{ $order->shipping_address ?? 'Alamat terdaftar di profil akun pembeli' }}
                                </div>
                            </div>
                        </div>

                        @if(!empty($order->notes))
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                                <span class="text-slate-500 block font-semibold">Catatan Pembeli</span>
                                <div class="text-slate-800 leading-relaxed">{{ $order->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Payment & Summary Info (4 cols) -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Grand Total Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm">Ringkasan Pembayaran</h3>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Status Pembayaran:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $order->status->badgeColor() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Waktu Pembayaran:</span>
                                <span class="font-medium text-slate-800">
                                    {{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : 'Belum dibayar' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Jumlah Toko Terlibat:</span>
                                <span class="font-semibold text-slate-900">{{ $order->shopOrders->count() }} Toko</span>
                            </div>

                            <div class="flex justify-between items-center pt-2">
                                <span class="text-sm font-bold text-slate-900">Total Transaksi:</span>
                                <span class="text-lg font-bold text-emerald-700">{{ $order->formattedTotal() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Logs (Midtrans Records) -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm">Riwayat Transaksi Gateway</h3>

                        @if($order->payments->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($order->payments as $payment)
                                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-800">{{ $payment->payment_number }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $payment->status->badgeColor() }}">
                                                {{ $payment->status->label() }}
                                            </span>
                                        </div>
                                        <div class="text-slate-600">
                                            Metode: <strong class="text-slate-800">{{ strtoupper($payment->payment_method) }}</strong>
                                        </div>
                                        <div class="text-slate-500 text-[11px]">
                                            Nominal: {{ $payment->formattedAmount() }}
                                        </div>
                                        @if($payment->gateway_transaction_id)
                                            <div class="text-slate-400 text-[10px]">
                                                Gateway ID: {{ $payment->gateway_transaction_id }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-500">Belum ada riwayat transaksi pembayaran tercatat.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
