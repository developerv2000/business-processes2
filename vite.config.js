import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/themes/dark.css',
                'resources/css/themes/light.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    publicDir: 'public',
    base: '/',
    build: {
        assetsDir: '',
        copyPublicDir: false,
    },
});
