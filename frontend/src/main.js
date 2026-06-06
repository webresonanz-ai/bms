import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'

// Import Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// Import custom styles
import './assets/styles/custom.scss'

const app = createApp(App)

const pinia = createPinia()
app.use(pinia)
app.use(router)

const authStore = useAuthStore()
authStore.initFromStorage()

app.mount('#app')