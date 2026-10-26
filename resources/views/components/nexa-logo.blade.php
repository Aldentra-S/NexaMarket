@props([
    'size' => 'md',
    'iconOnly' => false,
    'subtitle' => false,
    'light' => false,
    'fullImage' => false,
])

@php
    $iconSizes = [
        'sm' => 'h-7 w-7',
        'md' => 'h-9 w-9',
        'lg' => 'h-11 w-11',
        'xl' => 'h-14 w-14',
    ];

    $textSizes = [
        'sm' => 'text-lg',
        'md' => 'text-xl',
        'lg' => 'text-2xl',
        'xl' => 'text-3xl',
    ];

    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
    $textClass = $textSizes[$size] ?? $textSizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none']) }}>
    <!-- NexaMarket Brand Shopping Bag Icon Mark (Image 2 Design System) -->
    <div class="{{ $iconClass }} shrink-0 bg-orange-600 rounded-xl p-1.5 flex items-center justify-center text-white">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 01-8 0" />
        </svg>
    </div>

    @unless ($iconOnly)
        <div class="flex flex-col text-left leading-none">
            <span class="{{ $textClass }} font-black tracking-tight {{ $light ? 'text-white' : 'text-slate-900' }}">
                Nexa<span class="{{ $light ? 'text-orange-400' : 'text-orange-600' }}">Market</span>
            </span>
            @if ($subtitle)
                <span class="hidden sm:inline-block text-[11px] sm:text-xs font-medium {{ $light ? 'text-orange-100' : 'text-slate-500' }} mt-1 tracking-tight">
                    Belanja Mudah, Hidup Lebih Baik
                </span>
            @endif
        </div>
    @endunless
</div>
