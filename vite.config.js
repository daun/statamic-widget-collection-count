import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/addon.css',
            ],
            publicDirectory: 'resources/dist',
            refresh: true,
        }),
    ],
});
