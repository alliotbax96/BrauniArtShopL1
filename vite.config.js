import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            external: [
                'https://cdn.jsdelivr.net/npm/@cdek-it/widget@3',
                'https://bootstraptema.ru/snippets/audio/2017/jplayer/jquery.jplayer.min.js',
                'https://bootstraptema.ru/snippets/audio/2017/jplayer/jplayer.playlist.min.js',
                'https://use.fontawesome.com/b6bb56a290.js',
                'https://cdn.jsdelivr.net/npm/suggestions-jquery@22.6.0/dist/css/suggestions.min.css',
            ]
        },
        target: 'es2017', // или 'es2017' для лучшей поддержки WeakMap
        minify: false
    },
    server: {
        cors: true,
        sourcemap: false, // Отключаем source map в режиме разработки
    },
    resolve: {
        alias: {
            popper: '/node_modules/@popperjs/core/lib/popper.js',
            '@': '/resources/js',
        }
    },

});
