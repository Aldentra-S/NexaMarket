@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 transition outline-none']) }}>
