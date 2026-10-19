<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Seller Center — ') }} {{ $shop->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Pusat kendali dan manajemen operasional toko resmi Anda di NexaMarket</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                Toko Terverifikasi (Approved)
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Shop Overview Banner -->
            <div class="bg-gradient-to-r from-emerald-700 to-teal-800 rounded-2xl p-6 sm:p-8 text-white shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[11px] font-bold tracking-widest uppercase bg-white/20 px-2.5 py-1 rounded-md">
                            Mitra Penjual Resmi
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-black mt-2">{{ $shop->name }}</h3>
                        <p class="text-xs text-emerald-100 font-mono mt-1">Domain Toko: nexamarket.test/shop/{{ $shop->slug }}</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-4 rounded-xl text-xs space-y-1">
                        <div class="text-emerald-200">Disetujui Sejak:</div>
                        <div class="font-bold text-white text-sm">{{ $shop->approved_at?->format('d M Y') ?? 'Terverifikasi' }}</div>
                    </div>
                </div>
            </div>

            <!-- Operational Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Shop Profile Card -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Identitas Toko</h4>
                    <div class="mt-4 space-y-2 text-xs">
                        <div><strong class="text-gray-600">Pemilik:</strong> <span class="text-gray-800 font-medium">{{ $user->name }}</span></div>
                        <div><strong class="text-gray-600">Email:</strong> <span class="text-gray-800 font-medium">{{ $user->email }}</span></div>
                        <div><strong class="text-gray-600">Telepon:</strong> <span class="text-gray-800 font-medium">{{ $shop->phone ?: '-' }}</span></div>
                        <div><strong class="text-gray-600">Alamat:</strong> <span class="text-gray-800 font-medium">{{ $shop->address ?: '-' }}</span></div>
                    </div>
                </div>

                <!-- Products Readiness Card (Phase 3 placeholder) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Katalog Produk</h4>
                    <p class="text-xs text-gray-600 mt-3 leading-relaxed">
                        Manajemen katalog, matriks varian (ukuran/warna), galeri foto, dan stok produk akan aktif pada <strong>Phase 3</strong>.
                    </p>
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <span class="inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md">
                            Siap untuk Phase 3 (Catalog)
                        </span>
                    </div>
                </div>

                <!-- Orders & Ledger Readiness Card (Phase 4-6 placeholder) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pesanan & Saldo Toko</h4>
                    <p class="text-xs text-gray-600 mt-3 leading-relaxed">
                        Pemisahan sub-order per toko, pelacakan nomor resi pengiriman, dan buku besar keuangan (ledger) otomatis.
                    </p>
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <span class="inline-flex items-center text-[11px] font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-md">
                            Siap untuk Phase 4 & 6
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
