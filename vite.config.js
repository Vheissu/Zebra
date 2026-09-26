import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                // Libre Franklin: a revival of Franklin Gothic, the American newspaper grotesque.
                bunny('Libre Franklin', {
                    weights: [400, 500, 700, 800],
                    styles: ['normal', 'italic'],
                    fallbacks: ['Franklin Gothic Medium', 'Arial Narrow', 'sans-serif'],
                }),
            ],
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
