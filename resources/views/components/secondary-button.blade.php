<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-slate-900 border border-[#E8EAF0] dark:border-slate-800 rounded-xl font-semibold text-xs sm:text-sm text-slate-700 dark:text-slate-200 hover:bg-[#FFF0E9] hover:text-coral-600 dark:hover:bg-slate-800 dark:hover:text-coral-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-500 focus-visible:ring-offset-2 disabled:opacity-50 transition duration-150 shadow-2xs']) }}>
    {{ $slot }}
</button>
