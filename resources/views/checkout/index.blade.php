<x-marketplace-layout>
    <x-slot name="title">
        Checkout Pesanan | NexaMarket
    </x-slot>

    <div class="py-8 sm:py-10 min-h-[calc(100vh-18rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumbs -->
            <nav class="flex items-center text-xs text-slate-500 gap-2">
                <a href="{{ url('/') }}" class="hover:text-orange-600 transition">Beranda</a>
                <span class="text-slate-400">&rsaquo;</span>
                <a href="{{ route('cart.index') }}" class="hover:text-orange-600 transition">Keranjang</a>
                <span class="text-slate-400">&rsaquo;</span>
                <span class="font-semibold text-slate-900">Checkout</span>
            </nav>

            <!-- Page Title -->
            <div class="space-y-1">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Konfirmasi & Checkout Pesanan</h1>
                <p class="text-xs sm:text-sm text-slate-600">Periksa rincian produk dari masing-masing toko mitra sebelum menyelesaikan pembuatan pesanan.</p>
            </div>

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 text-xs sm:text-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if($hasUnavailableItems)
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 text-xs sm:text-sm">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold">Beberapa item tidak memenuhi syarat checkout</p>
                        <p class="mt-0.5 text-amber-800">Terdapat produk yang stoknya habis atau status tokonya tidak aktif. Silakan kembali ke <a href="{{ route('cart.index') }}" class="underline font-semibold text-orange-600 hover:text-orange-700">Keranjang Belanja</a> untuk menyesuaikan.</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start" x-data="{ showAddressModal: false }">
                    <!-- Left Column: Shipping Address & Multi-Vendor Grouped Orders -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Shipping Address Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                            <div class="bg-gradient-to-r from-orange-50/80 via-white to-amber-50/50 px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <h2 class="font-bold text-slate-900 text-sm sm:text-base">Alamat Pengiriman</h2>
                                </div>
                                @if($selectedAddress)
                                    <button
                                        type="button"
                                        @click="showAddressModal = true"
                                        class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition flex items-center gap-1"
                                    >
                                        Ganti Alamat
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            <div class="p-5 sm:p-6">
                                @if($selectedAddress)
                                    <input type="hidden" name="address_id" value="{{ $selectedAddress->id }}">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                        <div class="space-y-1.5 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-slate-900 text-sm">{{ $selectedAddress->recipient_name }}</span>
                                                <span class="text-xs text-slate-500 font-medium">({{ $selectedAddress->phone }})</span>
                                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $selectedAddress->label }}
                                                </span>
                                                @if($selectedAddress->is_default)
                                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 border border-orange-200">
                                                        Utama
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                                {{ $selectedAddress->address_line }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $selectedAddress->district }}, {{ $selectedAddress->city }}, {{ $selectedAddress->province }} {{ $selectedAddress->postal_code }}
                                            </p>
                                            @if($selectedAddress->delivery_notes)
                                                <p class="text-xs text-amber-800 bg-amber-50/80 rounded-lg px-2.5 py-1 mt-2 inline-block border border-amber-200/60">
                                                    <span class="font-medium">Catatan Kurir:</span> {{ $selectedAddress->delivery_notes }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4 space-y-3">
                                        <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center mx-auto">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm">Belum Ada Alamat Pengiriman</p>
                                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tambahkan alamat pengiriman Anda terlebih dahulu agar pesanan dapat dikirimkan dengan benar.</p>
                                        </div>
                                        <a
                                            href="{{ route('buyer.addresses.create', ['redirect_to_checkout' => 1]) }}"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-orange-600 text-white font-bold text-xs hover:bg-orange-700 transition"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Tambah Alamat Sekarang
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Address Selection Modal -->
                        @if($addresses->isNotEmpty())
                            <div
                                x-show="showAddressModal"
                                x-cloak
                                class="fixed inset-0 z-50 overflow-y-auto"
                                aria-labelledby="modal-title"
                                role="dialog"
                                aria-modal="true"
                            >
                                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                                    <div
                                        x-show="showAddressModal"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
                                        @click="showAddressModal = false"
                                    ></div>

                                    <div
                                        x-show="showAddressModal"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-slate-200"
                                    >
                                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                            <h3 class="text-sm font-bold text-slate-900" id="modal-title">Pilih Alamat Pengiriman</h3>
                                            <button type="button" @click="showAddressModal = false" class="text-slate-400 hover:text-slate-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="p-5 max-h-[60vh] overflow-y-auto space-y-3">
                                            @foreach($addresses as $addr)
                                                <a
                                                    href="{{ route('checkout.create', ['address_id' => $addr->id]) }}"
                                                    class="block p-4 rounded-xl border text-left transition {{ ($selectedAddress && $selectedAddress->id === $addr->id) ? 'border-orange-500 bg-orange-50/40 ring-1 ring-orange-500' : 'border-slate-200 hover:border-orange-300 hover:bg-slate-50/50' }}"
                                                >
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-bold text-slate-900 text-sm">{{ $addr->recipient_name }}</span>
                                                            <span class="text-xs text-slate-500 font-medium">({{ $addr->phone }})</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                                                                {{ $addr->label }}
                                                            </span>
                                                            @if($addr->is_default)
                                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">
                                                                    Utama
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $addr->address_line }}, {{ $addr->district }}, {{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                                                </a>
                                            @endforeach
                                        </div>

                                        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                                            <a
                                                href="{{ route('buyer.addresses.create', ['redirect_to_checkout' => 1]) }}"
                                                class="text-xs font-semibold text-orange-600 hover:text-orange-700"
                                            >
                                                + Tambah Alamat Baru
                                            </a>
                                            <button
                                                type="button"
                                                @click="showAddressModal = false"
                                                class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-white"
                                            >
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @foreach($groupedItems as $shopId => $vendorItems)
                            @php
                                $shop = $vendorItems->first()->product->shop;
                                $vendorSubtotal = $vendorItems->sum(fn ($i) => (float) bcmul((string) $i->product->price, (string) $i->quantity, 2));
                                $vendorShipping = $shippingCostsByShop[$shopId] ?? 0.00;
                                $vendorTotal = $vendorSubtotal + $vendorShipping;
                            @endphp
                            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                                <!-- Shop Header -->
                                <div class="bg-orange-50/60 px-4 sm:px-6 py-3.5 border-b border-orange-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-orange-600 text-white font-bold flex items-center justify-center text-sm shrink-0">
                                            {{ strtoupper(substr($shop->name ?? 'T', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-slate-900 text-sm flex flex-wrap items-center gap-2">
                                                <span class="truncate">{{ $shop->name ?? 'Toko Mitra' }}</span>
                                                <span class="text-[11px] bg-orange-100 text-orange-700 font-semibold px-2 py-0.5 rounded-full border border-orange-200 shrink-0">Mitra Resmi</span>
                                            </h3>
                                            <p class="text-xs text-slate-500 truncate">{{ $shop->slug ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right shrink-0">
                                        <span class="text-xs text-slate-500">Subtotal Produk:</span>
                                        <span class="font-bold text-slate-900 text-sm ml-1">Rp {{ number_format($vendorSubtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Shop Items -->
                                <div class="divide-y divide-slate-100 px-4 sm:px-6">
                                    @foreach($vendorItems as $item)
                                        @php
                                            $itemSubtotal = (float) bcmul((string) $item->product->price, (string) $item->quantity, 2);
                                        @endphp
                                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-center gap-4 min-w-0">
                                                <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-xl border border-slate-200 shrink-0 bg-slate-50">
                                                <div class="min-w-0">
                                                    <h4 class="font-bold text-slate-900 text-sm truncate">
                                                        {{ $item->product->name }}
                                                    </h4>
                                                    <div class="text-xs text-slate-500 mt-0.5">
                                                        {{ $item->quantity }} x {{ $item->product->formattedPrice() }}
                                                    </div>
                                                    @if($item->product->stock < $item->quantity)
                                                        <span class="inline-block mt-1 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                                            Stok sisa {{ $item->product->stock }} unit
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="text-sm font-black text-slate-900">
                                                    Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Shop Shipping & Total Footer -->
                                <div class="bg-slate-50/70 px-4 sm:px-6 py-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                        </svg>
                                        <span>Ongkos Kirim Toko:</span>
                                        <span class="font-bold text-slate-900">{{ $vendorShipping > 0 ? 'Rp ' . number_format($vendorShipping, 0, ',', '.') : 'Gratis Ongkir (Rp 0)' }}</span>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <span class="text-slate-500">Subtotal Toko + Ongkir:</span>
                                        <span class="font-bold text-orange-600 ml-1">Rp {{ number_format($vendorTotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Notes Box -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 space-y-2.5">
                            <label for="notes" class="block text-sm font-bold text-slate-900">
                                Catatan Pesanan <span class="font-normal text-xs text-slate-500">(Opsional)</span>
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="Contoh: Harap kemas dengan rapi, titipkan di pos satpam jika tidak ada orang."
                                class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 placeholder:text-slate-400"
                            >{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Right Column: Final Order Summary -->
                    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 sticky top-28 space-y-5">
                        <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                            <span>Ringkasan Pembayaran</span>
                            <span class="text-xs font-normal text-slate-500">{{ $groupedItems->count() }} Toko Mitra</span>
                        </h2>

                        <div class="space-y-3 text-xs sm:text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>Total Barang</span>
                                <span class="font-semibold text-slate-900">{{ $totalQuantity }} unit</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Jumlah Toko Mitra</span>
                                <span class="font-semibold text-slate-900">{{ $groupedItems->count() }} toko</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal Produk</span>
                                <span class="font-semibold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Total Ongkos Kirim</span>
                                <span class="font-semibold {{ $totalShippingCost > 0 ? 'text-slate-900' : 'text-emerald-600' }}">
                                    {{ $totalShippingCost > 0 ? 'Rp ' . number_format($totalShippingCost, 0, ',', '.') : 'Gratis' }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-xs text-slate-500 block">Total Tagihan Final</span>
                            <span class="text-2xl sm:text-3xl font-black text-orange-600 block mt-0.5">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-1">
                            @if($hasUnavailableItems)
                                <button type="button" disabled class="w-full py-3.5 px-4 rounded-xl bg-slate-200 text-slate-400 font-bold text-sm text-center cursor-not-allowed">
                                    Tidak Dapat Melanjutkan
                                </button>
                                <p class="text-xs text-center text-rose-600 mt-2">Perbaiki item di keranjang sebelum checkout.</p>
                            @elseif($addresses->isEmpty())
                                <a
                                    href="{{ route('buyer.addresses.create', ['redirect_to_checkout' => 1]) }}"
                                    class="w-full py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm text-center transition flex items-center justify-center gap-2"
                                >
                                    + Tambah Alamat Pengiriman
                                </a>
                                <p class="text-xs text-center text-amber-700 mt-2">Tambahkan alamat terlebih dahulu untuk melanjutkan.</p>
                            @else
                                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white font-bold text-sm text-center transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Buat Pesanan & Bayar
                                </button>
                            @endif
                        </div>

                        <div class="text-center pt-2">
                            <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-600 transition">
                                &larr; Kembali ke Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-marketplace-layout>
