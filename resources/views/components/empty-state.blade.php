@props([
    'title',
    'description' => null,
    'actionText' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 rounded-3xl border border-[#E8EAF0] dark:border-slate-800 p-8 sm:p-12 text-center max-w-lg mx-auto shadow-card']) }}>
    <div class="w-14 h-14 bg-[#FFF0E9] dark:bg-coral-950/40 text-coral-600 dark:text-coral-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-coral-200/80 dark:border-coral-800/60 shadow-2xs">
        @if (isset($icon) && $icon->isNotEmpty())
            {{ $icon }}
        @else
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        @endif
    </div>

    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1 tracking-tight">
        {{ $title }}
    </h3>

    @if ($description)
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-6 leading-relaxed">
            {{ $description }}
        </p>
    @endif

    @if ($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-coral-500 hover:bg-coral-600 active:bg-coral-700 text-white font-semibold text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-500 focus-visible:ring-offset-2 shadow-soft">
            <span>{{ $actionText }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    @endif

    {{ $slot }}
</div>
