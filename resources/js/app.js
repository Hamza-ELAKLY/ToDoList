import './bootstrap';
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'

console.log('Starting full Vue app...')

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.mount('#app')

console.log('Full Vue app mounted successfully!')
