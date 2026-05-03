import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/dashboard/app.css',
                'resources/js/dashboard/app.js',
                'resources/js/Pages/cart.js',
                'resources/js/Pages/checkout.js',
                'resources/js/Pages/contacts.js',
                'resources/js/Pages/home.js',
                'resources/js/Pages/login.js',
                'resources/js/Pages/login.js',
                'resources/js/Pages/product.js',
                'resources/js/Pages/products.js',
                'resources/js/Pages/signup.js',
                'resources/js/dashboard/Pages/chat.js',
                'resources/js/dashboard/Pages/login.js',
                'resources/js/dashboard/Pages/orders.js',
                'resources/js/dashboard/Pages/payments.js',
                'resources/js/dashboard/Pages/product.js',
                'resources/js/dashboard/Pages/ProductCreate.js',
                'resources/js/dashboard/Pages/ProductQuantity.js',
                'resources/js/dashboard/Pages/products.js',
                'resources/js/dashboard/Pages/profile.js',
                'resources/js/dashboard/Pages/settings.js',
                'resources/js/dashboard/Pages/users.js',
                'resources/js/dashboard/Pages/QuestCreate.js',
                'resources/js/dashboard/Pages/QuestEdit.js',
                'resources/js/dashboard/Pages/Quest.js',
                'resources/js/Pages/quests.js'
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
        commonjsOptions: {
            transformMixedEsModules: true, // разрешает транспиляцию смешанных модулей
        }
        // target: 'es2017', // или 'es2017' для лучшей поддержки WeakMap
        // minify: false,
    },
    server: {
        cors: true,
        sourcemap: false, // Отключаем source map в режиме разработки
    },
    resolve: {
        alias: {
            popper: '/node_modules/@popperjs/core/lib/popper.js',
            '@': '/resources/js',
            // jquery: '/node_modules/jquery/dist/jquery.js',
        }
    },

});
