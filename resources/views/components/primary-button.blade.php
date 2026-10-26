<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-orange-600 border border-transparent rounded-xl font-semibold text-xs sm:text-sm text-white tracking-wide hover:bg-orange-700 active:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-150 shadow-xs disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
