<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useChoirStore } from '../stores/choir'
import { resolveUploadSrc } from '../utils/imageCompress'

const choirStore = useChoirStore()

// Live gallery items from the database (sorted by sort_order via API)
const galleryItems = computed(() => choirStore.galleryItems ?? [])

const photoSrc = (item) => resolveUploadSrc(item?.image_url)

function formatPhotoDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(`${dateStr}T00:00:00`)
  return Number.isNaN(d.getTime())
    ? dateStr
    : d.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' })
}

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
  const next = (activeIndex.value - 1 + galleryItems.value.length) % galleryItems.value.length
  activeIndex.value = next
  activeItem.value = galleryItems.value[next]
}

const showNext = () => {
  const next = (activeIndex.value + 1) % galleryItems.value.length
  activeIndex.value = next
  activeItem.value = galleryItems.value[next]
}

const handleKey = (e) => {
  if (!activeItem.value) return
  if (e.key === 'Escape') closeLightbox()
  else if (e.key === 'ArrowLeft') showPrev()
  else if (e.key === 'ArrowRight') showNext()
}

onMounted(async () => {
  window.addEventListener('keydown', handleKey)
  await choirStore.fetchGallery()
})
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
        <!-- Loading state (live fetch from database) -->
        <div v-if="choirStore.galleryLoading" class="row g-4">
          <div v-for="n in 6" :key="n" class="col-lg-4 col-md-6">
            <div class="gallery-item" aria-hidden="true">
              <div class="gallery-item-inner">
                <i class="bi bi-hourglass-split"></i>
              </div>
              <div class="gallery-caption">
                <p class="text-gold small mb-0">Loading…</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Error state -->
        <div v-else-if="choirStore.galleryError" class="text-center py-5">
          <i class="bi bi-exclamation-circle text-gold" style="font-size: 3rem; opacity: 0.5;"></i>
          <p class="text-muted mt-3">{{ choirStore.galleryError }}</p>
          <button class="btn btn-outline-gold mt-2" @click="choirStore.fetchGallery()">
            Try again
          </button>
        </div>

        <!-- Empty state -->
        <div v-else-if="!galleryItems.length" class="text-center py-5">
          <i class="bi bi-images text-gold" style="font-size: 3rem; opacity: 0.5;"></i>
          <p class="text-muted mt-3">No gallery items to display yet.</p>
        </div>

        <div v-else class="row g-4">
          <div
            v-for="(item, index) in galleryItems"
            :key="item.id ?? item.title"
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
                <img
                  v-if="photoSrc(item)"
                  :src="photoSrc(item)"
                  :alt="item.title"
                  loading="lazy"
                />
                <i v-else :class="`bi ${item.icon || 'bi-image'}`"></i>
              </div>
              <div class="gallery-caption">
                <p class="text-gold small mb-0" style="letter-spacing: 0.15em; text-transform: uppercase; font-size: 0.7rem;">
                  {{ item.category }}
                </p>
                <h6 class="text-cream mb-0">{{ item.title }}</h6>
                <p v-if="item.photo_date" class="gallery-date mb-0">{{ formatPhotoDate(item.photo_date) }}</p>
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
            <div class="lightbox-frame" :class="{ 'has-photo': photoSrc(activeItem) }">
              <img
                v-if="photoSrc(activeItem)"
                :src="photoSrc(activeItem)"
                :alt="activeItem.title"
              />
              <i v-else :class="`bi ${activeItem.icon || 'bi-image'}`"></i>
            </div>
            <div class="mt-4">
              <p class="text-gold small mb-1" style="letter-spacing: 0.2em; text-transform: uppercase;">
                {{ activeItem.category }}
              </p>
              <h4 class="text-cream mb-0">{{ activeItem.title }}</h4>
              <p v-if="activeItem.photo_date" class="small mb-0" style="color: var(--bms-gold);">
                {{ formatPhotoDate(activeItem.photo_date) }}
              </p>
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

<style scoped>
.gallery-item-inner img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.gallery-date {
  font-size: 0.7rem;
  color: var(--bms-muted);
  margin-top: 0.2rem;
}

.lightbox-frame.has-photo {
  aspect-ratio: auto;
  padding: 0;
  background: #000;
  overflow: hidden;
}

.lightbox-frame.has-photo img {
  width: 100%;
  max-height: 70vh;
  object-fit: contain;
  display: block;
}
</style>
