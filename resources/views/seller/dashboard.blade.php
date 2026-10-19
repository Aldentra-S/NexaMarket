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

                <!-- Products Management Card (Phase 3 Active) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Katalog Produk Toko</h4>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Phase 3 Aktif
                            </span>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="block text-xl font-black text-gray-900">{{ $totalProducts }}</span>
                                <span class="block text-[10px] uppercase font-bold text-gray-400 mt-0.5">Total</span>
                            </div>
                            <div class="p-2.5 bg-emerald-50/60 rounded-lg border border-emerald-100">
                                <span class="block text-xl font-black text-emerald-700">{{ $publishedProducts }}</span>
                                <span class="block text-[10px] uppercase font-bold text-emerald-600 mt-0.5">Tayang</span>
                            </div>
                            <div class="p-2.5 bg-amber-50/60 rounded-lg border border-amber-100">
                                <span class="block text-xl font-black text-amber-700">{{ $draftProducts }}</span>
                                <span class="block text-[10px] uppercase font-bold text-amber-600 mt-0.5">Draft</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 leading-relaxed">
                            Kelola harga, stok inventaris, status publikasi, dan spesifikasi produk toko Anda.
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                        <a href="{{ route('seller.products.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition">
                            Lihat Semua &rarr;
                        </a>
                        <a href="{{ route('seller.products.create') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
                            + Tambah Produk
                        </a>
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
