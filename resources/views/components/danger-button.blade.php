<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white border border-transparent rounded-xl font-semibold text-xs sm:text-sm tracking-wide focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2 transition duration-150 shadow-soft disabled:opacity-50']) }}>
    {{ $slot }}
</button>
