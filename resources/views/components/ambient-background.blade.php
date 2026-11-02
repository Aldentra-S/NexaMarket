@props([
    'variant' => 'buyer',
])

@php
    // Validate variant fallback
    $validVariants = ['marketplace', 'buyer', 'seller', 'admin', 'catalog', 'product', 'auth'];
    $activeVariant = in_array($variant, $validVariants, true) ? $variant : 'buyer';
@endphp

<div class="fixed inset-0 pointer-events-none overflow-hidden z-0 select-none" aria-hidden="true">
    {{-- 1. BASE FULL-SURFACE PASTEL GRADIENT CANVAS --}}
    {{-- Visible, continuous, soft pastel gradient coloring the entire page surface --}}
    <div class="absolute inset-0 bg-pastel-canvas opacity-100 transition-colors duration-300"></div>

    {{-- 2. BROAD SOFT COLOR FIELDS (Organic depth behind corner geometry) --}}
    {{-- Left/Upper-Left Soft Blush Pink & Pastel Peach Color Field --}}
    <div class="absolute -top-32 -left-32 w-[60vw] h-[60vw] max-w-[950px] max-h-[950px] min-w-[420px] min-h-[420px] rounded-full bg-gradient-to-br from-[#FAD8CF]/75 via-[#FCEBE6]/45 to-transparent dark:from-[#2B1418]/35 dark:via-transparent dark:to-transparent blur-3xl opacity-80 transition-opacity"></div>

    {{-- Right/Upper-Right Soft Pale Mint & Subtle Cyan Color Field --}}
    <div class="absolute -top-32 -right-32 w-[60vw] h-[60vw] max-w-[950px] max-h-[950px] min-w-[420px] min-h-[420px] rounded-full bg-gradient-to-bl from-[#D6F2EA]/80 via-[#E7F7F3]/45 to-transparent dark:from-[#0B2625]/35 dark:via-transparent dark:to-transparent blur-3xl opacity-80 transition-opacity"></div>

    {{-- Mid-Left & Mid-Right Gentle Side Transitions (Prevents bottom-page color dropoff) --}}
    <div class="hidden lg:block absolute top-1/3 -left-44 w-[480px] h-[600px] rounded-full bg-gradient-to-r from-[#FCE8E5]/45 via-[#FFF0E9]/20 to-transparent dark:from-[#200E12]/25 dark:to-transparent blur-3xl opacity-60"></div>
    <div class="hidden lg:block absolute top-1/3 -right-44 w-[480px] h-[600px] rounded-full bg-gradient-to-l from-[#DEF5EF]/45 via-[#EDF8F5]/20 to-transparent dark:from-[#061B1D]/25 dark:to-transparent blur-3xl opacity-60"></div>

    {{-- 3. LARGE ORGANIC CURVED SHAPES (Matching reference screenshot) --}}
    @if ($activeVariant === 'marketplace' || $activeVariant === 'buyer')
        {{-- Top-Left Concentric Peach/Blush Arcs --}}
        <div class="absolute -top-[16vw] -left-[16vw] w-[62vw] h-[62vw] max-w-[920px] max-h-[920px] min-w-[460px] min-h-[460px] rounded-full border border-[#F2B3A7]/30 dark:border-coral-500/10 pointer-events-none"></div>
        <div class="absolute -top-[9vw] -left-[9vw] w-[44vw] h-[44vw] max-w-[660px] max-h-[660px] min-w-[340px] min-h-[340px] rounded-full border border-[#F5C2B7]/35 dark:border-coral-500/10 pointer-events-none"></div>
        <div class="absolute -top-[2vw] -left-[2vw] w-[26vw] h-[26vw] max-w-[420px] max-h-[420px] min-w-[200px] min-h-[200px] rounded-full border border-[#F7D3CB]/45 bg-gradient-to-br from-[#FCE8E5]/35 to-transparent dark:from-coral-950/15 dark:border-coral-500/10 pointer-events-none"></div>

        {{-- Top-Right Concentric Pale Mint/Cyan Arcs --}}
        <div class="absolute -top-[16vw] -right-[16vw] w-[62vw] h-[62vw] max-w-[920px] max-h-[920px] min-w-[460px] min-h-[460px] rounded-full border border-[#8FE0CE]/30 dark:border-teal-500/10 pointer-events-none"></div>
        <div class="absolute -top-[9vw] -right-[9vw] w-[44vw] h-[44vw] max-w-[660px] max-h-[660px] min-w-[340px] min-h-[340px] rounded-full border border-[#A7E8DA]/35 dark:border-teal-500/10 pointer-events-none"></div>
        <div class="absolute -top-[2vw] -right-[2vw] w-[26vw] h-[26vw] max-w-[420px] max-h-[420px] min-w-[200px] min-h-[200px] rounded-full border border-[#C0EFE5]/45 bg-gradient-to-bl from-[#E6F5F0]/40 to-transparent dark:from-teal-950/15 dark:border-teal-500/10 pointer-events-none"></div>

        {{-- Subtle Mid-Canvas Warmth --}}
        <div class="hidden xl:block absolute bottom-12 -left-36 w-[450px] h-[450px] rounded-full bg-gradient-to-tr from-[#FFF0E9]/35 via-transparent to-transparent dark:from-coral-950/10 dark:to-transparent blur-3xl opacity-50"></div>

    @elseif ($activeVariant === 'seller')
        {{-- Seller Center: Professional SaaS, calm peach accent, crisp neutral workspace --}}
        <div class="absolute -top-[14vw] -left-[14vw] w-[58vw] h-[58vw] max-w-[860px] max-h-[860px] min-w-[440px] min-h-[440px] rounded-full border border-[#F2B3A7]/25 dark:border-coral-500/10 pointer-events-none"></div>
        <div class="absolute -top-[7vw] -left-[7vw] w-[40vw] h-[40vw] max-w-[600px] max-h-[600px] min-w-[300px] min-h-[300px] rounded-full border border-[#F5C2B7]/30 dark:border-coral-500/10 pointer-events-none"></div>
        
        <div class="absolute -top-[14vw] -right-[14vw] w-[58vw] h-[58vw] max-w-[860px] max-h-[860px] min-w-[440px] min-h-[440px] rounded-full border border-[#8FE0CE]/25 dark:border-teal-500/10 pointer-events-none"></div>
        <div class="absolute -top-[7vw] -right-[7vw] w-[40vw] h-[40vw] max-w-[600px] max-h-[600px] min-w-[300px] min-h-[300px] rounded-full border border-[#A7E8DA]/30 dark:border-teal-500/10 pointer-events-none"></div>

    @elseif ($activeVariant === 'admin')
        {{-- Admin Center: Crisp, information-focused SaaS dashboard --}}
        <div class="absolute -top-[14vw] -right-[14vw] w-[58vw] h-[58vw] max-w-[860px] max-h-[860px] min-w-[440px] min-h-[440px] rounded-full border border-sky-300/25 dark:border-sky-500/10 pointer-events-none"></div>
        <div class="absolute -top-[7vw] -right-[7vw] w-[40vw] h-[40vw] max-w-[600px] max-h-[600px] min-w-[300px] min-h-[300px] rounded-full border border-sky-200/30 dark:border-sky-500/10 pointer-events-none"></div>
        <div class="absolute -top-[14vw] -left-[14vw] w-[58vw] h-[58vw] max-w-[860px] max-h-[860px] min-w-[440px] min-h-[440px] rounded-full border border-[#F2B3A7]/20 dark:border-coral-500/10 pointer-events-none"></div>

    @elseif ($activeVariant === 'catalog' || $activeVariant === 'product')
        {{-- Catalog & Product: Clean backdrop allowing product imagery to remain primary focus --}}
        <div class="absolute -top-[16vw] -left-[16vw] w-[56vw] h-[56vw] max-w-[800px] max-h-[800px] min-w-[400px] min-h-[400px] rounded-full border border-[#F2B3A7]/20 dark:border-coral-500/10 pointer-events-none"></div>
        <div class="absolute -top-[16vw] -right-[16vw] w-[56vw] h-[56vw] max-w-[800px] max-h-[800px] min-w-[400px] min-h-[400px] rounded-full border border-[#8FE0CE]/20 dark:border-teal-500/10 pointer-events-none"></div>

    @elseif ($activeVariant === 'auth')
        {{-- Authentication: Concentric framing rings centered around login / register forms --}}
        <div class="absolute -top-[16vw] -left-[16vw] w-[58vw] h-[58vw] max-w-[850px] max-h-[850px] min-w-[420px] min-h-[420px] rounded-full border border-[#F2B3A7]/25 dark:border-coral-500/10 pointer-events-none"></div>
        <div class="absolute -top-[16vw] -right-[16vw] w-[58vw] h-[58vw] max-w-[850px] max-h-[850px] min-w-[420px] min-h-[420px] rounded-full border border-[#8FE0CE]/25 dark:border-teal-500/10 pointer-events-none"></div>

        {{-- Center Subtle Radial Aura & Framing Rings for the Form Card --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[380px] h-[380px] sm:w-[540px] sm:h-[540px] rounded-full bg-gradient-to-tr from-[#FFF0E9]/50 via-[#E6F5F0]/30 to-transparent dark:from-slate-900/40 dark:to-transparent blur-3xl opacity-70"></div>
        <div class="hidden sm:block absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[480px] h-[480px] rounded-full border border-[#F2B3A7]/25 dark:border-coral-500/10 pointer-events-none"></div>
        <div class="hidden lg:block absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[640px] h-[640px] rounded-full border border-dashed border-[#8FE0CE]/25 dark:border-teal-500/10 pointer-events-none"></div>
    @endif
</div>
