<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth   = useAuthStore()

const loading = ref(false)
const apiError = ref('')
const fieldErrors = reactive({})

const form = reactive({
  email:    '',
  password: '',
})

const showPassword = ref(false)

async function handleSubmit() {
  // Clear previous errors
  apiError.value = ''
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])

  loading.value = true
  try {
    await auth.login({ email: form.email, password: form.password })
    router.push({ name: 'home' })
  } catch (err) {
    if (err.errors && Object.keys(err.errors).length) {
      Object.assign(fieldErrors, err.errors)
    } else {
      apiError.value = err.message || 'Login failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <!-- Decorative background orbs (reuse hero palette) -->
    <div class="auth-bg" aria-hidden="true">
      <div class="auth-orb auth-orb--1"></div>
      <div class="auth-orb auth-orb--2"></div>
    </div>

    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh; padding-top: 5rem; padding-bottom: 3rem;">
      <div class="auth-card elegant-card p-4 p-md-5 w-100">

        <!-- Logo -->
        <div class="text-center mb-4">
          <RouterLink to="/" class="auth-brand text-decoration-none">
            <i class="bi bi-music-note-beamed text-gold" style="font-size: 2rem;"></i>
            <div class="text-cream fw-bold mt-1" style="letter-spacing: 0.15em; font-size: 0.9rem;">BATAVIA</div>
            <div class="text-gold" style="letter-spacing: 0.3em; font-size: 0.6rem;">MADRIGAL SINGERS</div>
          </RouterLink>
        </div>

        <h1 class="text-cream text-center mb-1" style="font-size: 1.9rem;">Welcome Back</h1>
        <p class="text-center mb-4" style="color: var(--bms-muted); font-size: 0.85rem; letter-spacing: 0.05em;">
          Sign in to your account
        </p>

        <div class="gold-divider mb-4"></div>

        <!-- Global API error -->
        <transition name="fade">
          <div v-if="apiError" class="auth-alert mb-4" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ apiError }}
          </div>
        </transition>

        <form @submit.prevent="handleSubmit" novalidate>
          <!-- Email -->
          <div class="mb-3">
            <label for="login-email" class="form-label-lux">Email Address</label>
            <div class="input-group-lux" :class="{ 'has-error': fieldErrors.email }">
              <i class="bi bi-envelope input-icon"></i>
              <input
                id="login-email"
                v-model="form.email"
                type="email"
                class="form-control lux"
                placeholder="you@example.com"
                autocomplete="email"
                required
                :aria-describedby="fieldErrors.email ? 'email-err' : undefined"
                :aria-invalid="!!fieldErrors.email"
              />
            </div>
            <p v-if="fieldErrors.email" id="email-err" class="field-error mt-1">
              {{ fieldErrors.email }}
            </p>
          </div>

          <!-- Password -->
          <div class="mb-4">
            <label for="login-password" class="form-label-lux">Password</label>
            <div class="input-group-lux" :class="{ 'has-error': fieldErrors.password }">
              <i class="bi bi-lock input-icon"></i>
              <input
                id="login-password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-control lux"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
                :aria-describedby="fieldErrors.password ? 'pw-err' : undefined"
                :aria-invalid="!!fieldErrors.password"
              />
              <button
                type="button"
                class="toggle-pw"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                @click="showPassword = !showPassword"
              >
                <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </button>
            </div>
            <p v-if="fieldErrors.password" id="pw-err" class="field-error mt-1">
              {{ fieldErrors.password }}
            </p>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            class="btn btn-gold w-100 d-flex align-items-center justify-content-center gap-2"
            :disabled="loading"
          >
            <span
              v-if="loading"
              class="spinner-border spinner-border-sm"
              role="status"
              aria-hidden="true"
            ></span>
            {{ loading ? 'Signing In…' : 'Sign In' }}
          </button>
        </form>

        <!-- Footer link -->
        <p class="text-center mt-4 mb-0" style="font-size: 0.85rem; color: var(--bms-muted);">
          Don't have an account?
          <RouterLink to="/register" class="text-gold text-decoration-none ms-1">Create one</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.auth-page {
  position: relative;
  min-height: 100vh;
  background: var(--bms-darker);
  overflow: hidden;
}

.auth-bg {
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 0;
}

.auth-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
}

.auth-orb--1 {
  width: 500px;
  height: 500px;
  top: -100px;
  left: -100px;
  background: radial-gradient(circle, rgba(201, 169, 97, 0.1), transparent 70%);
}

.auth-orb--2 {
  width: 400px;
  height: 400px;
  bottom: -80px;
  right: -80px;
  background: radial-gradient(circle, rgba(139, 30, 63, 0.1), transparent 70%);
}

.auth-card {
  position: relative;
  z-index: 1;
  max-width: 460px;
  margin: 0 auto;
}

.auth-brand {
  display: inline-block;
}

.auth-alert {
  background: rgba(139, 30, 63, 0.15);
  border: 1px solid rgba(139, 30, 63, 0.45);
  border-radius: 4px;
  padding: 0.75rem 1rem;
  color: #e8a0b0;
  font-size: 0.85rem;
}

/* Input group with leading icon */
.input-group-lux {
  position: relative;
}

.input-group-lux .input-icon {
  position: absolute;
  left: 0.9rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--bms-muted);
  pointer-events: none;
  font-size: 0.9rem;
  z-index: 2;
}

.input-group-lux .form-control.lux {
  padding-left: 2.4rem;
}

.input-group-lux.has-error .form-control.lux {
  border-color: rgba(139, 30, 63, 0.7);
}

.toggle-pw {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--bms-muted);
  cursor: pointer;
  padding: 0.2rem 0.3rem;
  transition: color 0.25s ease;
}

.toggle-pw:hover {
  color: var(--bms-gold);
}

.field-error {
  font-size: 0.78rem;
  color: #e8a0b0;
  margin-bottom: 0;
}
</style>
