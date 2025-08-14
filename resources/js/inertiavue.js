import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PrimeVue from "primevue/config";
import Noir from './Noir.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`../vue/${name}.vue`, import.meta.glob('../vue/**/*.vue')),
  setup({ el, App, props }) {
    return createApp({ render: () => h(App, props) })
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
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
