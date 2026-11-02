<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">
            {{ __('Hapus Akun Pengguna') }}
        </h2>

        <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            {{ __('Setelah akun Anda dihapus, seluruh data dan riwayat pesanan akan dihapus secara permanen. Pastikan Anda telah mengunduh data penting sebelum melanjutkan.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Hapus Akun') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6 sm:p-8">

            <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="mt-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                {{ __('Tindakan ini tidak dapat dibatalkan. Seluruh data pesanan dan profil akan terhapus permanen. Masukkan kata sandi akun Anda untuk konfirmasi.') }}
            </p>

            <div class="mt-5">
                <x-input-label for="password" value="{{ __('Kata Sandi') }}" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full sm:w-3/4"
                    placeholder="{{ __('Masukkan kata sandi untuk konfirmasi') }}"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('Konfirmasi Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
