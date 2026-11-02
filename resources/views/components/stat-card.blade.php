@props([
    'title',
    'value',
    'subtitle' => null,
    'variant' => 'coral',
    'trend' => null,
    'link' => null,
])

@php
    $variants = [
        'coral' => [
            'iconBg' => 'bg-[#FFF0E9] text-coral-600 border-coral-200 dark:bg-coral-950/40 dark:text-coral-400 dark:border-coral-800/60',
            'border' => 'hover:border-coral-400 dark:hover:border-coral-600',
        ],
        'orange' => [
            'iconBg' => 'bg-[#FFF0E9] text-coral-600 border-coral-200 dark:bg-coral-950/40 dark:text-coral-400 dark:border-coral-800/60',
            'border' => 'hover:border-coral-400 dark:hover:border-coral-600',
        ],
        'brand' => [
            'iconBg' => 'bg-[#FFF0E9] text-coral-600 border-coral-200 dark:bg-coral-950/40 dark:text-coral-400 dark:border-coral-800/60',
            'border' => 'hover:border-coral-400 dark:hover:border-coral-600',
        ],
        'mint' => [
            'iconBg' => 'bg-[#E6F5F0] text-teal-700 border-teal-200 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800/60',
            'border' => 'hover:border-teal-400 dark:hover:border-teal-600',
        ],
        'emerald' => [
            'iconBg' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
            'border' => 'hover:border-emerald-400 dark:hover:border-emerald-600',
        ],
        'blue' => [
            'iconBg' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800/60',
            'border' => 'hover:border-sky-400 dark:hover:border-sky-600',
        ],
        'amber' => [
            'iconBg' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
            'border' => 'hover:border-amber-400 dark:hover:border-amber-600',
        ],
        'neutral' => [
            'iconBg' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            'border' => 'hover:border-slate-400 dark:hover:border-slate-600',
        ],
    ];

    $styles = $variants[$variant] ?? $variants['coral'];
    $tag = $link ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($link) href="{{ $link }}" @endif
    {{ $attributes->merge(['class' => "bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl border border-[#E8EAF0] dark:border-slate-800 shadow-card transition-all duration-150 {$styles['border']} hover:shadow-card-hover flex items-start justify-between gap-4 block"]) }}
>
    <div class="space-y-1 flex-1 min-w-0">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">
            {{ $title }}
        </p>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ $value }}
            </span>
            @if ($trend)
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                    {{ $trend }}
                </span>
            @endif
        </div>
        @if ($subtitle)
            <p class="text-[11px] text-slate-400 dark:text-slate-500 truncate">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if (isset($icon) && $icon->isNotEmpty())
        <div class="w-12 h-12 rounded-xl {{ $styles['iconBg'] }} border flex items-center justify-center shrink-0 shadow-2xs">
            {{ $icon }}
        </div>
    @endif
</{{ $tag }}>
