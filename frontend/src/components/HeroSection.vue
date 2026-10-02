<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useChoirStore } from '../stores/choir'
import { resolveUploadSrc } from '../utils/imageCompress'

const choirStore = useChoirStore()

// Custom hero background uploaded from Admin → Website (NULL = gradient only)
const heroBg = computed(() => resolveUploadSrc(choirStore.settings?.hero_background))

const parallaxStyle = ref({})
let rafId = null

const handleScroll = () => {
  if (rafId) return
  rafId = requestAnimationFrame(() => {
    const y = window.scrollY
    if (y < window.innerHeight * 1.2) {
      parallaxStyle.value = { transform: `translate3d(0, ${y * 0.25}px, 0)` }
    }
    rafId = null
  })
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  choirStore.fetchSettings()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  if (rafId) cancelAnimationFrame(rafId)
})
</script>

<template>
  <section class="hero-section" :class="{ 'has-custom-bg': heroBg }">
    <div
      class="hero-parallax"
      :style="parallaxStyle"
      aria-hidden="true"
    >
      <div
        v-if="heroBg"
        class="hero-bg-photo"
        :style="{ backgroundImage: `url('${heroBg}')` }"
      ></div>
      <div v-if="heroBg" class="hero-bg-overlay"></div>
      <div class="orb orb-1"></div>
      <div class="orb orb-2"></div>
    </div>

    <div class="hero-ring ring-1" aria-hidden="true"></div>
    <div class="hero-ring ring-2" aria-hidden="true"></div>

    <i class="bi bi-music-note floating-note" style="top: 15%; left: 10%; animation-delay: 0s;" aria-hidden="true"></i>
    <i class="bi bi-music-note-beamed floating-note" style="top: 25%; right: 15%; animation-delay: 1s;" aria-hidden="true"></i>
    <i class="bi bi-music-note-list floating-note" style="bottom: 20%; left: 20%; animation-delay: 2s;" aria-hidden="true"></i>
    <i class="bi bi-music-note floating-note" style="bottom: 30%; right: 10%; animation-delay: 3s;" aria-hidden="true"></i>

    <div class="container position-relative">
      <div class="row justify-content-center text-center">
        <div class="col-lg-10">
          <p class="hero-subtitle mb-4 fade-in">
            Est. 2001 · Jakarta, Indonesia
          </p>

          <h1 class="hero-title mb-4">
            <span class="line"><span style="animation-delay: 0.15s, 1.3s;">Batavia</span></span>
            <span class="line"><span style="animation-delay: 0.35s, 1.3s;">Madrigal Singers</span></span>
          </h1>

          <div class="gold-divider fade-in" style="animation-delay: 0.7s;"></div>

          <p
            class="lead text-cream mx-auto mb-5 fade-in-up"
            style="max-width: 700px; animation-delay: 0.8s; opacity: 0.9;"
          >
            Where voices unite in perfect harmony. A premier chamber choir
            dedicated to the timeless art of choral music, weaving stories
            through song from the heart of Indonesia to the world stage.
          </p>

          <div
            class="d-flex gap-3 justify-content-center flex-wrap fade-in-up"
            style="animation-delay: 1s;"
          >
            <RouterLink to="/about" class="btn btn-gold">Discover Our Story</RouterLink>
            <RouterLink to="/events" class="btn btn-outline-gold">Upcoming Concerts</RouterLink>
          </div>
        </div>
      </div>
    </div>

    <a href="#highlights" class="scroll-indicator" aria-label="Scroll to highlights">
      <i class="bi bi-chevron-double-down"></i>
    </a>
  </section>
</template>

<style scoped>
/* Custom background photo (uploaded from Admin → Website) */
.hero-bg-photo {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  animation: heroBgIn 2.5s ease forwards;
}

/* Dark overlay so the title stays readable over any photo */
.hero-bg-overlay {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(180deg, rgba(10, 10, 22, 0.62) 0%, rgba(10, 10, 22, 0.45) 45%, rgba(15, 15, 30, 0.88) 100%),
    radial-gradient(circle at 50% 40%, transparent 30%, rgba(10, 10, 22, 0.5) 100%);
}

@keyframes heroBgIn {
  from { opacity: 0; transform: scale(1.04); }
  to   { opacity: 1; transform: scale(1); }
}
</style>
