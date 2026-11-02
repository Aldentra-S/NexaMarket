<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight">
                    Rincian Pengajuan Toko: {{ $shop->name }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Evaluasi kelayakan pendaftaran toko sebelum memberikan keputusan persetujuan</p>
            </div>
            <a href="{{ route('admin.shops.index') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-coral-600 dark:hover:text-coral-400 transition">
                &larr; Kembali ke Daftar Pengajuan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('status') || session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm font-medium">
                    {{ session('status') ?? session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-800 dark:text-rose-300 text-xs sm:text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Shop Details Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-[#E8EAF0] dark:border-slate-800 overflow-hidden shadow-card">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Profil Calon Toko</span>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $shop->name }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Domain URL: /shop/{{ $shop->slug }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $shop->status->badgeColor() }}">
                            Status: {{ $shop->status->label() }}
                        </span>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400">Informasi Pemilik Akun</h4>
                            <div class="mt-2 text-slate-900 dark:text-white font-bold">{{ $shop->user->name ?? 'User' }}</div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">{{ $shop->user->email ?? '-' }}</div>
                            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1">Peran akun: <span class="capitalize font-medium text-slate-700 dark:text-slate-300">{{ $shop->user->role->label() }}</span></div>
                        </div>

                        <div>
                            <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400">Waktu Pengajuan</h4>
                            <div class="mt-2 text-slate-900 dark:text-white font-medium">{{ $shop->created_at->format('d M Y, H:i') }} WIB</div>
                            @if ($shop->approved_at)
                                <div class="text-xs text-emerald-700 dark:text-emerald-400 mt-1 font-semibold">Disetujui pada: {{ $shop->approved_at->format('d M Y, H:i') }} WIB</div>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400">Deskripsi Toko</h4>
                            <p class="mt-2 text-slate-800 dark:text-slate-200 text-xs sm:text-sm leading-relaxed whitespace-pre-line bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                                {{ $shop->description ?: 'Tidak ada deskripsi yang disertakan.' }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nomor Kontak Toko</h4>
                            <p class="mt-1 text-slate-800 dark:text-slate-200 text-xs">{{ $shop->phone ?: '-' }}</p>
                        </div>

                        <div>
                            <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400">Alamat Operasional</h4>
                            <p class="mt-1 text-slate-800 dark:text-slate-200 text-xs">{{ $shop->address ?: '-' }}</p>
                        </div>

                        @if ($shop->rejection_reason)
                            <div class="md:col-span-2 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60">
                                <h4 class="text-xs font-semibold text-rose-800 dark:text-rose-300">Catatan Penolakan Sebelumnya</h4>
                                <p class="mt-1 text-xs text-rose-700 dark:text-rose-400 italic">"{{ $shop->rejection_reason }}"</p>
                            </div>
                        @endif

                        @if ($shop->suspension_reason)
                            <div class="md:col-span-2 p-4 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-semibold text-slate-800 dark:text-slate-200">Catatan Penangguhan Toko (Suspension)</h4>
                                    @if ($shop->suspended_at)
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Ditangguhkan: {{ $shop->suspended_at->format('d M Y, H:i') }} WIB</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-slate-700 dark:text-slate-300 italic">"{{ $shop->suspension_reason }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Action Buttons Section -->
                <div class="p-6 bg-slate-50/80 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        Pastikan data toko telah sesuai dengan pedoman syarat & ketentuan NexaMarket.
                    </span>

                    <div class="flex items-center space-x-3 w-full sm:w-auto">
                        <!-- Reject Button / Form trigger (Only for Pending or Approved) -->
                        @if ($shop->isPending())
                            <div x-data="{ openRejectModal: false }">
                                <button type="button" @click="openRejectModal = true" class="px-4 py-2 bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 text-xs font-semibold rounded-xl border border-rose-200 dark:border-rose-900/60 transition">
                                    Tolak Pengajuan
                                </button>

                                <!-- Reject Modal -->
                                <div x-show="openRejectModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
                                    <div @click.outside="openRejectModal = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-xl">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Konfirmasi Penolakan Pengajuan</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sertakan alasan penolakan agar pemohon dapat memperbaiki dokumen atau nama toko.</p>

                                        <form method="POST" action="{{ route('admin.shops.reject', $shop) }}" class="mt-4 space-y-4">
                                            @csrf
                                            <div>
                                                <label for="rejection_reason" class="block font-medium text-xs text-slate-700 dark:text-slate-300">Alasan Penolakan</label>
                                                <textarea 
                                                    id="rejection_reason" 
                                                    name="rejection_reason" 
                                                    rows="3" 
                                                    class="mt-1 block w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 focus:border-rose-500 focus:ring-rose-500 rounded-xl text-xs text-slate-900 dark:text-white" 
                                                    required 
                                                    placeholder="Contoh: Nama toko melanggar hak cipta merek lain atau deskripsi tidak jelas..."
                                                ></textarea>
                                                <x-input-error class="mt-1" :messages="$errors->get('rejection_reason')" />
                                            </div>

                                            <div class="flex justify-end space-x-2 pt-2">
                                                <button type="button" @click="openRejectModal = false" class="px-3 py-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-4 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-semibold rounded-xl transition">
                                                    Kirim Penolakan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Suspend Button / Modal trigger (Only for Approved shop) -->
                        @if ($shop->isApproved())
                            <div x-data="{ openSuspendModal: false }">
                                <button type="button" @click="openSuspendModal = true" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-xl transition">
                                    Tangguhkan Toko (Suspend)
                                </button>

                                <!-- Suspend Modal -->
                                <div x-show="openSuspendModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
                                    <div @click.outside="openSuspendModal = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-xl">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Konfirmasi Penangguhan Toko</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Penangguhan akan membatasi etalase toko dan membekukan operasional penjual sementara waktu.</p>

                                        <form method="POST" action="{{ route('admin.shops.suspend', $shop) }}" class="mt-4 space-y-4">
                                            @csrf
                                            <div>
                                                <label for="suspension_reason" class="block font-medium text-xs text-slate-700 dark:text-slate-300">Alasan Penangguhan Toko</label>
                                                <textarea 
                                                    id="suspension_reason" 
                                                    name="suspension_reason" 
                                                    rows="3" 
                                                    class="mt-1 block w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 focus:border-slate-500 focus:ring-slate-500 rounded-xl text-xs text-slate-900 dark:text-white" 
                                                    required 
                                                    placeholder="Contoh: Toko terindikasi melakukan transaksi mencurigakan atau pelanggaran kebijakan..."
                                                ></textarea>
                                                <x-input-error class="mt-1" :messages="$errors->get('suspension_reason')" />
                                            </div>

                                            <div class="flex justify-end space-x-2 pt-2">
                                                <button type="button" @click="openSuspendModal = false" class="px-3 py-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white text-xs font-semibold rounded-xl transition">
                                                    Tangguhkan Toko
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Reactivate Form (Only for Suspended shop) -->
                        @if ($shop->isSuspended())
                            <form method="POST" action="{{ route('admin.shops.reactivate', $shop) }}">
                                @csrf
                                <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition">
                                    Aktifkan Kembali Toko
                                </button>
                            </form>
                        @endif

                        <!-- Approve Form (Only for Pending shop) -->
                        @if ($shop->isPending())
                            <form method="POST" action="{{ route('admin.shops.approve', $shop) }}">
                                @csrf
                                <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition">
                                    Setujui Toko (Approve)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
