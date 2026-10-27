<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'NexaMarket') }} | Workspace</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=4">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}?v=4">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=4">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Livewire Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
@if (request()->routeIs('admin.*'))
<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-full">
    <div x-data="{ sidebarOpen: false, logoutModalOpen: false }" @keydown.escape.window="sidebarOpen = false; logoutModalOpen = false" class="min-h-screen bg-slate-50 flex">
        <!-- Sidebar Backdrop for Mobile/Tablet -->
        <div 
            x-show="sidebarOpen" 
            x-cloak
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 lg:hidden" {{-- taste-skill-ignore --}}
        ></div>

        <!-- Admin Sidebar Component -->
        <x-admin-sidebar />

        <!-- Main Admin Workspace Area -->
        <div class="lg:pl-64 flex flex-col flex-1 min-w-0 min-h-screen">
            <!-- Topbar Component -->
            <x-admin-topbar />

            <!-- Optional Subheader for admin resource pages -->
            @if (isset($header))
                <div class="bg-white border-b border-slate-200 py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            @endif

            <!-- Flash Status Messages -->
            @if (session('status') || session('success') || session('error'))
                <div class="px-4 sm:px-6 lg:px-8 pt-4">
                    @if (session('status') || session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">{{ session('status') ?? session('success') }}</span>
                            </div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Main Page Content -->
            <main class="flex-1">
                {{ $slot ?? '' }}
                @yield('content')
            </main>

        </div>

        <!-- Logout Confirmation Modal -->
        <div 
            x-show="logoutModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto" 
            aria-labelledby="logout-modal-title" 
            role="dialog" 
            aria-modal="true"
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Modal Backdrop -->
                <div 
                    x-show="logoutModalOpen" 
                    x-transition:enter="transition-opacity ease-out duration-200" 
                    x-transition:enter-start="opacity-0" 
                    x-transition:enter-end="opacity-100" 
                    x-transition:leave="transition-opacity ease-out duration-150" 
                    x-transition:leave-start="opacity-100" 
                    x-transition:leave-end="opacity-0" 
                    @click="logoutModalOpen = false" 
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" {{-- taste-skill-ignore --}}
                ></div>

                <!-- Modal Content Card -->
                <div 
                    x-show="logoutModalOpen" 
                    x-transition:enter="transition ease-out duration-200" 
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="transition ease-out duration-150" 
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-left align-middle bg-white rounded-2xl shadow-xl transition-all border border-slate-200 z-10" {{-- taste-skill-ignore --}}
                >
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900" id="logout-modal-title">Konfirmasi Keluar</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Sesi administrator Anda akan diakhiri.</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 mt-3.5 leading-relaxed">
                        Apakah Anda yakin ingin keluar dari Admin Center NexaMarket? Anda harus memasukkan kredensial login kembali untuk melanjutkan.
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            @click="logoutModalOpen = false" 
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button 
                                type="submit" 
                                class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer shadow-xs"
                            >
                                Ya, Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @livewireScripts
</body>
@elseif (request()->routeIs('seller.*'))
<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-full">
    <div x-data="{ sidebarOpen: false, logoutModalOpen: false }" @keydown.escape.window="sidebarOpen = false; logoutModalOpen = false" class="min-h-screen bg-slate-50 flex">
        <!-- Sidebar Backdrop for Mobile/Tablet -->
        <div 
            x-show="sidebarOpen" 
            x-cloak
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 lg:hidden"
        ></div>

        <!-- Seller Sidebar Component -->
        <x-seller-sidebar />

        <!-- Main Seller Workspace Area -->
        <div class="lg:pl-64 flex flex-col flex-1 min-w-0 min-h-screen">
            <!-- Topbar Component -->
            <x-seller-topbar />

            <!-- Optional Subheader for seller pages -->
            @if (isset($header))
                <div class="bg-white border-b border-slate-200 py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            @endif

            <!-- Flash Status Messages -->
            @if (session('status') || session('success') || session('error'))
                <div class="px-4 sm:px-6 lg:px-8 pt-4">
                    @if (session('status') || session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">{{ session('status') ?? session('success') }}</span>
                            </div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Main Page Content -->
            <main class="flex-1">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>

        <!-- Logout Confirmation Modal -->
        <div 
            x-show="logoutModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto" 
            aria-labelledby="seller-logout-modal-title" 
            role="dialog" 
            aria-modal="true"
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div 
                    x-show="logoutModalOpen" 
                    x-transition:enter="transition-opacity ease-out duration-200" 
                    x-transition:enter-start="opacity-0" 
                    x-transition:enter-end="opacity-100" 
                    x-transition:leave="transition-opacity ease-out duration-150" 
                    x-transition:leave-start="opacity-100" 
                    x-transition:leave-end="opacity-0" 
                    @click="logoutModalOpen = false" 
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                ></div>

                <div 
                    x-show="logoutModalOpen" 
                    x-transition:enter="transition ease-out duration-200" 
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="transition ease-out duration-150" 
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-left align-middle bg-white rounded-2xl shadow-xl transition-all border border-slate-200 z-10"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900" id="seller-logout-modal-title">Konfirmasi Keluar</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Sesi akun NexaMarket Anda akan diakhiri.</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 mt-3.5 leading-relaxed">
                        Apakah Anda yakin ingin keluar dari akun Anda? Anda harus masuk kembali untuk mengelola toko atau berbelanja.
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            @click="logoutModalOpen = false" 
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button 
                                type="submit" 
                                class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer shadow-xs"
                            >
                                Ya, Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
@else
<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-full flex flex-col">
    <livewire:layout.navigation />

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Flash Status Messages -->
    @if (session('status') || session('success') || session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if (session('status') || session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('status') ?? session('success') }}</span>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Page Content -->
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Simple Workspace Footer -->
    <footer class="bg-white border-t border-slate-200 text-slate-500 text-xs py-6 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px]">
            <p>&copy; {{ date('Y') }} NexaMarket Multi-Vendor Platform. Dilindungi hak cipta.</p>
            <div class="flex items-center gap-4 text-slate-400">
                <a href="{{ route('products.index') }}" class="hover:text-orange-600 transition">Katalog Publik</a>
                <span>&bull;</span>
                <a href="{{ route('dashboard') }}" class="hover:text-orange-600 transition">Dashboard Akun</a>
                <span>&bull;</span>
                <span class="text-slate-500">v1.0-release</span>
            </div>
        </div>
    </footer>
    @livewireScripts
</body>
@endif
</html>
