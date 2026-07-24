// import { createSSRApp, h } from 'vue';
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
//             case name.startsWith('Feedback/'):
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
//         const app = createSSRApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(i18n);

//         if (el) {
//             app.mount(el);
//         }

//         return app;
//     },
// });

// // This will set light / dark mode on page load...
// initializeTheme();

// // This will listen for flash toast data from the server...
// initializeFlashToast();
import { createSSRApp, h } from 'vue';
import { createInertiaApp, usePage } from '@inertiajs/vue3';
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
//     progress: {
//         color: '#4B5563',
//     },
//     setup({ el, App, props, plugin }) {
//         const app = createSSRApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(i18n);

//         if (el) {
//             app.mount(el);
//         }

//         return app;
//     },
// });
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        const app = createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n);

        // Set locale awal dari backend (fix refresh problem)
        const initialLocale = (props.initialPage.props as any).locale as string;
        if (initialLocale) {
            i18n.global.locale.value = initialLocale as any;
        }

        // Update locale tiap navigasi Inertia tanpa refresh
        document.addEventListener('inertia:success', (event: any) => {
            const newLocale = event.detail?.page?.props?.locale;
            if (newLocale) {
                i18n.global.locale.value = newLocale;
            }
        });

        if (el) {
            app.mount(el!);
        }

        return app;
    },
});


// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();