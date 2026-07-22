// import { createInertiaApp } from '@inertiajs/vue3';
// import { initializeTheme } from '@/composables/useAppearance';
// import AppLayout from '@/layouts/AppLayout.vue';
// import AuthLayout from '@/layouts/AuthLayout.vue';
// import SettingsLayout from '@/layouts/settings/Layout.vue';
// import { initializeFlashToast } from '@/lib/flashToast';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// createInertiaApp({
//     title: (title) => (title ? `${title} - ${appName}` : appName),
//     layout: (name) => {
//         switch (true) {
//             case name === 'Welcome':
//                 return null;
//             case name.startsWith('auth/'):
//                 return AuthLayout;
//             case name.startsWith('settings/'):
//                 return [AppLayout, SettingsLayout];
//             default:
//                 return AppLayout;
//         }
//     },
//     progress: {
//         color: '#4B5563',
//     },
// });

// // This will set light / dark mode on page load...
// initializeTheme();

// // This will listen for flash toast data from the server...
// initializeFlashToast();
// import { createApp, h } from 'vue';
// import { createInertiaApp } from '@inertiajs/vue3';
// import { initializeTheme } from '@/composables/useAppearance';
// import AppLayout from '@/layouts/AppLayout.vue';
// import AuthLayout from '@/layouts/AuthLayout.vue';
// import SettingsLayout from '@/layouts/settings/Layout.vue';
// import { initializeFlashToast } from '@/lib/flashToast';
// import { i18n } from '@/i18n';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// createInertiaApp({
//     title: (title) => (title ? `${title} - ${appName}` : appName),
//     layout: (name) => {
//         switch (true) {
//             case name === 'Welcome':
//                 return null;
//             case name.startsWith('auth/'):
//                 return AuthLayout;
//             case name.startsWith('settings/'):
//                 return [AppLayout, SettingsLayout];
//             default:
//                 return AppLayout;
//         }
//     },
//     progress: {
//         color: '#4B5563',
//     },
//     setup({ el, App, props, plugin }) {
//     createApp({ render: () => h(App, props) })
//         .use(plugin)
//         .use(i18n)
//         .mount(el!); // tambahkan tanda seru di sini
// },
// });

// // This will set light / dark mode on page load...
// initializeTheme();

// // This will listen for flash toast data from the server...
// initializeFlashToast();
import { createSSRApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import { i18n } from '@/i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
            case name.startsWith('Feedback/'):
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        const app = createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n);

        if (el) {
            app.mount(el);
        }

        return app;
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();