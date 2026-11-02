@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-coral-500 text-xs sm:text-sm font-bold leading-5 text-slate-900 dark:text-white focus:outline-none focus:border-coral-600 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs sm:text-sm font-semibold leading-5 text-slate-600 dark:text-slate-400 hover:text-coral-600 dark:hover:text-coral-400 hover:border-slate-300 dark:hover:border-slate-700 focus:outline-none focus:text-slate-800 focus:border-slate-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
