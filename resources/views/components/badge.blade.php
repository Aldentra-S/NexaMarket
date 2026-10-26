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
            'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'dot' => 'bg-emerald-600',
        ],
        'warning' => [
            'bg' => 'bg-amber-50 text-amber-800 border-amber-200',
            'dot' => 'bg-amber-600',
        ],
        'danger' => [
            'bg' => 'bg-rose-50 text-rose-800 border-rose-200',
            'dot' => 'bg-rose-600',
        ],
        'blue' => [
            'bg' => 'bg-sky-50 text-sky-800 border-sky-200',
            'dot' => 'bg-sky-700',
        ],
        'indigo' => [
            'bg' => 'bg-sky-50 text-sky-800 border-sky-200',
            'dot' => 'bg-sky-700',
        ],
        'purple' => [
            'bg' => 'bg-slate-100 text-slate-800 border-slate-300',
            'dot' => 'bg-slate-700',
        ],
        'brand' => [
            'bg' => 'bg-orange-50 text-orange-700 border-orange-200',
            'dot' => 'bg-orange-600',
        ],
        'orange' => [
            'bg' => 'bg-orange-50 text-orange-700 border-orange-200',
            'dot' => 'bg-orange-600',
        ],
        'neutral' => [
            'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
            'dot' => 'bg-slate-500',
        ],
    ];

    $styles = $variants[$variant] ?? $variants['neutral'];

    $sizes = [
        'sm' => 'text-[11px] px-2 py-0.5 gap-1.5',
        'md' => 'text-xs px-2.5 py-1 gap-1.5',
        'lg' => 'text-sm px-3 py-1.5 gap-2',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $dotSizes = [
        'sm' => 'h-1.5 w-1.5',
        'md' => 'h-2 w-2',
        'lg' => 'h-2.5 w-2.5',
    ];
    $dotClass = $dotSizes[$size] ?? $dotSizes['md'];

    // If label not explicitly provided and status has a label method, use it
    if (! $label && is_object($status) && method_exists($status, 'label')) {
        $displayLabel = $status->label();
    } elseif ($label) {
        $displayLabel = $label;
    } else {
        $displayLabel = ucfirst(str_replace('_', ' ', $statusValue ?: ''));
    }
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full border {$styles['bg']} {$sizeClass}"]) }}>
    @if ($dot)
        <span class="rounded-full shrink-0 {{ $dotClass }} {{ $styles['dot'] }}"></span>
    @endif
    <span>{{ $displayLabel ?: $slot }}</span>
</span>

