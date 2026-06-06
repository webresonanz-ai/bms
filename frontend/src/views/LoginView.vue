<template>
    <div class="login-page d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-card luxury-card">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Welcome Back</h2>
                <p class="text-white-50">Sign in to your BMS account</p>
            </div>

            <form @submit.prevent="handleLogin">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" v-model="form.email" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" v-model="form.password" required>
                </div>
                <button type="submit" class="btn btn-gradient w-100" :disabled="loading">
                    <span v-if="loading">Signing in...</span>
                    <span v-else>Sign In</span>
                </button>
                <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
                <div class="text-center mt-3">
                    <router-link to="/register" class="text-white-50">Need an account? Register</router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
    email: '',
    password: ''
})

const loading = ref(false)
const error = ref('')

async function handleLogin() {
    loading.value = true
    error.value = ''
    try {
        await authStore.login(form)
        router.push('/')
    } catch (err) {
        error.value = err.message || 'Login failed'
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.login-page {
    background: linear-gradient(135deg, #0f0c29, #1a1a2e, #16213e);
}

.login-card {
    width: 100%;
    max-width: 400px;
    padding: 2.5rem;
}

.form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 8px;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>