<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-700 transition">Seller Center</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800 font-medium">Pengaturan Toko</span>
                </div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Pengaturan & Profil Toko') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui identitas toko, deskripsi etalase, kontak resmi, dan alamat pengiriman barang</p>
            </div>
            <a href="{{ route('seller.dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-700 transition">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('status') || session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium">
                    {{ session('status') ?? session('success') }}
                </div>
            @endif

            <!-- Verification Status Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Status Toko: Terverifikasi (Approved)
                    </span>
                    <h3 class="text-lg font-bold text-slate-900 mt-1">{{ $shop->name }}</h3>
                    <p class="text-xs text-slate-500">Domain URL Toko: <span class="font-semibold text-slate-700">/shop/{{ $shop->slug }}</span></p>
                </div>
                <div class="text-left sm:text-right text-xs text-slate-500">
                    <div>Disetujui sejak: <strong class="text-slate-800">{{ $shop->approved_at?->format('d M Y') ?? 'Resmi' }}</strong></div>
                    <div class="mt-0.5">Pemilik: <strong class="text-slate-800">{{ $user->name }}</strong></div>
                </div>
            </div>

            <!-- Shop Settings Form Card -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <form method="POST" action="{{ route('seller.shop.settings.update') }}" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Shop Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Toko Resmi')" />
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('name', $shop->name)" 
                            required 
                            autofocus 
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Nama toko akan tampil di etalase produk, katalog publik, dan halaman invoice pembeli.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Shop Description -->
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Toko')" />
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4" 
                            class="mt-1 block w-full border-slate-300 focus:border-emerald-700 focus:ring-emerald-700 rounded-xl text-xs sm:text-sm text-slate-900 leading-relaxed" 
                            required
                        >{{ old('description', $shop->description) }}</textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Jelaskan jenis produk yang Anda tawarkan, komitmen pelayanan, dan keunggulan toko Anda.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Contact & Address Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Phone Number -->
                        <div>
                            <x-input-label for="phone" :value="__('Nomor Telepon / WhatsApp')" />
                            <x-text-input 
                                id="phone" 
                                name="phone" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('phone', $shop->phone)" 
                                placeholder="Contoh: 081234567890" 
                            />
                            <p class="text-[11px] text-slate-400 mt-1">Kontak toko untuk koordinasi operasional dan konfirmasi pengiriman.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('Alamat Toko / Gudang Pengiriman')" />
                            <x-text-input 
                                id="address" 
                                name="address" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('address', $shop->address)" 
                                placeholder="Kota asal pengiriman barang..." 
                            />
                            <p class="text-[11px] text-slate-400 mt-1">Alamat asal kurir untuk perhitungan pengiriman barang.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('seller.dashboard') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Simpan Perubahan Toko') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
