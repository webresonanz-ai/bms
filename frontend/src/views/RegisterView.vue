<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { renderGoogleButton, isGoogleConfigured } from '../utils/googleAuth'

const router = useRouter()
const auth   = useAuthStore()

const loading  = ref(false)
const googleLoading = ref(false)
const apiError = ref('')
const fieldErrors = reactive({})

const form = reactive({
  name:                  '',
  email:                 '',
  password:              '',
  password_confirmation: '',
})

const showPassword  = ref(false)
const showConfirm   = ref(false)
const googleBtnEl = ref(null)

onMounted(async () => {
  if (!isGoogleConfigured()) return
  try {
    await renderGoogleButton(googleBtnEl.value, handleGoogleCredential, { text: 'signup_with' })
  } catch (e) {
    console.warn('[Google sign-up]', e.message)
  }
})

async function handleGoogleCredential(idToken) {
  apiError.value = ''
  googleLoading.value = true
  try {
    await auth.loginWithGoogle(idToken)
    router.push({ name: 'home' })
  } catch (err) {
    apiError.value = err.message || 'Google sign-up failed. Please try again.'
  } finally {
    googleLoading.value = false
  }
}

// Live password strength indicator
const strength = computed(() => {
  const pw = form.password
  if (!pw) return 0
  let score = 0
  if (pw.length >= 8)           score++
  if (/[A-Z]/.test(pw))         score++
  if (/[0-9]/.test(pw))         score++
  if (/[^A-Za-z0-9]/.test(pw))  score++
  return score
})

const strengthLabel = computed(() => {
  const labels = ['', 'Weak', 'Fair', 'Good', 'Strong']
  return labels[strength.value] || ''
})

const strengthClass = computed(() => {
  const classes = ['', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong']
  return classes[strength.value] || ''
})

async function handleSubmit() {
  apiError.value = ''
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])

  loading.value = true
  try {
    await auth.register({
      name:                  form.name,
      email:                 form.email,
      password:              form.password,
      password_confirmation: form.password_confirmation,
    })
    router.push({ name: 'home' })
  } catch (err) {
    if (err.errors && Object.keys(err.errors).length) {
      Object.assign(fieldErrors, err.errors)
    } else {
      apiError.value = err.message || 'Registration failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
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

        <h1 class="text-cream text-center mb-1" style="font-size: 1.9rem;">Join the Choir</h1>
        <p class="text-center mb-4" style="color: var(--bms-muted); font-size: 0.85rem; letter-spacing: 0.05em;">
          Create your member account
        </p>

        <div class="gold-divider mb-4"></div>

        <transition name="fade">
          <div v-if="apiError" class="auth-alert mb-4" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ apiError }}
          </div>
        </transition>

        <form @submit.prevent="handleSubmit" novalidate>
          <!-- Full name -->
          <div class="mb-3">
            <label for="reg-name" class="form-label-lux">Full Name</label>
            <div class="input-group-lux" :class="{ 'has-error': fieldErrors.name }">
              <i class="bi bi-person input-icon"></i>
              <input
                id="reg-name"
                v-model="form.name"
                type="text"
                class="form-control lux"
                placeholder="Your full name"
                autocomplete="name"
                required
                :aria-describedby="fieldErrors.name ? 'name-err' : undefined"
                :aria-invalid="!!fieldErrors.name"
              />
            </div>
            <p v-if="fieldErrors.name" id="name-err" class="field-error mt-1">{{ fieldErrors.name }}</p>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="reg-email" class="form-label-lux">Email Address</label>
            <div class="input-group-lux" :class="{ 'has-error': fieldErrors.email }">
              <i class="bi bi-envelope input-icon"></i>
              <input
                id="reg-email"
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
            <p v-if="fieldErrors.email" id="email-err" class="field-error mt-1">{{ fieldErrors.email }}</p>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="reg-password" class="form-label-lux">Password</label>
            <div class="input-group-lux" :class="{ 'has-error': fieldErrors.password }">
              <i class="bi bi-lock input-icon"></i>
              <input
                id="reg-password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-control lux"
                placeholder="Min. 8 chars, 1 uppercase, 1 number"
                autocomplete="new-password"
                required
                :aria-describedby="fieldErrors.password ? 'pw-err' : 'pw-hint'"
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

            <!-- Strength meter -->
            <div v-if="form.password" class="strength-meter mt-2" aria-live="polite" id="pw-hint">
              <div class="strength-bars">
                <span
                  v-for="i in 4"
                  :key="i"
                  class="strength-bar"
                  :class="{ active: strength >= i, [strengthClass]: strength >= i }"
                ></span>
              </div>
              <span class="strength-text" :class="strengthClass">{{ strengthLabel }}</span>
            </div>

            <p v-if="fieldErrors.password" id="pw-err" class="field-error mt-1">{{ fieldErrors.password }}</p>
          </div>

          <!-- Confirm password -->
          <div class="mb-4">
            <label for="reg-confirm" class="form-label-lux">Confirm Password</label>
            <div class="input-group-lux" :class="{ 'has-error': fieldErrors.password_confirmation }">
              <i class="bi bi-lock-fill input-icon"></i>
              <input
                id="reg-confirm"
                v-model="form.password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                class="form-control lux"
                placeholder="Repeat your password"
                autocomplete="new-password"
                required
                :aria-describedby="fieldErrors.password_confirmation ? 'confirm-err' : undefined"
                :aria-invalid="!!fieldErrors.password_confirmation"
              />
              <button
                type="button"
                class="toggle-pw"
                :aria-label="showConfirm ? 'Hide password' : 'Show password'"
                @click="showConfirm = !showConfirm"
              >
                <i :class="showConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </button>
            </div>
            <p v-if="fieldErrors.password_confirmation" id="confirm-err" class="field-error mt-1">
              {{ fieldErrors.password_confirmation }}
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
            {{ loading ? 'Creating Account…' : 'Create Account' }}
          </button>
        </form>

        <!-- Divider -->
        <div class="auth-divider" aria-hidden="true"><span>or</span></div>

        <!-- Google Sign-Up (official GIS button renders here) -->
        <div class="google-wrap">
          <div ref="googleBtnEl" class="google-btn" :class="{ 'is-loading': googleLoading }"></div>
          <p v-if="googleLoading" class="google-status">Signing up with Google…</p>
        </div>

        <p class="text-center mt-4 mb-0" style="font-size: 0.85rem; color: var(--bms-muted);">
          Already have an account?
          <RouterLink to="/login" class="text-gold text-decoration-none ms-1">Sign in</RouterLink>
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
  right: -100px;
  background: radial-gradient(circle, rgba(201, 169, 97, 0.1), transparent 70%);
}

.auth-orb--2 {
  width: 400px;
  height: 400px;
  bottom: -80px;
  left: -80px;
  background: radial-gradient(circle, rgba(139, 30, 63, 0.1), transparent 70%);
}

.auth-card {
  position: relative;
  z-index: 1;
  max-width: 500px;
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

/* Password strength meter */
.strength-meter {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.strength-bars {
  display: flex;
  gap: 4px;
  flex: 1;
}

.strength-bar {
  flex: 1;
  height: 3px;
  border-radius: 2px;
  background: rgba(201, 169, 97, 0.15);
  transition: background 0.3s ease;
}

.strength-bar.active.strength-weak   { background: #e05c5c; }
.strength-bar.active.strength-fair   { background: #e0a85c; }
.strength-bar.active.strength-good   { background: #a8c85c; }
.strength-bar.active.strength-strong { background: #5cc87a; }

.strength-text {
  font-size: 0.72rem;
  letter-spacing: 0.1em;
  min-width: 46px;
  text-align: right;
}

.strength-text.strength-weak   { color: #e05c5c; }
.strength-text.strength-fair   { color: #e0a85c; }
.strength-text.strength-good   { color: #a8c85c; }
.strength-text.strength-strong { color: #5cc87a; }

.auth-divider {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 1.25rem 0;
  color: var(--bms-muted);
  font-size: 0.75rem;
  letter-spacing: 0.2em;
  text-transform: uppercase;
}
.auth-divider::before,
.auth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: rgba(201, 169, 97, 0.25);
}

.google-wrap {
  display: flex;
  flex-direction: column;
  align-items: stretch;
}
.google-btn {
  display: flex;
  justify-content: center;
  min-height: 44px;
}
.google-btn.is-loading {
  opacity: 0.6;
  pointer-events: none;
}
.google-status {
  text-align: center;
  font-size: 0.78rem;
  color: var(--bms-muted);
  margin: 0.5rem 0 0;
}
</style>
