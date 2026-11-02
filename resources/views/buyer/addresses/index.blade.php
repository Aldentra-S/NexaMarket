<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-2xl sm:text-3xl text-slate-900 dark:text-white tracking-tight leading-tight">
                    {{ __('Buku Alamat Pengiriman') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Kelola daftar alamat tujuan pengiriman barang pesanan Anda di NexaMarket.
                </p>
            </div>
            <div>
                <a href="{{ route('buyer.addresses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white text-xs font-bold shadow-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Alamat Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm font-medium flex items-center justify-between shadow-2xs">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-800 dark:text-rose-300 text-xs sm:text-sm font-medium flex items-center justify-between shadow-2xs">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Addresses List Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-[#E8EAF0] dark:border-slate-800 p-6 sm:p-8 shadow-card space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Alamat Tersimpan</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Alamat utama akan otomatis dipilih saat Anda melakukan checkout pesanan.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $addresses->count() }} Alamat
                    </span>
                </div>

                @if ($addresses->isNotEmpty())
                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($addresses as $addr)
                            <div class="p-5 sm:p-6 rounded-2xl border {{ $addr->is_default ? 'border-orange-500 bg-orange-50/20 dark:bg-orange-950/10' : 'border-[#E8EAF0] dark:border-slate-800 bg-white dark:bg-slate-900' }} transition flex flex-col md:flex-row md:items-start justify-between gap-5">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $addr->recipient_name }}
                                        </span>
                                        <span class="text-xs text-slate-400">&bull;</span>
                                        <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                                            {{ $addr->phone }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ $addr->label }}
                                        </span>
                                        @if ($addr->is_default)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-orange-100 dark:bg-orange-900/40 text-orange-800 dark:text-orange-300 border border-orange-200 dark:border-orange-800/60">
                                                Alamat Utama
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                                        {{ $addr->formattedAddress() }}
                                    </p>

                                    @if ($addr->delivery_notes)
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800">
                                            Catatan: "{{ $addr->delivery_notes }}"
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-2 shrink-0 pt-2 md:pt-0">
                                    @if (! $addr->is_default)
                                        <form method="POST" action="{{ route('buyer.addresses.default', $addr) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-xl border border-[#E8EAF0] dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:border-orange-500 hover:text-orange-600 transition shadow-2xs">
                                                Jadikan Utama
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('buyer.addresses.edit', $addr) }}" class="px-3 py-1.5 rounded-xl border border-[#E8EAF0] dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-2xs">
                                        Ubah
                                    </a>

                                    <form method="POST" action="{{ route('buyer.addresses.destroy', $addr) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 px-6 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 flex items-center justify-center mx-auto mb-3.5 shadow-soft">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Alamat Pengiriman</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                            Tambahkan alamat rumah atau kantor Anda sekarang agar proses belanja dan checkout pesanan lebih cepat dan mudah.
                        </p>
                        <div class="mt-5">
                            <a href="{{ route('buyer.addresses.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition">
                                + Tambah Alamat Pertama
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

