import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    // base : '/',
    plugins: [
        laravel({
            input: [
                //default
                'resources/css/app-default.css',
                'resources/js/app-default.js',

                //bootstrap
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),tailwindcss(),
    ],
        server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
