<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const galleryItems = [
  { icon: 'bi-music-note-beamed', title: 'Concert Hall', category: 'Performance' },
  { icon: 'bi-people', title: 'Ensemble', category: 'Group' },
  { icon: 'bi-mic', title: 'Solo Moments', category: 'Performance' },
  { icon: 'bi-globe', title: 'World Tour', category: 'Tour' },
  { icon: 'bi-heart', title: 'Backstage', category: 'Behind the Scenes' },
  { icon: 'bi-star', title: 'Award Night', category: 'Milestone' },
  { icon: 'bi-camera', title: 'Rehearsal', category: 'Behind the Scenes' },
  { icon: 'bi-chat-quote', title: 'Interview', category: 'Media' },
  { icon: 'bi-music-note-list', title: 'Recording', category: 'Studio' }
]

const activeItem = ref(null)
const activeIndex = ref(-1)

const openLightbox = (item, index) => {
  activeItem.value = item
  activeIndex.value = index
  document.body.style.overflow = 'hidden'
}

const closeLightbox = () => {
  activeItem.value = null
  activeIndex.value = -1
  document.body.style.overflow = ''
}

const showPrev = () => {
  const next = (activeIndex.value - 1 + galleryItems.length) % galleryItems.length
  activeIndex.value = next
  activeItem.value = galleryItems[next]
}

const showNext = () => {
  const next = (activeIndex.value + 1) % galleryItems.length
  activeIndex.value = next
  activeItem.value = galleryItems[next]
}

const handleKey = (e) => {
  if (!activeItem.value) return
  if (e.key === 'Escape') closeLightbox()
  else if (e.key === 'ArrowLeft') showPrev()
  else if (e.key === 'ArrowRight') showNext()
}

onMounted(() => window.addEventListener('keydown', handleKey))
onUnmounted(() => {
  window.removeEventListener('keydown', handleKey)
  document.body.style.overflow = ''
})
</script>

<template>
  <div>
    <div class="page-header">
      <div class="container">
        <p class="hero-subtitle mb-3 fade-in">Moments</p>
        <h1 class="hero-title fade-in-up">Gallery</h1>
        <div class="gold-divider"></div>
        <p class="mt-4 fade-in" style="max-width: 600px; margin-inline: auto;">
          A visual journey through our performances, tours, and cherished memories.
        </p>
      </div>
    </div>

    <section class="section-padding bg-dark-custom">
      <div class="container">
        <div class="row g-4">
          <div
            v-for="(item, index) in galleryItems"
            :key="item.title"
            class="col-lg-4 col-md-6"
            v-reveal="{ delay: (index % 3) * 100 }"
          >
            <div
              class="gallery-item"
              role="button"
              tabindex="0"
              :aria-label="`View ${item.title}`"
              @click="openLightbox(item, index)"
              @keydown.enter="openLightbox(item, index)"
            >
              <div class="gallery-item-inner">
                <i :class="`bi ${item.icon}`"></i>
              </div>
              <div class="gallery-caption">
                <p class="text-gold small mb-0" style="letter-spacing: 0.15em; text-transform: uppercase; font-size: 0.7rem;">
                  {{ item.category }}
                </p>
                <h6 class="text-cream mb-0">{{ item.title }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Lightbox -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="activeItem"
          class="lightbox-backdrop"
          role="dialog"
          aria-modal="true"
          :aria-label="activeItem.title"
          @click.self="closeLightbox"
        >
          <button class="lightbox-close" aria-label="Close" @click="closeLightbox">
            <i class="bi bi-x-lg"></i>
          </button>
          <button class="lightbox-nav prev" aria-label="Previous" @click="showPrev">
            <i class="bi bi-chevron-left"></i>
          </button>

          <div class="lightbox-panel">
            <div class="lightbox-frame">
              <i :class="`bi ${activeItem.icon}`"></i>
            </div>
            <div class="mt-4">
              <p class="text-gold small mb-1" style="letter-spacing: 0.2em; text-transform: uppercase;">
                {{ activeItem.category }}
              </p>
              <h4 class="text-cream mb-0">{{ activeItem.title }}</h4>
              <p class="small text-muted mt-2 mb-0">{{ activeIndex + 1 }} / {{ galleryItems.length }}</p>
            </div>
          </div>

          <button class="lightbox-nav next" aria-label="Next" @click="showNext">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>
