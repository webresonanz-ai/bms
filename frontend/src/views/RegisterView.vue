<template>
    <div class="register-page d-flex align-items-center justify-content-center min-vh-100">
        <div class="register-card luxury-card">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Create Account</h2>
                <p class="text-white-50">Join the BMS Choir community</p>
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
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" v-model="form.role">
                        <option value="Member">Member</option>
                        <option value="Music Director">Music Director</option>
                        <option value="Section Leader">Section Leader</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Phone (optional)</label>
                    <input type="tel" class="form-control" v-model="form.phone">
                </div>
                <button type="submit" class="btn btn-gradient w-100" :disabled="loading">
                    <span v-if="loading">Creating account...</span>
                    <span v-else>Create Account</span>
                </button>
                <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
                <div class="text-center mt-3">
                    <router-link to="/login" class="text-white-50">Already have an account? Sign in</router-link>
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

<style scoped>
.register-page {
    background: linear-gradient(135deg, #0f0c29, #1a1a2e, #16213e);
}

.register-card {
    width: 100%;
    max-width: 400px;
    padding: 2.5rem;
}

.form-control,
.form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 8px;
}

.form-control:focus,
.form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>