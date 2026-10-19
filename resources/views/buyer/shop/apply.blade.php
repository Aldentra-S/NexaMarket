<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pendaftaran Toko (Seller Onboarding)') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-gray-600 hover:text-emerald-600 transition">
                &larr; Kembali ke Dashboard
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

            @if ($shop && $shop->isPending())
                <!-- Pending Status Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-amber-200">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="p-2 rounded-lg bg-amber-100 text-amber-700">
                                ⏳
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Pengajuan Toko Sedang Ditinjau</h3>
                                <p class="text-xs text-gray-500">Dikirim pada {{ $shop->created_at->format('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 leading-relaxed">
                            Terima kasih telah mendaftar sebagai mitra penjual di NexaMarket. Tim Administrator kami sedang memverifikasi rincian toko Anda. Anda akan mendapatkan notifikasi status persetujuan sesegera mungkin.
                        </p>

                        <div class="mt-6 p-4 rounded-xl bg-gray-50 border border-gray-100 text-xs space-y-2">
                            <div><strong class="text-gray-700">Nama Toko:</strong> <span class="font-semibold text-gray-900">{{ $shop->name }}</span></div>
                            <div><strong class="text-gray-700">Deskripsi:</strong> <span class="text-gray-800">{{ $shop->description }}</span></div>
                            @if ($shop->phone)
                                <div><strong class="text-gray-700">Nomor Kontak:</strong> <span class="text-gray-800">{{ $shop->phone }}</span></div>
                            @endif
                            @if ($shop->address)
                                <div><strong class="text-gray-700">Alamat Usaha:</strong> <span class="text-gray-800">{{ $shop->address }}</span></div>
                            @endif
                            <div><strong class="text-gray-700">Status Saat Ini:</strong> 
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $shop->status->badgeColor() }}">
                                    {{ $shop->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($shop && $shop->isSuspended())
                <!-- Suspended Status Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-300">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="p-2 rounded-lg bg-gray-100 text-gray-700 text-lg">
                                🚫
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Toko Anda Sedang Ditangguhkan</h3>
                                <p class="text-xs text-gray-500">
                                    Ditangguhkan pada {{ $shop->suspended_at?->format('d M Y, H:i') ?? $shop->updated_at->format('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-700 space-y-2 mb-6">
                            <p class="font-bold text-gray-900 text-sm">Alasan Penangguhan Resmi oleh Administrator:</p>
                            <p class="italic text-gray-800 bg-white p-3 rounded-lg border border-gray-100 text-xs font-medium">
                                "{{ $shop->suspension_reason ?: 'Toko ditangguhkan sementara untuk evaluasi kepatuhan operasional.' }}"
                            </p>
                            <p class="text-gray-500 text-[11px] leading-relaxed pt-1">
                                Selama status penangguhan aktif, operasional toko dinonaktifkan sementara dan akses ke Seller Center dibatasi. Anda tidak dapat mengajukan toko baru selama penangguhan berlangsung.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 text-xs space-y-2">
                            <div><strong class="text-gray-700">Nama Toko:</strong> <span class="font-semibold text-gray-900">{{ $shop->name }}</span></div>
                            <div><strong class="text-gray-700">Domain Slug:</strong> <span class="font-mono text-gray-600">{{ $shop->slug }}</span></div>
                            @if ($shop->phone)
                                <div><strong class="text-gray-700">Kontak Terdaftar:</strong> <span class="text-gray-800">{{ $shop->phone }}</span></div>
                            @endif
                            <div><strong class="text-gray-700">Status Saat Ini:</strong> 
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $shop->status->badgeColor() }}">
                                    {{ $shop->status->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <p class="text-xs text-gray-500">
                                Butuh informasi lebih lanjut mengenai status ini?
                            </p>
                            <span class="text-xs font-bold text-emerald-600">
                                Layanan Pengaduan NexaCare Aktif
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Application or Re-Application Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-6 sm:p-8">
                        @if ($shop && $shop->isRejected())
                            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1">
                                <p class="font-bold">⚠️ Pengajuan toko Anda sebelumnya ditolak oleh Admin:</p>
                                <p class="italic font-medium">"{{ $shop->rejection_reason }}"</p>
                                <p class="text-[11px] text-red-600 mt-2">Silakan perbaiki informasi di bawah ini dan kirim ulang pengajuan Anda.</p>
                            </div>
                        @endif

                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ $shop && $shop->isRejected() ? 'Perbaiki & Ajukan Ulang Toko' : 'Formulir Pendaftaran Toko Baru' }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
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
                                    class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-sm" 
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
                                    class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-sm" 
                                    placeholder="Alamat asal pengiriman pesanan toko..."
                                >{{ old('address', $shop?->address) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                                <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">
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
