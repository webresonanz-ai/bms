<script setup>
/**
 * AdminSettings — website customization (home hero background).
 * The hero photo is stored AS-IS (original quality, no compression).
 * Compression applies to gallery photos only.
 */
import { ref, computed, onMounted } from 'vue'
import { useAdminStore } from '../../stores/admin'
import { useAuthStore } from '../../stores/auth'
import { useChoirStore } from '../../stores/choir'
import { formatBytes, resolveUploadSrc, IMAGE_MAX_SIZE } from '../../utils/imageCompress'

const admin = useAdminStore()
const auth = useAuthStore()
const choir = useChoirStore()

const fileInput = ref(null)
const heroBg = ref('') // saved image_url (relative path or remote URL)
const uploading = ref(false)
const removing = ref(false)
const loading = ref(true)
const apiError = ref('')
const notice = ref('')

const heroPreview = computed(() => resolveUploadSrc(heroBg.value))

function clearMessages() {
  apiError.value = ''
  notice.value = ''
}

async function loadSettings() {
  loading.value = true
  clearMessages()
  try {
    await choir.fetchSettings()
    heroBg.value = choir.settings?.hero_background || ''
  } catch {
    apiError.value = 'Could not load website settings.'
  } finally {
    loading.value = false
  }
}

async function onFileChange(e) {
  const file = e.target.files?.[0]
  e.target.value = ''
  if (!file) return
  clearMessages()

  // Hero background is stored as-is — only validate size/type here
  if (file.size > IMAGE_MAX_SIZE) {
    apiError.value = `Image is too large (max ${formatBytes(IMAGE_MAX_SIZE)}).`
    return
  }

  uploading.value = true
  try {
    // 1) Upload the original file (backend stores it untouched)
    const uploaded = await admin.uploadFile(
      '/api/v1/settings/upload',
      file,
      file.name || 'hero-bg',
    )

    // 2) Point the hero background at the new file (old file is deleted server-side)
    const saved = await admin.saveSettings({ hero_background: uploaded.path })
    heroBg.value = saved?.hero_background || uploaded.path
    choir.settings = { ...choir.settings, hero_background: heroBg.value }

    notice.value = `Background updated — original quality kept (${formatBytes(file.size)}).`
  } catch (err) {
    apiError.value = err.message || 'Upload failed. Please try again.'
  } finally {
    uploading.value = false
  }
}

async function handleRemove() {
  if (!heroBg.value) return
  clearMessages()
  removing.value = true
  try {
    const saved = await admin.saveSettings({ hero_background: null })
    heroBg.value = saved?.hero_background || ''
    choir.settings = { ...choir.settings, hero_background: heroBg.value }
    notice.value = 'Background removed — the default gradient is showing.'
  } catch (err) {
    apiError.value = err.message || 'Remove failed. Please try again.'
  } finally {
    removing.value = false
  }
}

onMounted(async () => {
  if (!auth.isAdmin) return
  await loadSettings()
})
</script>

<template>
  <div>
    <!-- ── Header ────────────────────────────────────────────── -->
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
      <div>
        <p class="admin-kicker mb-1">Customization</p>
        <h1 class="admin-title mb-0">Website</h1>
      </div>
    </div>

    <div class="gold-divider-left mb-4"></div>

    <!-- Messages -->
    <transition name="fade">
      <div v-if="apiError" class="admin-alert mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-2 flex-shrink-0"></i>
        <span>{{ apiError }}</span>
        <button type="button" class="btn-close btn-close-white ms-auto" aria-label="Dismiss" @click="apiError = ''"></button>
      </div>
    </transition>
    <transition name="fade">
      <div v-if="notice" class="admin-notice mb-4" role="status">
        <i class="bi bi-check-circle me-2 flex-shrink-0"></i>
        <span>{{ notice }}</span>
        <button type="button" class="btn-close btn-close-white ms-auto" aria-label="Dismiss" @click="notice = ''"></button>
      </div>
    </transition>

    <!-- ── Hero background card ──────────────────────────────── -->
    <section class="elegant-card p-4 p-md-5 mb-4">
      <div class="d-flex align-items-center gap-3 mb-1">
        <span class="setting-icon"><i class="bi bi-image"></i></span>
        <div>
          <h2 class="text-cream mb-0" style="font-size: 1.1rem;">Home Hero Background</h2>
          <p class="text-muted small mb-0">Photo behind “Batavia Madrigal Singers” on the home page.</p>
        </div>
      </div>

      <div class="gold-divider-left my-4"></div>

      <!-- Loading -->
      <div v-if="loading" aria-hidden="true">
        <div class="hero-preview skeleton"></div>
        <p class="text-muted small mb-0 mt-3">Loading…</p>
      </div>

      <div v-else>
        <input
          ref="fileInput"
          type="file"
          accept="image/jpeg,image/png,image/gif,image/webp"
          class="d-none"
          aria-label="Choose a hero background photo"
          @change="onFileChange"
        />

        <!-- Preview (16:9 like the hero) -->
        <div class="hero-preview" :class="{ 'is-empty': !heroPreview }">
          <img v-if="heroPreview" :src="heroPreview" alt="Home hero background preview" />
          <div v-else class="hero-preview-empty">
            <i class="bi bi-card-image"></i>
            <p class="mb-0">No custom background — the default gradient is showing.</p>
          </div>
          <span v-if="uploading" class="hero-preview-busy">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Uploading…
          </span>
        </div>

        <div class="d-flex align-items-center gap-3 mt-3 flex-wrap">
          <button
            type="button"
            class="btn btn-gold btn-sm-admin"
            :disabled="uploading || removing"
            @click="fileInput?.click()"
          >
            <i class="bi bi-cloud-arrow-up me-2"></i>
            {{ heroBg ? 'Change Photo' : 'Upload Photo' }}
          </button>
          <button
            v-if="heroBg"
            type="button"
            class="btn btn-outline-gold btn-sm-admin"
            :disabled="uploading || removing"
            @click="handleRemove"
          >
            <span v-if="removing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Remove
          </button>
          <small class="text-muted">JPG · PNG · GIF · WebP — max 15 MB, original quality kept.</small>
        </div>

        <p class="text-muted small mt-3 mb-0">
          Tip: wide landscape photos (16:9 or wider) look best. Check the
          <RouterLink to="/" class="text-gold text-decoration-none">home page</RouterLink>
          after uploading.
        </p>
      </div>
    </section>
  </div>
</template>

<style scoped>
.setting-icon {
  width: 42px;
  height: 42px;
  flex-shrink: 0;
  border-radius: 10px;
  background: linear-gradient(135deg, rgba(201,169,97,0.2), rgba(201,169,97,0.06));
  border: 1px solid rgba(201,169,97,0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--bms-gold);
  font-size: 1.2rem;
}

.hero-preview {
  position: relative;
  aspect-ratio: 16/9;
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid rgba(201,169,97,0.2);
  background: linear-gradient(135deg, #0f0f1e 0%, #1a1a2e 50%, #0f0f1e 100%);
}
.hero-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.hero-preview.is-empty {
  border-style: dashed;
}
.hero-preview-empty {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 2rem;
  text-align: center;
  color: var(--bms-muted);
}
.hero-preview-empty i {
  font-size: 2.5rem;
  color: rgba(201,169,97,0.4);
}
.hero-preview-busy {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(5,5,14,0.65);
  color: var(--bms-cream);
  font-size: 0.85rem;
}
.hero-preview.skeleton {
  background: linear-gradient(90deg,
    rgba(201,169,97,0.05) 25%, rgba(201,169,97,0.1) 50%, rgba(201,169,97,0.05) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.6s ease-in-out infinite;
}
@keyframes skeleton-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

.admin-notice {
  display: flex;
  align-items: center;
  background: rgba(92, 200, 122, 0.1);
  border: 1px solid rgba(92, 200, 122, 0.4);
  border-radius: 4px;
  padding: 0.75rem 1rem;
  color: #a8d8b0;
  font-size: 0.85rem;
}
</style>
