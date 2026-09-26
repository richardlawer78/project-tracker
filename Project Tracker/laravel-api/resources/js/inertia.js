import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import MainLayout from './layouts/MainLayout.vue'

import './assets/css/styles.css'
import './assets/css/pm-custom.css'
import './assets/js/preline.js'

createInertiaApp({
  title: (title) => (title ? `Project Tracker - ${title}` : 'Project Tracker'),
  resolve: (name) => {
    const pages = import.meta.glob('./views/**/*.vue', { eager: true })

    console.log('Inertia page name:', name)
    console.log('Available pages:', Object.keys(pages))

    const page = pages[`./views/${name}.vue`]

    if (!page) {
        throw new Error(`Inertia page not found: ${name}`)
    }

    page.default.layout ??= MainLayout
    return page
},
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(createPinia())
      .mount(el)
  },
})
