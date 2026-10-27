<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Edit Produk: ') }} {{ $product->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui rincian harga, stok, deskripsi, atau status visibilitas produk</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-700 transition">
                &larr; Kembali ke Daftar Produk
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden rounded-2xl border border-slate-200">
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
                        <p class="text-[11px] text-slate-400 mt-1">Slug saat ini: {{ $product->slug }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Category Selection -->
                    <div>
                        <x-input-label for="category_id" :value="__('Kategori Produk')" />
                        <select 
                            id="category_id" 
                            name="category_id" 
                            class="mt-1 block w-full border-slate-300 focus:border-emerald-700 focus:ring-emerald-700 rounded-xl text-xs sm:text-sm">
                            <option value="">-- Pilih Kategori Produk (Opsional) --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) old('category_id', $product->category_id) === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
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
                            class="mt-1 block w-full border-slate-300 focus:border-emerald-700 focus:ring-emerald-700 rounded-xl text-xs sm:text-sm" 
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
                            class="mt-1 block w-full border-slate-300 focus:border-emerald-700 focus:ring-emerald-700 rounded-xl text-xs sm:text-sm leading-relaxed" 
                            required 
                        >{{ old('description', $product->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('seller.products.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Product Images Management Card -->
            <div class="bg-white overflow-hidden rounded-2xl border border-slate-200">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Galeri & Foto Produk</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Kelola foto etalase produk (Maksimal 5 foto). Foto utama digunakan sebagai sampul katalog.</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $product->images->count() >= \App\Models\ProductImage::MAX_PER_PRODUCT ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                            {{ $product->images->count() }} / {{ \App\Models\ProductImage::MAX_PER_PRODUCT }} Foto
                        </span>
                    </div>

                    <!-- Existing Images List -->
                    @if ($product->images->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($product->images as $img)
                                <div class="relative bg-slate-50 rounded-xl p-3 border {{ $img->is_primary ? 'border-orange-500 ring-2 ring-orange-100' : 'border-slate-200' }} flex flex-col justify-between">
                                    <div class="aspect-square w-full rounded-lg overflow-hidden bg-white mb-3 relative flex items-center justify-center">
                                        <img src="{{ $img->url }}" alt="Foto {{ $product->name }}" class="object-cover w-full h-full" />
                                        @if ($img->is_primary)
                                            <span class="absolute top-2 left-2 bg-orange-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-xs">
                                                Foto Utama
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-2 pt-2 border-t border-slate-100">
                                        @if (! $img->is_primary)
                                            <form method="POST" action="{{ route('seller.products.images.primary', [$product, $img]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full text-center py-1.5 px-2 bg-white hover:bg-orange-50 text-orange-700 border border-orange-200 rounded-lg text-xs font-semibold transition">
                                                    Jadikan Foto Utama
                                                </button>
                                            </form>
                                        @endif

                                        <div class="flex items-center gap-2">
                                            <!-- Replace Image Form -->
                                            <form method="POST" action="{{ route('seller.products.images.update', [$product, $img]) }}" enctype="multipart/form-data" class="flex-1">
                                                @csrf
                                                @method('PUT')
                                                <label class="block text-center cursor-pointer py-1.5 px-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-[11px] font-semibold transition">
                                                    <span>Ganti</span>
                                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only" onchange="this.form.submit()">
                                                </label>
                                            </form>

                                            <!-- Delete Image Form -->
                                            <form method="POST" action="{{ route('seller.products.images.destroy', [$product, $img]) }}" onsubmit="return confirm('Hapus foto ini?')" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-center py-1.5 px-2 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-[11px] font-semibold transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-500">Produk ini belum memiliki foto. Unggah foto produk di bawah untuk menarik pembeli.</p>
                        </div>
                    @endif

                    <!-- Upload Additional Images (if under limit) -->
                    @if ($product->images->count() < \App\Models\ProductImage::MAX_PER_PRODUCT)
                        <form method="POST" action="{{ route('seller.products.images.store', $product) }}" enctype="multipart/form-data" class="pt-4 border-t border-slate-100">
                            @csrf
                            <x-input-label for="upload_images" :value="__('Unggah Foto Tambahan')" />
                            <div class="mt-2 flex flex-col sm:flex-row gap-3 items-center">
                                <input 
                                    id="upload_images" 
                                    name="images[]" 
                                    type="file" 
                                    multiple 
                                    accept="image/jpeg,image/png,image/webp" 
                                    class="block w-full text-xs text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 border border-slate-200 rounded-xl cursor-pointer" 
                                    required 
                                />
                                <x-primary-button class="shrink-0">
                                    {{ __('Unggah Foto') }}
                                </x-primary-button>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Mendukung JPEG, PNG, dan WebP (Maks. 2MB per berkas). Sisa kuota: {{ \App\Models\ProductImage::MAX_PER_PRODUCT - $product->images->count() }} foto.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('images')" />
                            <x-input-error class="mt-2" :messages="$errors->get('images.*')" />
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
