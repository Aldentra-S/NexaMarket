<x-marketplace-layout>
    <x-slot name="title">
        NexaMarket — Pasar Digital Terpercaya Multi-Vendor
    </x-slot>

    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-b from-emerald-50/60 via-white to-white py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm mb-6">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    Platform Multi-Vendor Modern Berbasis Laravel 13 & Livewire
                </div>

                <h1 class="text-4xl sm:text-6xl font-black text-gray-900 tracking-tight leading-tight">
                    Ekosistem Belanja & Jual Beli <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">Modern Terpadu</span>
                </h1>

                <p class="mt-6 text-base sm:text-lg text-gray-600 leading-relaxed">
                    NexaMarket menghubungkan ribuan pembeli dengan penjual terpercaya di seluruh Indonesia. Dilengkapi sistem pemisahan sub-order per toko, buku besar otomatis, dan perlindungan transaksi terjamin.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 text-center">
                            Buka Dashboard Saya ({{ auth()->user()->role->label() }}) &rarr;
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 text-center">
                            Daftar Akun Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 bg-white hover:bg-gray-50 text-gray-800 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition text-center">
                            Masuk ke Akun
                        </a>
                    @endauth
                </div>

                <!-- Live Metrics Highlights -->
                <div class="mt-12 pt-8 border-t border-gray-200 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-gray-900">100%</div>
                        <div class="text-xs text-gray-500 font-medium mt-0.5">Lokal & Mandiri (Rp0)</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">PostgreSQL 17</div>
                        <div class="text-xs text-gray-500 font-medium mt-0.5">Integritas Transaksi</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-teal-600">Livewire 3</div>
                        <div class="text-xs text-gray-500 font-medium mt-0.5">Reaktif Tanpa SPA Bloat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Pillars / Architecture Highlights -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-600">Peran & Keamanan</h2>
                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">Didesain dengan Otorisasi Ketat</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Buyer Card -->
                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-200/80 hover:border-emerald-300 transition hover:shadow-md">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xl mb-6">
                        🛍️
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Peran Pembeli (Buyer)</h3>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Role Default Registrasi Publik</p>
                    <p class="text-sm text-gray-600 mt-4 leading-relaxed">
                        Menjelajahi katalog multi-vendor, checkout belanja dengan pemisahan sub-order per penjual secara otomatis, simulasi pembayaran aman, dan perlindungan garansi refund.
                    </p>
                </div>

                <!-- Seller Card -->
                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-200/80 hover:border-emerald-300 transition hover:shadow-md">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xl mb-6">
                        🏪
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Peran Penjual (Seller)</h3>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Melalui Verifikasi Toko</p>
                    <p class="text-sm text-gray-600 mt-4 leading-relaxed">
                        Onboarding toko profesional, manajemen produk varian matriks (SKU, harga, stok), pemenuhan pesanan mandiri, dan buku besar keuangan toko (ledger) transparan.
                    </p>
                </div>

                <!-- Admin Card -->
                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-200/80 hover:border-emerald-300 transition hover:shadow-md">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xl mb-6">
                        🛡️
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Peran Administrator</h3>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Proteksi Akses Terisolasi</p>
                    <p class="text-sm text-gray-600 mt-4 leading-relaxed">
                        Persetujuan dan verifikasi toko baru, arbitrase sengketa refund, pengaturan tarif komisi platform, dan monitoring laporan omzet/GMV platform.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-marketplace-layout>
