import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: {
            origin: 'http://localhost:8000', // your PHP frontend URL
            methods: ['GET', 'POST', 'PUT', 'DELETE'], // allowed methods
            allowedHeaders: ['Content-Type', 'Authorization'], // allowed headers
            credentials: true, // if you want to send cookies or authentication headers
        },
        hmr: {
            host: 'localhost',
            port: 5173,
            clientPort: 8000,
        },
    }
});
