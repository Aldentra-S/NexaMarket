<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-xl text-slate-900 leading-tight">
                        Detail Pemenuhan Pesanan
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $shopOrder->status->badgeColor() }}">
                        {{ $shopOrder->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    No. Sub-Order: <span class="font-bold text-slate-800">{{ $shopOrder->order_number }}</span> &bull; Toko: {{ $shopOrder->shop_name }}
                </p>
            </div>
            <div>
                <a href="{{ route('seller.orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Warning if Unpaid -->
            @if($shopOrder->status->isPending())
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-medium flex items-start gap-3">
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
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-sm">Daftar Produk Pesanan</h3>
                            <span class="text-xs text-slate-500">{{ $shopOrder->items->count() }} Produk ({{ $shopOrder->totalQuantity() }} item)</span>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @foreach($shopOrder->items as $item)
                                <div class="p-6 flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                            @if($item->product?->thumbnail_url)
                                                <img src="{{ $item->product->thumbnail_url }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</h4>
                                            <div class="text-xs text-slate-500 mt-1 space-x-2">
                                                <span>Harga Satuan: <strong class="font-semibold text-slate-700">{{ $item->formattedUnitPrice() }}</strong></span>
                                                <span>&bull;</span>
                                                <span>Jumlah: <strong class="font-semibold text-slate-700">{{ $item->quantity }}x</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-slate-900">{{ $item->formattedSubtotal() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Subtotal Footer -->
                        <div class="p-6 bg-slate-50 border-t border-slate-100 space-y-2 text-xs">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Subtotal Produk:</span>
                                <span class="font-bold text-slate-900">{{ $shopOrder->formattedSubtotal() }}</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Ongkos Kirim Toko:</span>
                                <span class="font-bold text-slate-900">{{ $shopOrder->formattedShippingCost() }}</span>
                            </div>
                            <div class="pt-2 border-t border-slate-200/80 flex justify-between items-center">
                                <span class="font-bold text-slate-800 text-sm">Total Pesanan Toko:</span>
                                <span class="text-lg font-black text-orange-600">{{ $shopOrder->formattedTotal() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Buyer Info Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm">Informasi Pembeli & Pengiriman</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-500 block font-semibold">Identitas Pembeli</span>
                                <div class="font-bold text-slate-900 text-sm">{{ $shopOrder->order->user->name ?? 'Pembeli NexaMarket' }}</div>
                                <div class="text-slate-600">{{ $shopOrder->order->user->email ?? '-' }}</div>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-500 block font-semibold">Penerima & Alamat Kirim</span>
                                @if($shopOrder->order->hasShippingAddress())
                                    <div class="font-bold text-slate-900">{{ $shopOrder->order->shipping_recipient_name }} ({{ $shopOrder->order->shipping_phone }})</div>
                                    <div class="text-slate-700 leading-relaxed mt-1">
                                        {{ $shopOrder->order->formattedShippingAddress() }}
                                    </div>
                                    @if($shopOrder->order->shipping_delivery_notes)
                                        <div class="text-amber-800 bg-amber-50/80 px-2 py-1 rounded text-[11px] mt-1.5 border border-amber-200/60 inline-block">
                                            <span class="font-semibold">Catatan Kurir:</span> {{ $shopOrder->order->shipping_delivery_notes }}
                                        </div>
                                    @endif
                                @else
                                    <div class="text-slate-700 leading-relaxed">
                                        {{ $shopOrder->order->shipping_address ?? 'Alamat standar terdaftar di profil pembeli' }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if(!empty($shopOrder->order->notes))
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                                <span class="text-slate-500 block font-semibold">Catatan Pembeli</span>
                                <div class="text-slate-800 leading-relaxed">{{ $shopOrder->order->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Action & Status Card -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
                        <h3 class="font-bold text-slate-900 text-sm">Tindakan Pemenuhan</h3>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Status Saat Ini:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $shopOrder->status->badgeColor() }}">
                                    {{ $shopOrder->status->label() }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Waktu Pembayaran:</span>
                                <span class="font-medium text-slate-800">
                                    {{ $shopOrder->order->paid_at ? $shopOrder->order->paid_at->format('d M Y, H:i') : 'Belum Dibayar' }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Action Controls -->
                        <div class="pt-2">
                            @if($shopOrder->status->isPaid())
                                <div class="space-y-3">
                                    <p class="text-xs text-emerald-800 bg-emerald-50 p-3 rounded-xl border border-emerald-100 leading-relaxed">
                                        Pembayaran telah diverifikasi. Mulai persiapkan barang dan kemas pesanan untuk segera dikirim ke pembeli.
                                    </p>
                                    <form method="POST" action="{{ route('seller.orders.status.update', $shopOrder) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                                            Mulai Proses Pesanan &rarr;
                                        </button>
                                    </form>
                                </div>
                            @elseif($shopOrder->status->isProcessing())
                                <div class="space-y-4">
                                    <p class="text-xs text-sky-800 bg-sky-50 p-3 rounded-xl border border-sky-100 leading-relaxed">
                                        Pesanan sedang diproses. Bila pesanan telah selesai dikemas, pilih kurir pengiriman dan masukkan nomor resi untuk mengirimkan barang ke pembeli.
                                    </p>
                                    <form method="POST" action="{{ route('seller.orders.status.update', $shopOrder) }}" class="space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="shipped">

                                        <div>
                                            <label for="courier" class="block text-xs font-bold text-slate-700 mb-1">
                                                Kurir Pengiriman <span class="text-rose-500">*</span>
                                            </label>
                                            <select id="courier" name="courier" required class="w-full text-xs rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                                <option value="">-- Pilih Kurir Pengiriman --</option>
                                                @foreach(\App\Models\ShopOrder::COURIERS as $courierKey => $courierName)
                                                    <option value="{{ $courierKey }}" @selected(old('courier', $shopOrder->courier) === $courierKey)>{{ $courierName }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label for="tracking_number" class="block text-xs font-bold text-slate-700 mb-1">
                                                Nomor Resi (Tracking Number) <span class="text-rose-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                id="tracking_number"
                                                name="tracking_number"
                                                value="{{ old('tracking_number', $shopOrder->tracking_number) }}"
                                                required
                                                placeholder="Contoh: JP1234567890"
                                                maxlength="100"
                                                class="w-full text-xs rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 uppercase font-mono tracking-wider"
                                            >
                                        </div>

                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white text-xs font-bold rounded-xl transition shadow-xs gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                            </svg>
                                            Tandai Telah Dikirim & Input Resi
                                        </button>
                                    </form>
                                </div>
                            @elseif($shopOrder->status->isShipped())
                                <div class="space-y-4" x-data="{ copied: false }">
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-500 font-semibold">Kurir Pengiriman:</span>
                                            <span class="font-bold text-slate-900 px-2 py-0.5 rounded-md bg-white border border-slate-200">{{ $shopOrder->courier ?? 'Manual' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-500 font-semibold">Nomor Resi:</span>
                                            <div class="flex items-center gap-1.5 font-mono font-bold text-slate-900">
                                                <span>{{ $shopOrder->tracking_number ?? '-' }}</span>
                                                @if($shopOrder->tracking_number)
                                                    <button
                                                        type="button"
                                                        @click="navigator.clipboard.writeText('{{ $shopOrder->tracking_number }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                        class="p-1 rounded hover:bg-slate-200 text-slate-500 hover:text-slate-800 transition"
                                                        title="Salin Resi"
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
                                        @if($shopOrder->shipped_at)
                                            <div class="flex justify-between items-center text-slate-500">
                                                <span>Waktu Kirim:</span>
                                                <span class="font-medium text-slate-700">{{ $shopOrder->shipped_at->format('d M Y, H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('seller.orders.status.update', $shopOrder) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition">
                                            Selesaikan Pesanan &rarr;
                                        </button>
                                    </form>
                                </div>
                            @elseif($shopOrder->status->isCompleted())
                                <div class="space-y-4" x-data="{ copied: false }">
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs space-y-2">
                                        <strong class="font-bold text-sm block mb-1">Pesanan Telah Selesai</strong>
                                        <p class="text-slate-600 leading-relaxed">
                                            Seluruh alur pemenuhan pesanan ini telah selesai dan tercatat resmi di sistem marketplace.
                                        </p>
                                        @if($shopOrder->courier || $shopOrder->tracking_number)
                                            <div class="pt-2 border-t border-slate-200/80 space-y-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-slate-500">Kurir:</span>
                                                    <span class="font-bold text-slate-800">{{ $shopOrder->courier ?? '-' }}</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-slate-500">Resi:</span>
                                                    <div class="flex items-center gap-1.5 font-mono font-bold text-slate-800">
                                                        <span>{{ $shopOrder->tracking_number ?? '-' }}</span>
                                                        @if($shopOrder->tracking_number)
                                                            <button
                                                                type="button"
                                                                @click="navigator.clipboard.writeText('{{ $shopOrder->tracking_number }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                                class="p-0.5 rounded hover:bg-slate-200 text-slate-500 hover:text-slate-800"
                                                                title="Salin Resi"
                                                            >
                                                                <svg x-show="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                                </svg>
                                                                <svg x-show="copied" x-cloak class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($shopOrder->status->isPending())
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-xs">
                                    Menunggu pembayaran dari pembeli via sistem digital sebelum pesanan dapat diproses.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
