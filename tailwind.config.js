import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
                brand: {
                    50: '#FFF8F5',
                    100: '#FFE6D9',
                    200: '#FFCEB3',
                    300: '#FFAA80',
                    400: '#FF8A3D',
                    500: '#FF6800',
                    600: '#E85B00',
                    700: '#C74A00',
                    800: '#A33C00',
                    900: '#7A2C00',
                    950: '#4A1800',
                },
            },
            boxShadow: {
                soft: '0 2px 10px -2px rgba(15, 23, 42, 0.04), 0 1px 3px -1px rgba(15, 23, 42, 0.03)',
                elevated: '0 12px 28px -6px rgba(15, 23, 42, 0.07), 0 4px 10px -2px rgba(15, 23, 42, 0.03)',
            },
        },
    },

    plugins: [forms],
};
