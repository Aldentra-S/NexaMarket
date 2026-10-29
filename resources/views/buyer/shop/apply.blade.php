<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Pendaftaran Toko (Seller Onboarding)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            @if ($shop && $shop->isPending())
                <!-- Pending Status Card -->
                <div class="bg-white overflow-hidden rounded-2xl border border-amber-200">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="p-2 rounded-lg bg-amber-100 text-amber-700">
                                ⏳
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Pengajuan Toko Sedang Ditinjau</h3>
                                <p class="text-xs text-slate-500">Dikirim pada {{ $shop->created_at->format('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>

                        <p class="text-sm text-slate-600 leading-relaxed">
                            Terima kasih telah mendaftar sebagai mitra penjual di NexaMarket. Tim Administrator kami sedang memverifikasi rincian toko Anda. Anda akan mendapatkan notifikasi status persetujuan sesegera mungkin.
                        </p>

                        <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-2">
                            <div><strong class="text-slate-700">Nama Toko:</strong> <span class="font-semibold text-slate-900">{{ $shop->name }}</span></div>
                            <div><strong class="text-slate-700">Deskripsi:</strong> <span class="text-slate-800">{{ $shop->description }}</span></div>
                            @if ($shop->phone)
                                <div><strong class="text-slate-700">Nomor Kontak:</strong> <span class="text-slate-800">{{ $shop->phone }}</span></div>
                            @endif
                            @if ($shop->address)
                                <div><strong class="text-slate-700">Alamat Usaha:</strong> <span class="text-slate-800">{{ $shop->address }}</span></div>
                            @endif
                            <div><strong class="text-slate-700">Status Saat Ini:</strong> 
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $shop->status->badgeColor() }}">
                                    {{ $shop->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($shop && $shop->isSuspended())
                <!-- Suspended Status Card -->
                <div class="bg-white overflow-hidden rounded-2xl border border-slate-300">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="p-2.5 rounded-xl bg-slate-100 text-slate-700">
                                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Toko Anda Sedang Ditangguhkan</h3>
                                <p class="text-xs text-slate-500">
                                    Ditangguhkan pada {{ $shop->suspended_at?->format('d M Y, H:i') ?? $shop->updated_at->format('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 space-y-2 mb-6">
                            <p class="font-bold text-slate-900 text-sm">Alasan Penangguhan Resmi oleh Administrator:</p>
                            <p class="italic text-slate-800 bg-white p-3 rounded-lg border border-slate-100 text-xs font-medium">
                                "{{ $shop->suspension_reason ?: 'Toko ditangguhkan sementara untuk evaluasi kepatuhan operasional.' }}"
                            </p>
                            <p class="text-slate-500 text-[11px] leading-relaxed pt-1">
                                Selama status penangguhan aktif, operasional toko dinonaktifkan sementara dan akses ke Seller Center dibatasi. Anda tidak dapat mengajukan toko baru selama penangguhan berlangsung.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-2">
                            <div><strong class="text-slate-700">Nama Toko:</strong> <span class="font-semibold text-slate-900">{{ $shop->name }}</span></div>
                            <div><strong class="text-slate-700">Domain Slug:</strong> <span class="text-slate-800 font-medium">{{ $shop->slug }}</span></div>
                            @if ($shop->phone)
                                <div><strong class="text-slate-700">Kontak Terdaftar:</strong> <span class="text-slate-800">{{ $shop->phone }}</span></div>
                            @endif
                            <div><strong class="text-slate-700">Status Saat Ini:</strong> 
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $shop->status->badgeColor() }}">
                                    {{ $shop->status->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <p class="text-xs text-slate-500">
                                Butuh informasi lebih lanjut mengenai status ini?
                            </p>
                            <span class="text-xs font-bold text-orange-600">
                                Layanan Pengaduan NexaCare Aktif
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Application or Re-Application Form -->
                <div class="bg-white overflow-hidden rounded-2xl border border-slate-200">
                    <div class="p-6 sm:p-8">
                        @if ($shop && $shop->isRejected())
                            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1">
                                <p class="font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-red-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Pengajuan toko Anda sebelumnya ditolak oleh Admin:
                                </p>
                                <p class="italic font-medium">"{{ $shop->rejection_reason }}"</p>
                                <p class="text-[11px] text-red-600 mt-2">Silakan perbaiki informasi di bawah ini dan kirim ulang pengajuan Anda.</p>
                            </div>
                        @endif

                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-slate-900">
                                {{ $shop && $shop->isRejected() ? 'Perbaiki & Ajukan Ulang Toko' : 'Formulir Pendaftaran Toko Baru' }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Mulai jualan di NexaMarket dengan mengisi informasi toko Anda secara akurat.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('buyer.shop.store') }}" class="space-y-6">
                            @csrf

                            <!-- Shop Name -->
                            <div>
                                <x-input-label for="name" :value="__('Nama Toko Resmi')" />
                                <x-text-input 
                                    id="name" 
                                    name="name" 
                                    type="text" 
                                    class="mt-1 block w-full" 
                                    :value="old('name', $shop?->name)" 
                                    required 
                                    autofocus 
                                    placeholder="Contoh: Toko Elektronik Berkah" 
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Description -->
                            <div>
                                <x-input-label for="description" :value="__('Deskripsi Singkat Toko')" />
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    rows="4" 
                                    class="mt-1 block w-full border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl text-sm leading-relaxed" 
                                    required 
                                    placeholder="Jelaskan jenis produk yang akan dijual dan visi toko Anda..."
                                >{{ old('description', $shop?->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <x-input-label for="phone" :value="__('Nomor Telepon / WhatsApp Operasional (Opsional)')" />
                                <x-text-input 
                                    id="phone" 
                                    name="phone" 
                                    type="text" 
                                    class="mt-1 block w-full" 
                                    :value="old('phone', $shop?->phone)" 
                                    placeholder="08123456789" 
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div>

                            <!-- Address -->
                            <div>
                                <x-input-label for="address" :value="__('Alamat Lengkap Operasional Toko (Opsional)')" />
                                <textarea 
                                    id="address" 
                                    name="address" 
                                    rows="2" 
                                    class="mt-1 block w-full border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl text-sm leading-relaxed" 
                                    placeholder="Alamat asal pengiriman pesanan toko..."
                                >{{ old('address', $shop?->address) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                                <x-primary-button>
                                    {{ $shop && $shop->isRejected() ? __('Kirim Ulang Pengajuan') : __('Ajukan Toko Sekarang') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
                                <x-primary-button>
                                    {{ $shop && $shop->isRejected() ? __('Kirim Ulang Pengajuan') : __('Ajukan Toko Sekarang') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
