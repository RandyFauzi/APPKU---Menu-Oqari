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
                heading: ['var(--oq-font-heading)', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                brand: Object.fromEntries([50,100,200,300,400,500,600,700,800,900,950].map(shade => [shade, `rgb(var(--oq-burgundy-${shade}) / <alpha-value>)`])),
                gold: Object.fromEntries([50,100,200,300,400,500,600,700,800,900,950].map(shade => [shade, `rgb(var(--oq-gold-${shade}) / <alpha-value>)`])),
                primary: 'rgb(var(--oq-primary-rgb) / <alpha-value>)',
                'primary-dark': 'rgb(var(--oq-burgundy-900) / <alpha-value>)',
                secondary: 'rgb(var(--oq-burgundy-900) / <alpha-value>)',
                accent: 'rgb(var(--oq-gold-400) / <alpha-value>)',
                ivory: '#fffaf0', surface: '#fffdf8', 'surface-muted': '#f6eedf', line: '#e4d7bd', ink: '#38242b', muted: '#6c5b60',
                bgbase: '#fffaf0', textdark: '#38242b',
                brewlybg: '#fffaf0', brewlygreen: 'rgb(var(--oq-primary-rgb) / <alpha-value>)', brewlylightgreen: 'rgb(var(--oq-burgundy-100) / <alpha-value>)',
                brewlypeach: 'rgb(var(--oq-gold-100) / <alpha-value>)', brewlyorange: 'rgb(var(--oq-gold-600) / <alpha-value>)',
                brewlyborder: '#e4d7bd', brewlytext: '#38242b', brewlymuted: '#6c5b60',
                white: '#fffdf8',
                gray: {50:'#faf6ee',100:'#f4eee4',200:'#e4d7bd',300:'#cdbdc3',400:'#79666e',500:'#6c5b60',600:'#59444c',700:'#473039',800:'#38242b',900:'#24151b',950:'#170c11'},
            },
        },
    },

    plugins: [forms],
};
