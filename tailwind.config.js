import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './public/js/**/*.js',
    ],

    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['var(--oq-font-body)', ...defaultTheme.fontFamily.sans],
                heading: ['var(--oq-font-heading)', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: Object.fromEntries([50,100,200,300,400,500,600,700,800,900,950].map(shade => [shade, `rgb(var(--oq-burgundy-${shade}) / <alpha-value>)`])),
                gold: Object.fromEntries([50,100,200,300,400,500,600,700,800,900,950].map(shade => [shade, `rgb(var(--oq-gold-${shade}) / <alpha-value>)`])),
                primary: 'rgb(var(--oq-primary-rgb) / <alpha-value>)',
                'primary-dark': 'rgb(var(--oq-burgundy-900) / <alpha-value>)',
                secondary: 'rgb(var(--oq-burgundy-900) / <alpha-value>)',
                accent: 'rgb(var(--oq-gold-400) / <alpha-value>)',
                ivory: '#FDFBF7', surface: '#ffffff', 'surface-muted': '#F4F6F8', line: '#E5E7EB', ink: '#222222', muted: '#64748b',
                bgbase: '#F4EFE6', textdark: '#2D3748',
                brewlybg: '#FDFBF7', brewlygreen: '#34714C', brewlylightgreen: '#E7F2EB',
                brewlypeach: '#FEF0E6', brewlyorange: '#D9652A',
                brewlyborder: '#F0ECE4', brewlytext: '#222222', brewlymuted: '#64748b',
            },
        },
    },

    plugins: [forms],
};
