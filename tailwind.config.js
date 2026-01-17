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
            colors: {
                brand: {
                    purple: '#8838e0',
                    blue: '#355afe',
                    black: '#000000',
                },
                primary: {
                    50: '#f5f0fe',
                    100: '#ebe0fd',
                    200: '#d6c1fb',
                    300: '#bc96f7',
                    400: '#a060f2',
                    500: '#8838e0',
                    600: '#7528c7',
                    700: '#6220a6',
                    800: '#521d87',
                    900: '#441a6e',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
