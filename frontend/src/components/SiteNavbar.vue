<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router  = useRouter()
const auth    = useAuthStore()
const scrolled = ref(false)
const isOpen   = ref(false)

let rafId = null

const handleScroll = () => {
  if (rafId) return
  rafId = requestAnimationFrame(() => {
    scrolled.value = window.scrollY > 50
    rafId = null
  })
}

function handleLogout() {
  auth.logout()
  router.push({ name: 'home' })
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  const menu = document.getElementById('navMenu')
  if (menu) {
    menu.addEventListener('shown.bs.collapse',  () => { isOpen.value = true  })
    menu.addEventListener('hidden.bs.collapse', () => { isOpen.value = false })
  }
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  if (rafId) cancelAnimationFrame(rafId)
})
</script>

<template>
  <nav class="navbar navbar-expand-lg navbar-custom fixed-top" :class="{ scrolled }">
    <div class="container">
      <RouterLink class="navbar-brand d-flex align-items-center" to="/">
        <i class="bi bi-music-note-beamed text-gold fs-4 me-2"></i>
        <div>
          <div class="text-cream fw-bold" style="letter-spacing: 0.15em; font-size: 0.95rem;">BATAVIA</div>
          <div class="text-gold" style="letter-spacing: 0.3em; font-size: 0.6rem;">MADRIGAL SINGERS</div>
        </div>
      </RouterLink>
      <button
        class="navbar-toggler border-0"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navMenu"
        aria-controls="navMenu"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon-lux">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item">
            <RouterLink class="nav-link" to="/">Home</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link" to="/about">About</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link" to="/members">Members</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link" to="/gallery">Gallery</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link" to="/events">Events</RouterLink>
          </li>
          <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
            <RouterLink class="btn btn-outline-gold btn-sm" to="/contact">Contact</RouterLink>
          </li>

          <!-- Auth controls -->
          <template v-if="auth.isLoggedIn">
            <li v-if="auth.isAdmin" class="nav-item ms-lg-3 mt-2 mt-lg-0">
              <RouterLink class="btn btn-gold btn-sm" to="/admin">
                <i class="bi bi-speedometer2 me-1"></i>Dashboard
              </RouterLink>
            </li>
            <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
              <span class="nav-user text-gold">
                <i class="bi bi-person-circle me-1"></i>{{ auth.user?.name?.split(' ')[0] }}
              </span>
            </li>
            <li class="nav-item ms-lg-2 mt-1 mt-lg-0">
              <button class="btn btn-sm nav-logout" @click="handleLogout">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
              </button>
            </li>
          </template>
          <template v-else>
            <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
              <RouterLink class="btn btn-sm nav-login" to="/login">Sign In</RouterLink>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.navbar-brand {
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.navbar-brand:hover {
  opacity: 0.85;
}

.btn-sm {
  padding: 0.5rem 1.25rem;
  font-size: 0.72rem;
}

.nav-user {
  font-size: 0.82rem;
  letter-spacing: 0.08em;
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 0.25rem;
}

.nav-login {
  background: transparent;
  color: var(--bms-gold);
  border: 1px solid rgba(201, 169, 97, 0.55);
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.72rem;
  font-weight: 500;
  border-radius: 2px;
  transition: background 0.3s ease, color 0.3s ease, transform 0.3s ease;
  text-decoration: none;
}

.nav-login:hover {
  background: var(--bms-gold);
  color: var(--bms-darker);
  transform: translateY(-2px);
}

.nav-logout {
  background: transparent;
  color: var(--bms-muted);
  border: 1px solid rgba(168, 162, 147, 0.35);
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.72rem;
  font-weight: 500;
  border-radius: 2px;
  transition: background 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

.nav-logout:hover {
  background: rgba(139, 30, 63, 0.2);
  color: #e8a0b0;
  border-color: rgba(139, 30, 63, 0.5);
}
</style>
