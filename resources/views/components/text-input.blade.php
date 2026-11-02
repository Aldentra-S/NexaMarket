@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#E8EAF0] dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:border-coral-500 focus:ring-2 focus:ring-coral-500/20 rounded-xl text-xs sm:text-sm transition outline-none shadow-2xs disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:text-slate-500']) }}>
