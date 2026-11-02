@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-2.5 rounded-xl text-start text-xs sm:text-sm font-bold text-coral-700 dark:text-coral-300 bg-[#FFF0E9] dark:bg-coral-950/40 focus:outline-none focus:text-coral-900 focus:bg-[#FFF0E9] transition duration-150 ease-in-out'
            : 'block w-full px-4 py-2.5 rounded-xl text-start text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 focus:outline-none focus:text-slate-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
