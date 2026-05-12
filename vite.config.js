import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/trade-chart.js',
                'resources/js/chart-demo.js',
                'resources/js/trading-session/main.js',
                'resources/js/spot-trading/main.js',
                'resources/css/landing2.css',
                'resources/js/pages/landing2.js',
                'resources/js/admin/dashboard-chart.js',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/manager.js',
                'resources/js/admin/session-realtime.js',
                'resources/js/auth/signin.js',
                'resources/js/referral.js',
                'resources/js/balance-realtime.js',
                'resources/js/profile.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
