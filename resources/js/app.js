import './bootstrap';
import { createInertiaApp } from '@inertiajs/svelte'
import { mount } from 'svelte'

// Import CSS di sini atau di Blade, tapi kita coba di sini dulu
import '../css/app.css';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true })
        const page = pages[`./Pages/${name}.svelte`]

        // Debugging: Kita intip apa isi 'page' sebenernya
        console.log('Isi file page:', page);

        return page;
        // Catatan: Di Svelte 5 + Vite terbaru, kadang gak butuh .default 
        // kalau udah otomatis ter-resolve sebagai komponen.
    },
    setup({ el, App, props }) {
        console.log('Proses Mounting...');
        mount(App, { target: el, props })
    },
})