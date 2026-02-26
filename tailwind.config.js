import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                vino: {
                    50: '#f9f2f5',
                    100: '#f3e6ec',
                    200: '#e6ccd9', // equivalente a "200"
                    300: '#d9b3c6',
                    400: '#cc99b3',
                    500: '#bf80a0',
                    600: '#a65c80',
                    700: '#8c3360',
                    800: '#731a40',
                    900: '#6C143A', // tu color principal más intenso
                }
            }
        },
    },
    safelist: [
        'bg-green-100',
        'bg-green-200',
        'bg-green-300',
        'bg-vino-100',
        'bg-vino-200',
        'bg-vino-300',
        'bg-slate-100',
        'bg-slate-200',
        'bg-slate-300',
        'bg-amber-100',
    ],

    plugins: [forms, typography],
};
