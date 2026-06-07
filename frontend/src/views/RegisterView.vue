<template>
    <div class="register-page d-flex align-items-center justify-content-center min-vh-100">
        <div class="register-card luxury-card">
            <div class="text-center mb-4">
                <h2 class="fw-bold luxury-heading">Create Account</h2>
                <p class="luxury-subtitle">Join the BMS Choir community</p>
            </div>

            <form @submit.prevent="handleRegister">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" v-model="form.name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" v-model="form.email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" v-model="form.password" required minlength="6">
                </div>
                <div class="mb-4">
                    <label class="form-label">Phone (optional)</label>
                    <input type="tel" class="form-control" v-model="form.phone">
                </div>
                <button type="submit" class="btn btn-gradient w-100 luxury-btn" :disabled="loading">
                    <span v-if="loading">Creating account...</span>
                    <span v-else>Create Account</span>
                </button>
                <div v-if="error" class="alert alert-danger mt-3 luxury-alert">{{ error }}</div>
                <div class="text-center mt-3">
                    <router-link to="/login" class="luxury-link">Already have an account? Sign in</router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.register-page {
    background: linear-gradient(135deg, var(--dark-bg), var(--card-bg), #3D2B1F);
}

.register-card {
    width: 100%;
    max-width: 400px;
    padding: 2.5rem;
}

.form-control,
.form-select {
    background: rgba(166, 123, 91, 0.08);
    border: 1px solid rgba(166, 123, 91, 0.3);
    color: var(--text-primary);
    border-radius: 12px;
    padding: 0.875rem 1rem;
}

.form-control:focus,
.form-select:focus {
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
    .register-card {
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
    .register-card {
        padding: 1rem;
        max-width: 95vw;
    }

    .luxury-heading {
        font-size: 1.3rem;
    }
}
</style>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
    name: '',
    email: '',
    password: '',
    role: 'Member',
    phone: ''
})

const loading = ref(false)
const error = ref('')

async function handleRegister() {
    loading.value = true
    error.value = ''
    try {
        await authStore.register(form)
        router.push('/')
    } catch (err) {
        error.value = err.message || 'Registration failed'
    } finally {
        loading.value = false
    }
}
</script>