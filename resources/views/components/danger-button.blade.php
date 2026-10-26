<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-rose-700 border border-transparent rounded-xl font-medium text-xs text-white tracking-wide hover:bg-rose-800 active:bg-rose-900 focus:outline-none focus:ring-2 focus:ring-rose-700 focus:ring-offset-2 transition duration-150 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
