<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-purple-600 transition">Admin Portal</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('admin.categories.index') }}" class="hover:text-purple-600 transition">Kategori</a>
                    <span>&rsaquo;</span>
                    <span class="text-gray-800 font-medium">Edit Kategori</span>
                </div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Kategori: ') }} {{ $category->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui nama, slug, deskripsi, atau status keaktifan kategori</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-gray-600 hover:text-purple-600 transition">
                &larr; Kembali ke Daftar Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Category Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Kategori')" />
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('name', $category->name)" 
                            required 
                            autofocus 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Slug -->
                    <div>
                        <x-input-label for="slug" :value="__('Slug URL')" />
                        <x-text-input 
                            id="slug" 
                            name="slug" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('slug', $category->slug)" 
                            required 
                        />
                        <p class="text-[11px] text-gray-400 mt-1">Digunakan untuk URL pengelompokan produk (/products?category={{ $category->slug }}).</p>
                        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Kategori (Opsional)')" />
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4" 
                            class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm text-xs leading-relaxed" 
                        >{{ old('description', $category->description) }}</textarea>
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
                                class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500" 
                                {{ old('is_active', $category->is_active) ? 'checked' : '' }} 
                            />
                            <span class="ms-2 text-xs font-medium text-gray-700">Kategori Aktif</span>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                    </div>

                    <!-- Associated Products Note -->
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-xs text-gray-500">
                        Kategori ini saat ini digunakan oleh <strong class="text-gray-900">{{ $category->products_count }} produk</strong>.
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 transition">
                            Batal
                        </a>
                        <x-primary-button class="bg-purple-600 hover:bg-purple-700">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
