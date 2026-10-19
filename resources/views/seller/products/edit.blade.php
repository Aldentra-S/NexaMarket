<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-600 transition">Seller Center</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('seller.products.index') }}" class="hover:text-emerald-600 transition">Produk</a>
                    <span>&rsaquo;</span>
                    <span class="text-gray-800 font-medium">Edit Produk</span>
                </div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Produk: ') }} {{ $product->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui rincian harga, stok, deskripsi, atau status visibilitas produk</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="text-xs font-semibold text-gray-600 hover:text-emerald-600 transition">
                &larr; Kembali ke Daftar Produk
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <form method="POST" action="{{ route('seller.products.update', $product) }}" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Product Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Produk Lengkap')" />
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('name', $product->name)" 
                            required 
                            autofocus 
                        />
                        <p class="text-[11px] text-gray-400 mt-1 font-mono">Slug saat ini: {{ $product->slug }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Price & Stock Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Harga Jual Satuan (IDR / Rp)')" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-gray-400 pointer-events-none">
                                    Rp
                                </span>
                                <x-text-input 
                                    id="price" 
                                    name="price" 
                                    type="number" 
                                    step="0.01" 
                                    min="100" 
                                    class="pl-10 block w-full" 
                                    :value="old('price', $product->price)" 
                                    required 
                                />
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <!-- Stock -->
                        <div>
                            <x-input-label for="stock" :value="__('Jumlah Stok')" />
                            <x-text-input 
                                id="stock" 
                                name="stock" 
                                type="number" 
                                min="0" 
                                max="999999" 
                                class="mt-1 block w-full" 
                                :value="old('stock', $product->stock)" 
                                required 
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
                            class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-xs" 
                            required>
                            <option value="published" {{ old('status', $product->status->value) === 'published' ? 'selected' : '' }}>
                                Ditayangkan (Muncul di katalog publik dan dapat dibeli)
                            </option>
                            <option value="draft" {{ old('status', $product->status->value) === 'draft' ? 'selected' : '' }}>
                                Draf (Simpan sementara, belum ditampilkan ke pembeli)
                            </option>
                            <option value="archived" {{ old('status', $product->status->value) === 'archived' ? 'selected' : '' }}>
                                Diarsipkan (Tidak tampil di etalase toko)
                            </option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Rinci Produk')" />
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="6" 
                            class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-xs leading-relaxed" 
                            required 
                        >{{ old('description', $product->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('seller.products.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 transition">
                            Batal
                        </a>
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
