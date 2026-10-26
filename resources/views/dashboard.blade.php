<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ auth()->user()->role->badgeColor() }}">
                Peran: {{ auth()->user()->role->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Account Overview Card -->
            <div class="bg-white overflow-hidden rounded-2xl border border-slate-200">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-slate-900 truncate">
                                Halo, {{ auth()->user()->name }}!
                            </h3>
                            <p class="text-sm text-slate-600 mt-1">
                                Anda berhasil masuk sebagai <span class="font-semibold text-slate-900">{{ auth()->user()->role->label() }}</span> di NexaMarket.
                            </p>
                        </div>
                        <div class="text-xs text-slate-500 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200 max-w-full break-all">
                            Email terdaftar: <span class="text-slate-800 font-medium">{{ auth()->user()->email }}</span>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Buyer Portal Card -->
                        <div class="p-5 rounded-xl border {{ auth()->user()->isBuyer() ? 'border-sky-300 bg-sky-50/50' : 'border-slate-200 bg-slate-50' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-sky-800">Area Pembeli</span>
                                @if (auth()->user()->isBuyer())
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-sky-700 text-white rounded-full">Aktif</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 mt-2">
                                Jelajahi katalog multi-vendor, kelola keranjang belanja, checkout aman, dan pantau status pesanan Anda.
                            </p>
                        </div>

                        <!-- Seller Portal Card -->
                        <div class="p-5 rounded-2xl border flex flex-col justify-between {{ auth()->user()->isSeller() ? 'border-orange-300 bg-orange-50/40' : 'border-slate-200 bg-slate-50' }}">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-orange-700">Seller Center</span>
                                    @if (auth()->user()->isSeller())
                                        <span class="text-[10px] font-bold px-2 py-0.5 bg-orange-600 text-white rounded-full">Aktif</span>
                                    @elseif (auth()->user()->shop?->isSuspended())
                                        <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-600 text-white rounded-full">Ditangguhkan</span>
                                    @elseif (auth()->user()->shop?->isPending())
                                        <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-600 text-white rounded-full">Menunggu Review</span>
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-500">Tersedia</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 mt-2">
                                    Manajemen katalog produk, variasi harga & stok, resi pengiriman, serta buku besar keuangan toko.
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200">
                                @if (auth()->user()->isSeller())
                                    <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center text-xs font-bold text-orange-600 hover:text-orange-700">
                                        Buka Seller Center &rarr;
                                    </a>
                                @elseif (auth()->user()->shop?->isSuspended())
                                    <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center text-xs font-bold text-slate-700 hover:text-slate-900">
                                        Lihat Status Penangguhan &rarr;
                                    </a>
                                @elseif (auth()->user()->shop?->isPending())
                                    <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center text-xs font-bold text-amber-800 hover:text-amber-900">
                                        Lihat Status Pengajuan &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center text-xs font-bold text-orange-600 hover:text-orange-700">
                                        Daftar Buka Toko &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Admin Portal Card -->
                        <div class="p-5 rounded-xl border flex flex-col justify-between {{ auth()->user()->isAdmin() ? 'border-slate-300 bg-slate-100' : 'border-slate-200 bg-slate-50' }}">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">Admin Control</span>
                                    @if (auth()->user()->isAdmin())
                                        <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-900 text-white rounded-full">Aktif</span>
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-500">Akses Terbatas</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 mt-2">
                                    Verifikasi approval toko baru, moderasi sengketa refund, kontrol komisi platform, dan analitik GMV.
                                </p>
                            </div>

                            @if (auth()->user()->isAdmin())
                                <div class="mt-4 pt-3 border-t border-slate-200">
                                    <a href="{{ route('admin.shops.index') }}" class="inline-flex items-center text-xs font-bold text-slate-900 hover:text-slate-800">
                                        Kelola Persetujuan Toko &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
