import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Flaches Array für input, aber mit klarer Trennung
            input: [
                'resources/frontend/css/app.css',
                'resources/frontend/js/app.js',
                'resources/backend/css/app.css',
                'resources/backend/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        chunkSizeWarningLimit: 4000, // Erhöht das Limit auf 2000 kB
        rollupOptions: {
            output: {
                // Separate Verzeichnisse für Frontend und Backend
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.includes('frontend')) {
                        return 'assets/frontend/[name]-[hash][extname]';
                    }
                    if (assetInfo.name.includes('backend')) {
                        return 'assets/backend/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name.includes('frontend')) {
                        return 'assets/frontend/[name]-[hash].js';
                    }
                    if (chunkInfo.name.includes('backend')) {
                        return 'assets/backend/[name]-[hash].js';
                    }
                    return 'assets/[name]-[hash].js';
                },
                chunkFileNames: 'assets/[name]-[hash].js',
            },
        },
    },
});
