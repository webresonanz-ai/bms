<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import SiteNavbar from './components/SiteNavbar.vue'
import SiteFooter from './components/SiteFooter.vue'

const route = useRoute()
const isAdminArea = computed(() => route.path.startsWith('/admin'))

const scrollProgress = ref(0)
const showBackToTop = ref(false)
const spotlightOn = ref(false)
const spotlightStyle = ref({})

let rafId = null

const handleScroll = () => {
  if (rafId) return
  rafId = requestAnimationFrame(() => {
    const totalHeight = document.documentElement.scrollHeight - window.innerHeight
    scrollProgress.value = totalHeight > 0 ? (window.scrollY / totalHeight) * 100 : 0
    showBackToTop.value = window.scrollY > 600
    rafId = null
  })
}

const handlePointer = (e) => {
  spotlightOn.value = true
  spotlightStyle.value = {
    '--mx': `${e.clientX}px`,
    '--my': `${e.clientY}px`
  }
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  window.addEventListener('pointermove', handlePointer, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('pointermove', handlePointer)
  if (rafId) cancelAnimationFrame(rafId)
})
</script>

<template>
  <div id="app">
    <a class="skip-link" href="#main-content">Skip to main content</a>

    <div
      class="scroll-progress"
      :style="{ width: scrollProgress + '%' }"
      aria-hidden="true"
    ></div>

    <div
      class="noise-overlay"
      aria-hidden="true"
    ></div>

    <div
      class="page-spotlight"
      :class="{ on: spotlightOn }"
      :style="spotlightStyle"
      aria-hidden="true"
    ></div>

    <SiteNavbar v-if="!isAdminArea" />

    <main id="main-content">
      <RouterView v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </RouterView>
    </main>

    <SiteFooter v-if="!isAdminArea" />

    <transition name="fade">
      <button
        v-if="showBackToTop"
        class="back-to-top"
        type="button"
        aria-label="Back to top"
        @click="scrollToTop"
      >
        <i class="bi bi-arrow-up"></i>
      </button>
    </transition>
  </div>
</template>

<style scoped>
.page-enter-active,
.page-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
