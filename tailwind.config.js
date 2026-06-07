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
                sans: ['Tajawal', ...defaultTheme.fontFamily.sans],
                display: ['Cairo', 'Tajawal', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    DEFAULT: '#1e3a5f',
                    light: '#2d5986',
                    dark: '#16304d',
                },
                accent: {
                    DEFAULT: '#fbbf24',
                    hover: '#fcd34d',
                    muted: '#fef3c7',
                },
                surface: {
                    DEFAULT: '#f8fafc',
                    card: '#ffffff',
                },
                muted: '#64748b',
            },
        },
    },

    plugins: [forms],
};
