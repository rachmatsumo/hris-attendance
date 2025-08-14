import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        https: true,     // pakai HTTPS
        host: true,      // biar bisa diakses dari HP/ngrok
        port: 5173,      // port default Vite
        strictPort: true // gagal kalau port sudah dipakai
    },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
