import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Warm Coral Palette (NexaMarket Modern SaaS Identity)
                coral: {
                    50: '#FFF5F2',
                    100: '#FFF0E9', // Soft peach
                    200: '#FCE8E5', // Blush
                    300: '#FFCEBE',
                    400: '#FF8068', // Soft coral
                    500: '#F26A4B', // Warm coral (Primary brand accent)
                    600: '#E95735', // Deep coral (Hover / active)
                    700: '#C74020',
                    800: '#A43217',
                    900: '#832B16',
                    950: '#461206',
                },
                // Aliased brand tokens mapping to coral
                brand: {
                    50: '#FFF5F2',
                    100: '#FFF0E9',
                    200: '#FCE8E5',
                    300: '#FFCEBE',
                    400: '#FF8068',
                    500: '#F26A4B',
                    600: '#E95735',
                    700: '#C74020',
                    800: '#A43217',
                    900: '#832B16',
                    950: '#461206',
                },
                // Re-mapped orange tokens to warm coral for cohesive application-wide harmony
                orange: {
                    50: '#FFF5F2',
                    100: '#FFF0E9',
                    200: '#FCE8E5',
                    300: '#FFCEBE',
                    400: '#FF8068',
                    500: '#F26A4B',
                    600: '#E95735',
                    700: '#C74020',
                    800: '#A43217',
                    900: '#832B16',
                    950: '#461206',
                },
                // Supporting pastel & surface tokens
                pastel: {
                    peach: '#FFF0E9',
                    blush: '#FCE8E5',
                    mint: '#E6F5F0',
                    warm: '#FFFEFC',
                    soft: '#F7F8FA',
                    border: '#E8EAF0',
                },
            },
            boxShadow: {
                'soft': '0 2px 10px -2px rgba(23, 32, 51, 0.05), 0 1px 3px -1px rgba(23, 32, 51, 0.03)',
                'card': '0 1px 3px 0 rgba(23, 32, 51, 0.04), 0 1px 2px -1px rgba(23, 32, 51, 0.02)',
                'card-hover': '0 10px 24px -4px rgba(23, 32, 51, 0.07), 0 4px 8px -2px rgba(23, 32, 51, 0.03)',
                'elevated': '0 16px 36px -6px rgba(23, 32, 51, 0.08), 0 6px 14px -3px rgba(23, 32, 51, 0.04)',
            },
        },
    },

    plugins: [forms],
};
