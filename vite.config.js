import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite'; // Import plugin baru ini

export default defineConfig({
    plugins: [
        tailwindcss(), // Taruh di atas laravel plugin
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        svelte(),
    ],
    server: {
        host: '0.0.0.0', // Biar bisa diakses dari IP mana pun
        hmr: {
            host: '10.159.173.90', // Masukin IP Laptop lo di sini
        },
    },
});