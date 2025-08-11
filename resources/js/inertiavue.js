import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PrimeVue from "primevue/config";
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import TreeTable from 'primevue/treetable';
import Column from 'primevue/column';
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
            .component('InputText', InputText)
            .component('Textarea', Textarea)
            .component('TreeTable', TreeTable)
            .component('Column', Column)
            .component('Textarea', Textarea)
            .component('Toast', Toast)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
