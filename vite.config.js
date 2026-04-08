import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/base.css',
                'resources/css/landing.css',
                'resources/css/register.css',
                'resources/css/provision.css',
                'resources/css/admin.css',
                'resources/js/app.js',
                'resources/js/state.js',
                'resources/js/router.js',
                'resources/js/register.js',
                'resources/js/provision.js',
            ],
            refresh: true,
        }),
    ],
});
