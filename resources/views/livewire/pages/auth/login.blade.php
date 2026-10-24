<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Google Sign-In with Firebase -->
    <div x-data="firebaseGoogleAuth()" class="mt-6">
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white px-3 text-gray-500 font-medium tracking-wider">Atau masuk dengan</span>
            </div>
        </div>

        <!-- Feedback error alert -->
        <template x-if="errorMessage">
            <div class="mb-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-start gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-text="errorMessage"></span>
            </div>
        </template>

        <button type="button"
                @click="signInWithGoogle"
                :disabled="isLoading"
                class="w-full inline-flex items-center justify-center gap-3 px-4 py-2.5 border border-gray-300 rounded-xl shadow-sm bg-white hover:bg-gray-50 text-xs font-semibold text-gray-700 transition disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
            <template x-if="isLoading">
                <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <template x-if="!isLoading">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
            </template>
            <span x-text="isLoading ? 'Menghubungkan ke Google...' : 'Lanjutkan dengan Google'"></span>
        </button>
    </div>

    <script>
        function firebaseGoogleAuth() {
            return {
                isLoading: false,
                errorMessage: '',
                async signInWithGoogle() {
                    this.errorMessage = '';
                    this.isLoading = true;

                    try {
                        if (!window.NexaFirebaseAuth || typeof window.NexaFirebaseAuth.loginWithGoogle !== 'function') {
                            throw new Error('Modul autentikasi Firebase sedang dimuat atau belum siap. Silakan refresh halaman.');
                        }

                        const idToken = await window.NexaFirebaseAuth.loginWithGoogle();

                        const response = await fetch('{{ route('auth.firebase.google') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ id_token: idToken }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            const errorMsg = data.errors?.token?.[0] || data.message || 'Gagal masuk menggunakan Google.';
                            throw new Error(errorMsg);
                        }

                        window.location.href = data.redirect || '{{ route('dashboard') }}';
                    } catch (err) {
                        if (err.code === 'auth/popup-closed-by-user' || err.code === 'auth/cancelled-popup-request') {
                            this.errorMessage = 'Proses login Google dibatalkan.';
                        } else if (err.code === 'auth/popup-blocked') {
                            this.errorMessage = 'Jendela popup Google diblokir oleh browser. Izinkan popup untuk melanjutkan.';
                        } else {
                            this.errorMessage = err.message || 'Terjadi kesalahan saat masuk dengan Google.';
                        }
                    } finally {
                        this.isLoading = false;
                    }
                }
            };
        }
    </script>
</div>
