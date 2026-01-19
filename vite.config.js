import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/frontend/css/styles.css',
                'resources/assets/admin/css/styles_admin.css',
                'resources/assets/admin/css/modal_custom.css',
                'resources/assets/frontend/css/auth.css',
                'resources/assets/frontend/css/header.css',
                'resources/assets/frontend/css/footer.css',
                'resources/assets/frontend/css/products.css',
                'resources/assets/frontend/css/search.css',
                'resources/assets/admin/css/product-common.css',
                'resources/assets/admin/css/product-show.css',
                'resources/assets/admin/css/product-form.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'taphoammo.local',
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: 'taphoammo.local',
            protocol: 'http',
            port: 5173,
        },
    },
    
    
});
