@props([
    'title',
    'description' => null,
    'actionText' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 text-center max-w-lg mx-auto']) }}>
    <div class="w-14 h-14 bg-emerald-50 text-emerald-800 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-200">
        @if (isset($icon) && $icon->isNotEmpty())
            {{ $icon }}
        @else
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        @endif
    </div>

    <h3 class="text-base font-bold text-slate-900 mb-1">
        {{ $title }}
    </h3>

    @if ($description)
        <p class="text-xs sm:text-sm text-slate-600 max-w-sm mx-auto mb-6 leading-relaxed">
            {{ $description }}
        </p>
    @endif

    @if ($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs transition focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">
            <span>{{ $actionText }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    @endif

    {{ $slot }}
</div>

