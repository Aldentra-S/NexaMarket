@props([
    'status' => null,
    'variant' => null,
    'size' => 'md',
    'dot' => false,
    'label' => null,
])

@php
    $statusValue = is_object($status) && isset($status->value) ? $status->value : (string) $status;

    // Infer variant from status string if variant is not explicitly given
    if (! $variant && $statusValue) {
        $variant = match (strtolower($statusValue)) {
            'paid', 'success', 'approved', 'published', 'completed' => 'success',
            'pending', 'review' => 'warning',
            'failed', 'cancelled', 'canceled', 'rejected' => 'danger',
            'processing', 'buyer' => 'blue',
            'shipped', 'delivered' => 'indigo',
            'admin' => 'purple',
            'expired', 'suspended', 'draft', 'archived' => 'neutral',
            default => 'neutral',
        };
    }

    $variant = $variant ?: 'neutral';

    // High contrast color pairings verified with WCAG AA standard
    $variants = [
        'success' => [
            'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
            'dot' => 'bg-emerald-500',
        ],
        'warning' => [
            'bg' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
            'dot' => 'bg-amber-500',
        ],
        'danger' => [
            'bg' => 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60',
            'dot' => 'bg-rose-500',
        ],
        'blue' => [
            'bg' => 'bg-sky-50 text-sky-800 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800/60',
            'dot' => 'bg-sky-600',
        ],
        'indigo' => [
            'bg' => 'bg-sky-50 text-sky-800 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800/60',
            'dot' => 'bg-sky-600',
        ],
        'purple' => [
            'bg' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            'dot' => 'bg-slate-500',
        ],
        'coral' => [
            'bg' => 'bg-[#FFF0E9] text-coral-700 border-coral-200 dark:bg-coral-950/40 dark:text-coral-300 dark:border-coral-800/60',
            'dot' => 'bg-coral-500',
        ],
        'brand' => [
            'bg' => 'bg-[#FFF0E9] text-coral-700 border-coral-200 dark:bg-coral-950/40 dark:text-coral-300 dark:border-coral-800/60',
            'dot' => 'bg-coral-500',
        ],
        'orange' => [
            'bg' => 'bg-[#FFF0E9] text-coral-700 border-coral-200 dark:bg-coral-950/40 dark:text-coral-300 dark:border-coral-800/60',
            'dot' => 'bg-coral-500',
        ],
        'mint' => [
            'bg' => 'bg-[#E6F5F0] text-teal-800 border-teal-200 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800/60',
            'dot' => 'bg-teal-500',
        ],
        'neutral' => [
            'bg' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            'dot' => 'bg-slate-400',
        ],
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-[10px] gap-1',
        'md' => 'px-2.5 py-1 text-xs gap-1.5',
        'lg' => 'px-3 py-1.5 text-sm gap-2',
    ];

    $currentVariant = $variants[$variant] ?? $variants['neutral'];
    $currentSize = $sizes[$size] ?? $sizes['md'];

    $displayLabel = $label ?? ($status && method_exists($status, 'label') ? $status->label() : ($statusValue ?: $slot));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full border {$currentVariant['bg']} {$currentSize} leading-none tracking-tight shadow-2xs"]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $currentVariant['dot'] }} shrink-0"></span>
    @endif
    <span>{{ $displayLabel }}</span>
</span>
