<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Tambah Produk Baru') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Lengkapi informasi produk untuk mulai menawarkan barang di pasar</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-orange-600 transition">
                &larr; Kembali ke Daftar Produk
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden rounded-2xl border border-slate-200">
                <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- Product Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Produk Lengkap')" />
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('name')" 
                            required 
                            autofocus 
                            placeholder="Contoh: Kemeja Flanel Katun Premium Lengan Panjang" 
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Gunakan nama yang jelas dan deskriptif agar mudah dicari oleh calon pembeli.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Category Selection -->
                    <div>
                        <x-input-label for="category_id" :value="__('Kategori Produk')" />
                        <select 
                            id="category_id" 
                            name="category_id" 
                            class="mt-1 block w-full border-slate-300 focus:border-orange-500 focus:ring-orange-500 rounded-xl text-xs sm:text-sm">
                            <option value="">-- Pilih Kategori Produk (Opsional) --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih kategori yang paling sesuai agar produk mudah ditemukan oleh pembeli.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                    </div>

                    <!-- Price & Stock Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Harga Jual Satuan (IDR / Rp)')" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400 pointer-events-none">
                                    Rp
                                </span>
                                <x-text-input 
                                    id="price" 
                                    name="price" 
                                    type="number" 
                                    step="0.01" 
                                    min="100" 
                                    class="pl-10 block w-full" 
                                    :value="old('price')" 
                                    required 
                                    placeholder="150000" 
                                />
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <!-- Stock -->
                        <div>
                            <x-input-label for="stock" :value="__('Jumlah Stok Awal')" />
                            <x-text-input 
                                id="stock" 
                                name="stock" 
                                type="number" 
                                min="0" 
                                max="999999" 
                                class="mt-1 block w-full" 
                                :value="old('stock', 10)" 
                                required 
                                placeholder="10" 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('stock')" />
                        </div>
                    </div>

                    <!-- Publication Status -->
                    <div>
                        <x-input-label for="status" :value="__('Status Penayangan Produk')" />
                        <select 
                            id="status" 
                            name="status" 
                            class="mt-1 block w-full border-slate-300 focus:border-orange-500 focus:ring-orange-500 rounded-xl text-xs sm:text-sm" 
                            required>
                            <option value="published" {{ old('status') === 'published' || old('status') === null ? 'selected' : '' }}>
                                Ditayangkan (Langsung muncul di katalog publik dan dapat dibeli)
                            </option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>
                                Draf (Simpan sementara, belum ditampilkan ke pembeli)
                            </option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>
                                Diarsipkan (Tidak tampil di etalase toko)
                            </option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>

                    <!-- Product Images -->
                    <div>
                        <x-input-label for="images" :value="__('Foto / Gambar Produk (Maksimal 5 Berkas)')" />
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-orange-500 transition bg-slate-50/50">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-slate-600 justify-center">
                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-semibold text-orange-600 hover:text-orange-700 focus-within:outline-none">
                                        <span>Pilih berkas dari komputer</span>
                                        <input id="images" name="images[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-[11px] text-slate-500">Mendukung JPEG, PNG, dan WebP (Maks. 2MB per gambar). Gambar pertama akan menjadi foto utama.</p>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('images')" />
                        <x-input-error class="mt-2" :messages="$errors->get('images.*')" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Rinci Produk')" />
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="6" 
                            class="mt-1 block w-full border-slate-300 focus:border-orange-500 focus:ring-orange-500 rounded-xl text-xs sm:text-sm leading-relaxed" 
                            required 
                            placeholder="Jelaskan spesifikasi, material, ukuran, keunggulan, dan panduan penggunaan produk..."
                        >{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('seller.products.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Simpan & Publikasikan Produk') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
