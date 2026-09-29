<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const scrolled = ref(false)
const isOpen = ref(false)

let rafId = null

const handleScroll = () => {
  if (rafId) return
  rafId = requestAnimationFrame(() => {
    scrolled.value = window.scrollY > 50
    rafId = null
  })
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  // Watch bootstrap's collapse state to animate the hamburger
  const menu = document.getElementById('navMenu')
  if (menu) {
    menu.addEventListener('shown.bs.collapse', () => { isOpen.value = true })
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
</style>
