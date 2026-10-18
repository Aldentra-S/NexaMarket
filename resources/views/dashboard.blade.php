<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ auth()->user()->role->badgeColor() }}">
                Peran: {{ auth()->user()->role->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Account Overview Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Halo, {{ auth()->user()->name }}! 👋
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Anda berhasil masuk sebagai <span class="font-semibold text-gray-900">{{ auth()->user()->role->label() }}</span> di NexaMarket.
                            </p>
                        </div>
                        <div class="text-xs text-gray-500 bg-gray-50 px-3.5 py-2 rounded-lg border border-gray-200">
                            Email terdaftar: <span class="font-mono text-gray-700">{{ auth()->user()->email }}</span>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Buyer Portal Card -->
                        <div class="p-5 rounded-xl border {{ auth()->user()->isBuyer() ? 'border-blue-300 bg-blue-50/50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Area Pembeli</span>
                                @if (auth()->user()->isBuyer())
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-blue-600 text-white rounded-full">Aktif</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 mt-2">
                                Jelajahi katalog multi-vendor, kelola keranjang belanja, checkout aman, dan pantau status pesanan Anda.
                            </p>
                        </div>

                        <!-- Seller Portal Card -->
                        <div class="p-5 rounded-xl border {{ auth()->user()->isSeller() ? 'border-emerald-300 bg-emerald-50/50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Seller Center</span>
                                @if (auth()->user()->isSeller())
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-600 text-white rounded-full">Aktif</span>
                                @else
                                    <span class="text-[10px] font-semibold text-gray-500">Pendaftaran Toko (Fase 2)</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 mt-2">
                                Manajemen katalog produk, variasi harga & stok, resi pengiriman, serta buku besar keuangan toko.
                            </p>
                        </div>

                        <!-- Admin Portal Card -->
                        <div class="p-5 rounded-xl border {{ auth()->user()->isAdmin() ? 'border-purple-300 bg-purple-50/50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-purple-700">Admin Control</span>
                                @if (auth()->user()->isAdmin())
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-purple-600 text-white rounded-full">Aktif</span>
                                @else
                                    <span class="text-[10px] font-semibold text-gray-400">Akses Terbatas</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 mt-2">
                                Verifikasi approval toko baru, moderasi sengketa refund, kontrol komisi platform, dan analitik GMV.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
