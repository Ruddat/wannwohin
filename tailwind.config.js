import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',

        // Frontend & Backend
        './resources/frontend/**/*.blade.php',
        './resources/backend/**/*.blade.php',
        './resources/frontend/**/*.js',
        './resources/backend/**/*.js',

        // Falls Vue/TS genutzt wird
        './resources/frontend/**/*.vue',
        './resources/backend/**/*.vue',
        './resources/frontend/**/*.ts',
        './resources/backend/**/*.ts',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
}
