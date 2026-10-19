<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Rincian Pengajuan Toko: ') }} {{ $shop->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Evaluasi kelayakan pendaftaran toko sebelum menyetujui</p>
            </div>
            <a href="{{ route('admin.shops.index') }}" class="text-xs font-semibold text-gray-600 hover:text-purple-700 transition">
                &larr; Kembali ke Daftar Pengajuan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Main Shop Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-700">Profil Toko</span>
                            <h3 class="text-2xl font-black text-gray-900 mt-1">{{ $shop->name }}</h3>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">Slug: {{ $shop->slug }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $shop->status->badgeColor() }}">
                            Status: {{ $shop->status->label() }}
                        </span>
                    </div>

                    <div class="border-t border-gray-100 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase">Informasi Pemilik Akun</h4>
                            <div class="mt-2 text-gray-800 font-semibold">{{ $shop->user->name }}</div>
                            <div class="text-xs text-gray-600">{{ $shop->user->email }}</div>
                            <div class="text-xs text-gray-400 mt-1">Peran akun saat ini: <span class="capitalize font-medium text-gray-700">{{ $shop->user->role->label() }}</span></div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase">Waktu Pengajuan</h4>
                            <div class="mt-2 text-gray-800 font-medium">{{ $shop->created_at->format('d F Y, H:i:s') }} WIB</div>
                            @if ($shop->approved_at)
                                <div class="text-xs text-emerald-600 mt-1 font-semibold">Disetujui pada: {{ $shop->approved_at->format('d F Y, H:i') }} WIB</div>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-xs font-bold text-gray-500 uppercase">Deskripsi Toko</h4>
                            <p class="mt-2 text-gray-700 text-sm leading-relaxed whitespace-pre-line bg-gray-50 p-4 rounded-xl border border-gray-100">
                                {{ $shop->description }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase">Nomor Kontak</h4>
                            <p class="mt-1 text-gray-800 font-mono text-xs">{{ $shop->phone ?: '-' }}</p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase">Alamat Operasional</h4>
                            <p class="mt-1 text-gray-800 text-xs">{{ $shop->address ?: '-' }}</p>
                        </div>

                        @if ($shop->rejection_reason)
                            <div class="md:col-span-2 p-4 rounded-xl bg-red-50 border border-red-200">
                                <h4 class="text-xs font-bold text-red-800 uppercase">Catatan Penolakan Sebelumnya</h4>
                                <p class="mt-1 text-xs text-red-700 italic">"{{ $shop->rejection_reason }}"</p>
                            </div>
                        @endif

                        @if ($shop->suspension_reason)
                            <div class="md:col-span-2 p-4 rounded-xl bg-gray-100 border border-gray-300">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-gray-800 uppercase">Catatan Penangguhan Toko (Suspension)</h4>
                                    @if ($shop->suspended_at)
                                        <span class="text-[11px] text-gray-500 font-medium">Ditangguhkan: {{ $shop->suspended_at->format('d F Y, H:i') }} WIB</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-700 font-medium italic">"{{ $shop->suspension_reason }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Action Buttons Section -->
                <div class="p-6 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-xs text-gray-500">
                        Pastikan data toko telah sesuai dengan pedoman syarat & ketentuan NexaMarket.
                    </span>

                    <div class="flex items-center space-x-3 w-full sm:w-auto">
                        <!-- Reject Button / Form trigger (Only for Pending or Approved) -->
                        @if ($shop->isPending())
                            <div x-data="{ openRejectModal: false }">
                                <button type="button" @click="openRejectModal = true" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-xl border border-red-200 transition">
                                    Tolak Pengajuan
                                </button>

                                <!-- Reject Modal -->
                                <div x-show="openRejectModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;">
                                    <div @click.outside="openRejectModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100">
                                        <h3 class="text-lg font-bold text-gray-900">Konfirmasi Penolakan</h3>
                                        <p class="text-xs text-gray-500 mt-1">Wajib menyertakan alasan penolakan agar calon penjual dapat memperbaiki pengajuannya.</p>

                                        <form method="POST" action="{{ route('admin.shops.reject', $shop) }}" class="mt-4 space-y-4">
                                            @csrf
                                            <div>
                                                <x-input-label for="rejection_reason" :value="__('Alasan Penolakan')" />
                                                <textarea 
                                                    id="rejection_reason" 
                                                    name="rejection_reason" 
                                                    rows="3" 
                                                    class="mt-1 block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm text-xs" 
                                                    required 
                                                    placeholder="Contoh: Nama toko melanggar hak cipta merek lain atau deskripsi tidak jelas..."
                                                ></textarea>
                                                <x-input-error class="mt-1" :messages="$errors->get('rejection_reason')" />
                                            </div>

                                            <div class="flex justify-end space-x-2 pt-2">
                                                <button type="button" @click="openRejectModal = false" class="px-3 py-1.5 text-xs text-gray-600 hover:text-gray-900 font-medium">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm">
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
                                <button type="button" @click="openSuspendModal = true" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-xl border border-gray-300 transition">
                                    Tangguhkan Toko (Suspend)
                                </button>

                                <!-- Suspend Modal -->
                                <div x-show="openSuspendModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;">
                                    <div @click.outside="openSuspendModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100">
                                        <h3 class="text-lg font-bold text-gray-900">Konfirmasi Penangguhan Toko</h3>
                                        <p class="text-xs text-gray-500 mt-1">Penangguhan akan menonaktifkan operasional toko dan membatasi akses Seller Center pemilik akun.</p>

                                        <form method="POST" action="{{ route('admin.shops.suspend', $shop) }}" class="mt-4 space-y-4">
                                            @csrf
                                            <div>
                                                <x-input-label for="suspension_reason" :value="__('Alasan Penangguhan Toko')" />
                                                <textarea 
                                                    id="suspension_reason" 
                                                    name="suspension_reason" 
                                                    rows="3" 
                                                    class="mt-1 block w-full border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm text-xs" 
                                                    required 
                                                    placeholder="Contoh: Toko terindikasi melakukan transaksi mencurigakan atau pelanggaran kebijakan..."
                                                ></textarea>
                                                <x-input-error class="mt-1" :messages="$errors->get('suspension_reason')" />
                                            </div>

                                            <div class="flex justify-end space-x-2 pt-2">
                                                <button type="button" @click="openSuspendModal = false" class="px-3 py-1.5 text-xs text-gray-600 hover:text-gray-900 font-medium">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-4 py-1.5 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold rounded-lg shadow-sm">
                                                    Tangguhkan Toko Sekarang
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
                                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                                    Aktifkan Kembali Toko (Reactivate)
                                </button>
                            </form>
                        @endif

                        <!-- Approve Form (Only for Pending shop) -->
                        @if ($shop->isPending())
                            <form method="POST" action="{{ route('admin.shops.approve', $shop) }}">
                                @csrf
                                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
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
