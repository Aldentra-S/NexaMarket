<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight leading-tight">
                    {{ __('Profil Saya') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Pengaturan Profil Akun &bull; Kelola data identitas akun, status kemitraan toko, dan kredensial keamanan akun Anda.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white hover:border-orange-300 transition">
                    <span>&larr;</span> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $user = auth()->user();
        $shop = $user->shop;
    @endphp

    <div x-data="{ logoutModalOpen: false }" @keydown.escape.window="logoutModalOpen = false" class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Status Messages -->
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <!-- 1. Kartu Informasi Akun (Overview) -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-4 sm:gap-5">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-orange-600 text-white font-black text-2xl sm:text-3xl flex items-center justify-center shrink-0 shadow-xs">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h2>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $user->role->badgeColor() }}">
                                    {{ $user->role->label() }}
                                </span>
                                @if ($user->hasVerifiedEmail())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Belum Terverifikasi
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500">{{ $user->email }}</p>
                            <p class="text-[11px] text-slate-400">
                                Bergabung sejak {{ $user->created_at->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                        <a href="#edit-profil" class="flex-1 sm:flex-none text-center px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:border-orange-300 hover:text-orange-600 transition">
                            Edit Profil
                        </a>
                        <button type="button" @click="logoutModalOpen = true" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-bold text-rose-700 hover:bg-rose-100 transition cursor-pointer">
                            Keluar (Logout)
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[11px] font-medium text-slate-400 block">Total Pesanan</span>
                        <span class="text-xl font-black text-slate-900 mt-1 block">{{ $user->orders()->count() }}</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[11px] font-medium text-slate-400 block">Item di Keranjang</span>
                        <span class="text-xl font-black text-slate-900 mt-1 block">{{ $user->cart?->totalQuantity() ?? 0 }}</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[11px] font-medium text-slate-400 block">Status Akun</span>
                        <span class="text-sm font-bold text-emerald-600 mt-1 block">Aktif</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[11px] font-medium text-slate-400 block">Toko Kemitraan</span>
                        <span class="text-sm font-bold text-slate-800 mt-1 block">
                            @if ($shop && $shop->isApproved())
                                {{ $shop->name }}
                            @elseif ($shop && $shop->isPending())
                                Menunggu Review
                            @elseif ($shop && $shop->isRejected())
                                Perlu Revisi
                            @elseif ($shop && $shop->isSuspended())
                                Ditangguhkan
                            @else
                                Belum Terdaftar
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Bagian Kemitraan Toko / Mulai Berjualan (Sesuai Status Nyata Pengguna) -->
            <div id="buka-toko" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 scroll-mt-6">
                <div class="pb-4 border-b border-slate-100 mb-6">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">Kemitraan Toko & Penjual</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola status dan operasional toko multi-vendor Anda di NexaMarket</p>
                </div>

                @if ($user->isAdmin())
                    <!-- Status 6: Admin -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs font-bold">
                                    ADM
                                </span>
                                <h4 class="text-sm font-bold text-slate-900">Akses Platform Administrator</h4>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed max-w-xl">
                                Akun Anda memiliki wewenang penuh pada Admin Center untuk mengelola verifikasi toko mitra, moderasi produk, sengketa pesanan, dan laporan bisnis.
                            </p>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                            <span>Masuk Admin Center</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                @elseif ($user->canAccessSellerCenter())
                    <!-- Status 4 & 5: Toko Aktif / Seller -->
                    <div class="p-6 rounded-2xl bg-orange-50/40 border border-orange-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5">
                                <span class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-base font-bold text-slate-900">{{ $shop->name }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Toko Aktif &bull; Disetujui
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">Slug: <span class="font-medium text-slate-700">{{ $shop->slug }}</span></p>
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed max-w-xl">
                                Toko Anda telah resmi terverifikasi. Gunakan Seller Center untuk menambah produk, mengatur variasi inventaris, memproses pengiriman pesanan, dan memantau pendapatan toko.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                            <a href="{{ route('seller.dashboard') }}" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs transition shadow-xs text-center">
                                Buka Seller Center &rarr;
                            </a>
                            <a href="{{ route('seller.shop.settings') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:border-orange-300 font-semibold text-xs transition text-center">
                                Pengaturan Toko
                            </a>
                        </div>
                    </div>

                @elseif ($shop && $shop->isPending())
                    <!-- Status 2: Pengajuan Toko Sedang Menunggu Persetujuan -->
                    <div class="p-6 rounded-2xl bg-amber-50/50 border border-amber-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5">
                                <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center font-bold text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-base font-bold text-slate-900">{{ $shop->name }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Menunggu Persetujuan
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">Diajukan pada: {{ $shop->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed max-w-xl">
                                Pengajuan toko Anda berhasil kami terima dan saat ini sedang ditinjau oleh tim Administrator NexaMarket. Anda tidak perlu mengirimkan pengajuan ulang selama proses verifikasi berlangsung.
                            </p>
                        </div>

                        <div>
                            <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-amber-300 text-amber-900 hover:bg-amber-100 font-bold text-xs transition">
                                <span>Lihat Detail Pengajuan</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>

                @elseif ($shop && $shop->isRejected())
                    <!-- Status 3: Pengajuan Toko Ditolak / Perlu Revisi -->
                    <div class="p-6 rounded-2xl bg-rose-50/50 border border-rose-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5">
                                <span class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 border border-rose-200 flex items-center justify-center font-bold text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-base font-bold text-slate-900">{{ $shop->name }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            Perlu Perbaikan / Ditolak
                                        </span>
                                    </div>
                                    <p class="text-xs text-rose-700 mt-1 font-medium">
                                        Alasan: "{{ $shop->rejection_reason ?: 'Informasi atau berkas toko belum memenuhi syarat operasional.' }}"
                                    </p>
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed max-w-xl">
                                Anda dapat memperbarui informasi atau deskripsi toko Anda dan mengajukan kembali permohonan pembukaan toko ke Admin.
                            </p>
                        </div>

                        <div>
                            <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-xs">
                                <span>Perbaiki & Ajukan Ulang</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>

                @elseif ($shop && $shop->isSuspended())
                    <!-- Status 3b: Toko Ditangguhkan -->
                    <div class="p-6 rounded-2xl bg-slate-100 border border-slate-300 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5">
                                <span class="w-10 h-10 rounded-xl bg-slate-200 text-slate-800 flex items-center justify-center font-bold text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-base font-bold text-slate-900">{{ $shop->name }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-800 border border-slate-300">
                                            Ditangguhkan
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-1">
                                        Alasan: "{{ $shop->suspension_reason ?: 'Toko ditangguhkan sementara untuk evaluasi kepatuhan.' }}"
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('buyer.shop.apply') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition">
                                <span>Lihat Status Penangguhan</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>

                @else
                    <!-- Status 1: Buyer Belum Memiliki Toko (Peluang Buka Toko) -->
                    <div class="p-6 sm:p-8 rounded-2xl bg-amber-50/70 border border-orange-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="space-y-3 max-w-xl">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 border border-orange-200 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </span>
                                <h4 class="text-base sm:text-lg font-bold text-slate-900">Buka Toko di NexaMarket & Mulai Berjualan</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                Raih ribuan calon pembeli aktif di seluruh Indonesia. Proses pendaftaran penjual cepat dan mudah, dilengkapi dashboard Seller Center untuk inventaris produk dan pesanan masuk.
                            </p>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600 pt-1">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    100% Gratis Pendaftaran
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Midtrans Automated Settlement
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Dukungan Multi-Vendor Resmi
                                </span>
                            </div>
                        </div>

                        <div class="shrink-0 w-full md:w-auto">
                            <a href="{{ route('buyer.shop.apply') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs transition shadow-xs">
                                <span>Daftar Menjadi Penjual</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 3. Formulir Pengaturan Akun (Livewire Forms) -->
            <div id="edit-profil" class="space-y-6 scroll-mt-6">
                <!-- Update Profile Information Form -->
                <div class="p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-2xs">
                    <div class="max-w-xl">
                        <livewire:profile.update-profile-information-form />
                    </div>
                </div>

                <!-- Update Password Form -->
                <div class="p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-2xs">
                    <div class="max-w-xl">
                        <livewire:profile.update-password-form />
                    </div>
                </div>

                <!-- Delete User Account Form -->
                <div class="p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-2xs">
                    <div class="max-w-xl">
                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>

        </div>

        <!-- Custom Logout Confirmation Modal (No Window Alert) -->
        <div 
            x-show="logoutModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto" 
            aria-labelledby="profile-logout-modal-title" 
            role="dialog" 
            aria-modal="true"
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Modal Backdrop -->
                <div 
                    x-show="logoutModalOpen" 
                    x-transition:enter="transition-opacity ease-out duration-200" 
                    x-transition:enter-start="opacity-0" 
                    x-transition:enter-end="opacity-100" 
                    x-transition:leave="transition-opacity ease-out duration-150" 
                    x-transition:leave-start="opacity-100" 
                    x-transition:leave-end="opacity-0" 
                    @click="logoutModalOpen = false" 
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" {{-- taste-skill-ignore --}}
                ></div>

                <!-- Modal Content Card -->
                <div 
                    x-show="logoutModalOpen" 
                    x-transition:enter="transition ease-out duration-200" 
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="transition ease-out duration-150" 
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-left align-middle bg-white rounded-2xl shadow-xl transition-all border border-slate-200 z-10" {{-- taste-skill-ignore --}}
                >
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900" id="profile-logout-modal-title">Konfirmasi Keluar</h3>
                            <p class="text-[11px] text-slate-500">NexaMarket Multi-Vendor</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 mt-3.5 leading-relaxed">
                        Apakah Anda yakin ingin keluar dari akun Anda? Anda harus masuk kembali untuk melanjutkan transaksi belanja.
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            @click="logoutModalOpen = false" 
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button 
                                type="submit" 
                                class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer shadow-xs"
                            >
                                Ya, Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
