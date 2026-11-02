<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-coral-500 hover:bg-coral-600 active:bg-coral-700 text-white font-semibold text-xs sm:text-sm rounded-xl border border-transparent tracking-wide transition duration-150 shadow-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
