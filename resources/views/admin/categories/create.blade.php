<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight">
                    {{ __('Tambah Kategori Produk Baru') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Buat kategori baru untuk mengelompokkan produk di katalog marketplace NexaMarket</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-coral-600 dark:hover:text-coral-400 transition">
                &larr; Kembali ke Daftar Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-[#E8EAF0] dark:border-slate-800 shadow-card overflow-hidden">
                <form method="POST" action="{{ route('admin.categories.store') }}" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- Category Name -->
                    <div>
                        <label for="name" class="block font-semibold text-xs sm:text-sm text-slate-700 dark:text-slate-300">Nama Kategori</label>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="mt-1.5 block w-full border-[#E8EAF0] dark:border-slate-700 bg-white dark:bg-slate-900 focus:border-coral-500 focus:ring-2 focus:ring-coral-500/20 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder:text-slate-400 transition outline-none shadow-2xs" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus 
                            placeholder="Contoh: Elektronik & Gadget" 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Slug (Optional, auto-generated if blank) -->
                    <div>
                        <label for="slug" class="block font-semibold text-xs sm:text-sm text-slate-700 dark:text-slate-300">Slug URL (Opsional)</label>
                        <input 
                            id="slug" 
                            name="slug" 
                            type="text" 
                            class="mt-1.5 block w-full border-[#E8EAF0] dark:border-slate-700 bg-white dark:bg-slate-900 focus:border-coral-500 focus:ring-2 focus:ring-coral-500/20 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder:text-slate-400 transition outline-none shadow-2xs" 
                            value="{{ old('slug') }}" 
                            placeholder="elektronik-gadget (kosongkan untuk dibuat otomatis)" 
                        />
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Gunakan huruf kecil, angka, dan tanda hubung (-) jika diisi manual.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block font-semibold text-xs sm:text-sm text-slate-700 dark:text-slate-300">Deskripsi Kategori (Opsional)</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4" 
                            class="mt-1.5 block w-full border-[#E8EAF0] dark:border-slate-700 bg-white dark:bg-slate-900 focus:border-coral-500 focus:ring-2 focus:ring-coral-500/20 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder:text-slate-400 leading-relaxed transition outline-none shadow-2xs" 
                            placeholder="Jelaskan jenis produk yang termasuk dalam kategori ini..."
                        >{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Status Active -->
                    <div class="pt-2">
                        <label for="is_active" class="inline-flex items-center">
                            <input 
                                id="is_active" 
                                type="checkbox" 
                                name="is_active" 
                                value="1" 
                                class="rounded border-[#E8EAF0] dark:border-slate-700 text-coral-600 focus:ring-coral-500" 
                                {{ old('is_active', true) ? 'checked' : '' }} 
                            />
                            <span class="ms-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Aktifkan Kategori (Kategori dapat dipilih oleh penjual dan ditampilkan ke pembeli)</span>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-[#E8EAF0] dark:border-slate-800">
                        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-coral-500 hover:bg-coral-600 active:bg-coral-700 text-white text-xs font-semibold rounded-xl shadow-soft transition focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-500">
                            {{ __('Simpan Kategori') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
