@props([
    'title',
    'value',
    'subtitle' => null,
    'variant' => 'emerald',
    'trend' => null,
    'link' => null,
])

@php
    $variants = [
        'emerald' => [
            'iconBg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'border' => 'hover:border-emerald-600',
        ],
        'orange' => [
            'iconBg' => 'bg-orange-50 text-orange-700 border-orange-200',
            'border' => 'hover:border-orange-500',
        ],
        'brand' => [
            'iconBg' => 'bg-orange-50 text-orange-700 border-orange-200',
            'border' => 'hover:border-orange-500',
        ],
        'blue' => [
            'iconBg' => 'bg-sky-50 text-sky-800 border-sky-200',
            'border' => 'hover:border-sky-600',
        ],
        'purple' => [
            'iconBg' => 'bg-slate-100 text-slate-800 border-slate-300',
            'border' => 'hover:border-slate-500',
        ],
        'amber' => [
            'iconBg' => 'bg-amber-50 text-amber-800 border-amber-200',
            'border' => 'hover:border-amber-600',
        ],
        'neutral' => [
            'iconBg' => 'bg-slate-100 text-slate-700 border-slate-200',
            'border' => 'hover:border-slate-400',
        ],
    ];

    $styles = $variants[$variant] ?? $variants['emerald'];
    $tag = $link ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($link) href="{{ $link }}" @endif
    {{ $attributes->merge(['class' => "bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 transition duration-150 {$styles['border']} flex items-start justify-between gap-4 block"]) }}
>
    <div class="space-y-1 flex-1 min-w-0">
        <p class="text-xs font-medium text-slate-500 truncate">
            {{ $title }}
        </p>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                {{ $value }}
            </span>
            @if ($trend)
                <span class="text-xs font-semibold text-emerald-800">
                    {{ $trend }}
                </span>
            @endif
        </div>
        @if ($subtitle)
            <p class="text-xs text-slate-500 line-clamp-1">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if (isset($icon) && $icon->isNotEmpty())
        <div class="p-2.5 rounded-xl border shrink-0 {{ $styles['iconBg'] }}">
            {{ $icon }}
        </div>
    @endif
</{{ $tag }}>

