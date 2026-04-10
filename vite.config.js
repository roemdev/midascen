import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],

    // Fix para entornos Docker en Proxmox/LXC donde esbuild
    // no puede hacer spawn de procesos hijos (error ENOTCONN)
    optimizeDeps: {
        esbuildOptions: {
            // Deshabilita el uso del binario nativo de esbuild
            platform: 'node',
        },
    },

    build: {
        // Usa rollup puro para el build en lugar de depender del binario de esbuild
        rollupOptions: {},
        // Minificación con terser en lugar de esbuild para evitar el spawn
        minify: 'terser',
    },
});