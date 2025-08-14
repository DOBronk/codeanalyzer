import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PrimeVue from "primevue/config";
import ToastService from 'primevue/toastservice';
import Noir from './Noir.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`../vue/${name}.vue`, import.meta.glob('../vue/**/*.vue')),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(PrimeVue, {
        theme: {
          preset: Noir,
          options: {
            prefix: 'p',
            darkModeSelector: '.p-dark',
            cssLayer: false,
          },
        },
      })
      .use(ToastService)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
