<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-extrabold text-2xl sm:text-3xl text-slate-900 dark:text-white tracking-tight leading-tight">
                    {{ __('Ubah Alamat Pengiriman') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Perbarui informasi penerima atau rincian lokasi pengiriman paket Anda.
                </p>
            </div>
            <a href="{{ route('buyer.addresses.index') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-orange-600 dark:hover:text-orange-400 transition">
                &larr; Kembali ke Daftar Alamat
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-[#E8EAF0] dark:border-slate-800 p-6 sm:p-8 shadow-card">
                <form method="POST" action="{{ route('buyer.addresses.update', $address) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Label & Recipient Name Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="label" :value="__('Label Alamat')" />
                            <x-text-input 
                                id="label" 
                                name="label" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('label', $address->label)" 
                                placeholder="Contoh: Rumah, Kantor, Kosan" 
                                required 
                                autofocus 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('label')" />
                        </div>

                        <div>
                            <x-input-label for="recipient_name" :value="__('Nama Penerima')" />
                            <x-text-input 
                                id="recipient_name" 
                                name="recipient_name" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('recipient_name', $address->recipient_name)" 
                                placeholder="Nama lengkap penerima paket" 
                                required 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('recipient_name')" />
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <x-input-label for="phone" :value="__('Nomor Telepon / WhatsApp Penerima')" />
                        <x-text-input 
                            id="phone" 
                            name="phone" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('phone', $address->phone)" 
                            placeholder="Contoh: 081234567890" 
                            required 
                        />
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Kurir akan menghubungi nomor ini saat mengantarkan paket pesanan Anda.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <!-- Regional Grid: Province & City -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="province" :value="__('Provinsi')" />
                            <x-text-input 
                                id="province" 
                                name="province" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('province', $address->province)" 
                                placeholder="Contoh: Jawa Barat, DKI Jakarta" 
                                required 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('province')" />
                        </div>

                        <div>
                            <x-input-label for="city" :value="__('Kota / Kabupaten')" />
                            <x-text-input 
                                id="city" 
                                name="city" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('city', $address->city)" 
                                placeholder="Contoh: Kota Bandung, Jakarta Selatan" 
                                required 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>
                    </div>

                    <!-- Regional Grid: District, Village & Postal Code -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <x-input-label for="district" :value="__('Kecamatan')" />
                            <x-text-input 
                                id="district" 
                                name="district" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('district', $address->district)" 
                                placeholder="Contoh: Coblong, Sukajadi" 
                                required 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('district')" />
                        </div>

                        <div>
                            <x-input-label for="village" :value="__('Kelurahan / Desa (Opsional)')" />
                            <x-text-input 
                                id="village" 
                                name="village" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('village', $address->village)" 
                                placeholder="Contoh: Dago, Pasteur" 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('village')" />
                        </div>

                        <div>
                            <x-input-label for="postal_code" :value="__('Kode Pos')" />
                            <x-text-input 
                                id="postal_code" 
                                name="postal_code" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('postal_code', $address->postal_code)" 
                                placeholder="Contoh: 40135" 
                                required 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                        </div>
                    </div>

                    <!-- Detailed Street Address -->
                    <div>
                        <x-input-label for="address_line" :value="__('Detail Alamat Lengkap')" />
                        <textarea 
                            id="address_line" 
                            name="address_line" 
                            rows="3" 
                            class="mt-1 block w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 leading-relaxed" 
                            required 
                            placeholder="Nama jalan, nomor rumah, RT/RW, blok/lantai, patokan terdekat..."
                        >{{ old('address_line', $address->address_line) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address_line')" />
                    </div>

                    <!-- Delivery Notes -->
                    <div>
                        <x-input-label for="delivery_notes" :value="__('Catatan Pengiriman untuk Kurir (Opsional)')" />
                        <x-text-input 
                            id="delivery_notes" 
                            name="delivery_notes" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('delivery_notes', $address->delivery_notes)" 
                            placeholder="Contoh: Pagar abu-abu, tolong titip security bila sepi..." 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('delivery_notes')" />
                    </div>

                    <!-- Default Address Checkbox -->
                    <div class="flex items-center gap-3 pt-2">
                        <input 
                            id="is_default" 
                            name="is_default" 
                            type="checkbox" 
                            value="1" 
                            {{ old('is_default', $address->is_default) ? 'checked' : '' }} 
                            class="rounded border-slate-300 dark:border-slate-700 text-orange-600 focus:ring-orange-500 dark:bg-slate-800" 
                        />
                        <label for="is_default" class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium cursor-pointer">
                            Jadikan alamat ini sebagai alamat pengiriman utama saya
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('buyer.addresses.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

