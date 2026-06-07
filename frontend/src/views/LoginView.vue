<template>
    <div class="login-page d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-card luxury-card">
            <div class="text-center mb-4">
                <h2 class="fw-bold luxury-heading">Welcome Back</h2>
                <p class="luxury-subtitle">Sign in to your BMS account</p>
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
                <button type="submit" class="btn btn-gradient w-100 luxury-btn" :disabled="loading">
                    <span v-if="loading">Signing in...</span>
                    <span v-else>Sign In</span>
                </button>
                <div v-if="error" class="alert alert-danger mt-3 luxury-alert">{{ error }}</div>
                <div class="text-center mt-3">
                    <router-link to="/register" class="luxury-link">Need an account? Register</router-link>
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
    background: linear-gradient(135deg, var(--dark-bg), var(--card-bg), #3D2B1F);
}

.login-card {
    width: 100%;
    max-width: 400px;
    padding: 2.5rem;
}

.form-control {
    background: rgba(166, 123, 91, 0.08);
    border: 1px solid rgba(166, 123, 91, 0.3);
    color: var(--text-primary);
    border-radius: 12px;
    padding: 0.875rem 1rem;
}

.form-control:focus {
    background: rgba(166, 123, 91, 0.12);
    border-color: var(--primary-color);
    color: var(--text-primary);
    box-shadow: 0 0 0 0.25rem rgba(166, 123, 91, 0.25);
}

.luxury-alert {
    background: rgba(160, 82, 45, 0.1);
    border: 1px solid rgba(160, 82, 45, 0.3);
    color: var(--text-primary);
}

.luxury-link {
    color: var(--primary-color);
    transition: all 0.3s ease;
}

.luxury-link:hover {
    color: var(--gold-text);
}

@media (max-width: 576px) {
    .login-card {
        padding: 1.5rem;
        max-width: 90vw;
    }

    .luxury-heading {
        font-size: 1.5rem;
    }

    .luxury-subtitle {
        font-size: 0.9rem;
    }
}

@media (max-width: 400px) {
    .login-card {
        padding: 1rem;
        max-width: 95vw;
    }

    .luxury-heading {
        font-size: 1.3rem;
    }
}
</style>