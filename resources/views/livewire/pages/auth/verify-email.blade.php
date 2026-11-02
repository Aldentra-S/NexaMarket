<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
        {{ __('Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan. Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkannya kembali.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-semibold text-xs sm:text-sm text-emerald-700 dark:text-emerald-400">
            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda berikan saat pendaftaran.') }}
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between gap-3">
        <x-primary-button wire:click="sendVerification">
            {{ __('Kirim Ulang Email Verifikasi') }}
        </x-primary-button>

        <button wire:click="logout" type="button" class="underline text-xs text-slate-500 dark:text-slate-400 hover:text-coral-600 dark:hover:text-coral-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-coral-500 cursor-pointer">
            {{ __('Keluar') }}
        </button>
    </div>
</div>
