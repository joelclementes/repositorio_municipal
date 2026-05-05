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
                vino1: {
                    50: '#f9f2f5',
                    100: '#f3e6ec',
                    200: '#e6ccd9',
                    300: '#d9b3c6',
                    400: '#cc99b3',
                    500: '#bf80a0',
                    600: '#a65c80',
                    700: '#8c3360',
                    800: '#731a40',
                    900: '#6C143A',
                },
                vino2: {
                    50:  '#f6efe9',
                    100: '#eedfd3',
                    200: '#ddbea7',
                    300: '#cc9e7b',
                    400: '#bb7d4f',
                    500: '#aa5d23',
                    600: '#8f4e1d',
                    700: '#733e16',
                    800: '#5a2c0c',
                    900: '#461901',
                },
                vino3: {
                    50:  '#f6f7f9',
                    100: '#eceef2',
                    200: '#d7dbe3',
                    300: '#b9c0cd',
                    400: '#949fb2',
                    500: '#727f97',
                    600: '#59657d',
                    700: '#434e63',
                    800: '#2e374a',
                    900: '#1f2937',
                },
                vino: {
                    50:  '#f7f6f8',
                    100: '#efedf1',
                    200: '#dfdce3',
                    300: '#c7c2cd',
                    400: '#a8a2b2',
                    500: '#8b8497',
                    600: '#716a7d',
                    700: '#585163',
                    800: '#3f394a',
                    900: '#26212f',
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
