<script setup>
/**
 * AdminGallery — manage gallery items (grid, filter, create, edit, delete).
 */
import { ref, reactive, computed, onMounted } from 'vue'
import { useAdminStore } from '../../stores/admin'
import { useAuthStore } from '../../stores/auth'
import AdminModal from '../../components/admin/AdminModal.vue'
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue'

const admin = useAdminStore()
const auth  = useAuthStore()

const search    = ref('')
const showForm  = ref(false)
const editingId = ref(null)
const saving    = ref(false)
const deleting  = ref(false)

const confirmState = reactive({ show: false, id: null, title: '' })

const iconOptions = [
  'bi-image', 'bi-music-note-beamed', 'bi-people', 'bi-mic', 'bi-globe',
  'bi-heart', 'bi-star', 'bi-camera', 'bi-chat-quote', 'bi-music-note-list',
]

const emptyForm = () => ({ title: '', category: '', icon: 'bi-image', sort_order: 0 })

const form        = reactive(emptyForm())
const fieldErrors = reactive({})
const apiError    = ref('')

const filteredItems = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return admin.galleryItems
  return admin.galleryItems.filter(g =>
    [g.title, g.category].some(v => String(v).toLowerCase().includes(q))
  )
})

function openCreate() {
  editingId.value = null
  const nextSort = Math.max(0, ...admin.galleryItems.map(g => Number(g.sort_order) || 0)) + 1
  Object.assign(form, { ...emptyForm(), sort_order: nextSort })
  clearErrors()
  showForm.value = true
}

function openEdit(item) {
  editingId.value = item.id
  Object.assign(form, {
    title: item.title, category: item.category,
    icon: item.icon || 'bi-image', sort_order: Number(item.sort_order) || 0,
  })
  clearErrors()
  showForm.value = true
}

function clearErrors() {
  apiError.value = ''
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])
}

async function handleSubmit() {
  clearErrors()
  saving.value = true
  try {
    if (editingId.value === null) await admin.createGalleryItem({ ...form })
    else await admin.updateGalleryItem(editingId.value, { ...form })
    showForm.value = false
  } catch (err) {
    if (err.errors && Object.keys(err.errors).length) Object.assign(fieldErrors, err.errors)
    else apiError.value = err.message || 'Save failed. Please try again.'
  } finally {
    saving.value = false
  }
}

function askDelete(item) {
  confirmState.id    = item.id
  confirmState.title = item.title
  confirmState.show  = true
}

async function handleDelete() {
  deleting.value = true
  try {
    await admin.deleteGalleryItem(confirmState.id)
    confirmState.show = false
  } catch (err) {
    apiError.value = err.message || 'Delete failed.'
    confirmState.show = false
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  if (!auth.isAdmin) return
  try { await admin.fetchGallery() } catch { /* handled by admin.error */ }
})
</script>

<template>
  <div>
    <!-- ── Header ────────────────────────────────────────────── -->
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
      <div>
        <p class="admin-kicker mb-1">Management</p>
        <h1 class="admin-title mb-0">Gallery</h1>
      </div>
      <button class="btn btn-gold btn-sm-admin" @click="openCreate">
        <i class="bi bi-plus-lg me-2"></i>Add Item
      </button>
    </div>

    <div class="gold-divider-left mb-4"></div>

    <!-- Error banner -->
    <transition name="fade">
      <div v-if="apiError" class="admin-alert mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-2 flex-shrink-0"></i>
        <span>{{ apiError }}</span>
        <button type="button" class="btn-close btn-close-white ms-auto" aria-label="Dismiss" @click="apiError = ''"></button>
      </div>
    </transition>

    <!-- Search + count -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
      <div class="admin-search">
        <i class="bi bi-search"></i>
        <input
          v-model="search"
          type="search"
          class="form-control lux"
          placeholder="Search by title or category…"
          aria-label="Search gallery"
        />
      </div>
      <span class="text-muted small">
        {{ filteredItems.length }}<span class="text-gold"> / </span>{{ admin.galleryItems.length }} items
      </span>
    </div>

    <!-- ── Loading skeleton ──────────────────────────────────── -->
    <div v-if="admin.loading && !admin.galleryItems.length" class="row g-4">
      <div v-for="n in 8" :key="n" class="col-lg-3 col-md-4 col-6">
        <div class="gallery-skeleton-card">
          <div class="gallery-skeleton-thumb"></div>
          <div class="p-3">
            <div class="skeleton-line skeleton-sub mb-2" style="width:50%"></div>
            <div class="skeleton-line skeleton-title"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Empty state ───────────────────────────────────────── -->
    <div v-else-if="!filteredItems.length" class="elegant-card admin-empty-state">
      <div class="admin-empty-icon">
        <i class="bi bi-images"></i>
      </div>
      <p class="text-cream mb-1">{{ search ? 'No items match your search.' : 'No gallery items yet.' }}</p>
      <p class="text-muted small mb-3">{{ search ? 'Try a different keyword.' : 'Add your first gallery item.' }}</p>
      <button v-if="!search" class="btn btn-gold btn-sm-admin" @click="openCreate">
        <i class="bi bi-plus-lg me-2"></i>Add Item
      </button>
    </div>

    <!-- ── Grid ─────────────────────────────────────────────── -->
    <div v-else class="row g-4">
      <div
        v-for="item in filteredItems"
        :key="item.id"
        class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6"
      >
        <div class="gallery-admin-card" v-reveal>
          <!-- Thumbnail with hover overlay -->
          <div class="gallery-admin-thumb">
            <i class="bi" :class="item.icon"></i>
            <span class="sort-pill">#{{ item.sort_order }}</span>

            <!-- Hover action overlay -->
            <div class="gallery-card-overlay" aria-hidden="true">
              <button
                class="gallery-overlay-btn"
                title="Edit item"
                @click.stop="openEdit(item)"
              >
                <i class="bi bi-pencil"></i>
              </button>
              <button
                class="gallery-overlay-btn danger"
                title="Delete item"
                @click.stop="askDelete(item)"
              >
                <i class="bi bi-trash3"></i>
              </button>
            </div>
          </div>

          <!-- Card body -->
          <div class="gallery-card-body">
            <p class="gallery-card-category">{{ item.category }}</p>
            <h6 class="gallery-card-title">{{ item.title }}</h6>

            <!-- Visible action buttons for keyboard / small screens -->
            <div class="gallery-card-actions">
              <button
                class="btn-action"
                :aria-label="`Edit ${item.title}`"
                @click="openEdit(item)"
              >
                <i class="bi bi-pencil"></i>
              </button>
              <button
                class="btn-action danger"
                :aria-label="`Delete ${item.title}`"
                @click="askDelete(item)"
              >
                <i class="bi bi-trash3"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Create / Edit modal ───────────────────────────────── -->
    <AdminModal
      :show="showForm"
      :title="editingId === null ? 'Add Gallery Item' : 'Edit Gallery Item'"
      icon="bi-images"
      @close="showForm = false"
    >
      <form @submit.prevent="handleSubmit" novalidate>
        <div v-if="apiError" class="admin-alert mb-3" role="alert">
          <i class="bi bi-exclamation-circle me-2"></i>{{ apiError }}
        </div>

        <div class="row g-3">
          <div class="col-md-8">
            <label for="gallery-title" class="form-label-lux">Title</label>
            <input
              id="gallery-title" v-model="form.title" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.title }"
              placeholder="e.g. Concert Hall" required
            />
            <p v-if="fieldErrors.title" class="field-error mt-1">{{ fieldErrors.title }}</p>
          </div>
          <div class="col-md-4">
            <label for="gallery-sort" class="form-label-lux">Sort Order</label>
            <input
              id="gallery-sort" v-model.number="form.sort_order"
              type="number" min="0" class="form-control lux"
            />
          </div>
          <div class="col-12">
            <label for="gallery-category" class="form-label-lux">Category</label>
            <input
              id="gallery-category" v-model="form.category" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.category }"
              placeholder="e.g. Performance" required
            />
            <p v-if="fieldErrors.category" class="field-error mt-1">{{ fieldErrors.category }}</p>
          </div>
          <div class="col-12">
            <span class="form-label-lux d-block mb-2">Icon</span>
            <div class="icon-picker" role="radiogroup" aria-label="Choose an icon">
              <button
                v-for="icon in iconOptions" :key="icon"
                type="button"
                class="icon-option"
                :class="{ selected: form.icon === icon }"
                role="radio"
                :aria-checked="form.icon === icon"
                :aria-label="icon.replace('bi-', '').replaceAll('-', ' ')"
                @click="form.icon = icon"
              >
                <i class="bi" :class="icon"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4">
          <button type="button" class="btn btn-outline-gold btn-sm-admin" @click="showForm = false">Cancel</button>
          <button type="submit" class="btn btn-gold btn-sm-admin" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            {{ editingId === null ? 'Add Item' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </AdminModal>

    <!-- ── Delete confirmation ───────────────────────────────── -->
    <ConfirmDialog
      :show="confirmState.show"
      :busy="deleting"
      :message="`Delete &quot;${confirmState.title}&quot;? This action cannot be undone.`"
      @confirm="handleDelete"
      @cancel="confirmState.show = false"
    />
  </div>
</template>

<style scoped>
/* ── Gallery card ────────────────────────────────────────────── */
.gallery-admin-card {
  height: 100%;
  border-radius: 10px;
  overflow: hidden;
  background: linear-gradient(145deg, rgba(26,26,46,0.9), rgba(15,15,30,0.95));
  border: 1px solid rgba(201,169,97,0.14);
  transition: transform 0.4s var(--ease-lux), border-color 0.4s ease, box-shadow 0.4s ease;
}
.gallery-admin-card:hover {
  transform: translateY(-6px);
  border-color: rgba(201,169,97,0.4);
  box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

/* Thumbnail */
.gallery-admin-thumb {
  position: relative;
  aspect-ratio: 16/9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.8rem;
  color: rgba(201,169,97,0.35);
  background:
    radial-gradient(circle at 30% 30%, rgba(201,169,97,0.08), transparent 60%),
    linear-gradient(145deg, #1a1a2e, #0f0f1e);
  border-bottom: 1px solid rgba(201,169,97,0.1);
  overflow: hidden;
  transition: color 0.3s ease;
}
.gallery-admin-card:hover .gallery-admin-thumb { color: rgba(201,169,97,0.65); }

/* Hover overlay with actions */
.gallery-card-overlay {
  position: absolute;
  inset: 0;
  background: rgba(5,5,14,0.72);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.gallery-admin-card:hover .gallery-card-overlay { opacity: 1; }

.gallery-overlay-btn {
  width: 42px;
  height: 42px;
  border-radius: 8px;
  border: 1px solid rgba(201,169,97,0.4);
  background: rgba(15,15,30,0.8);
  color: var(--bms-gold);
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.25s ease;
  transform: translateY(6px);
  opacity: 0;
  transition: background 0.25s ease, transform 0.3s var(--ease-lux), opacity 0.3s ease;
}
.gallery-admin-card:hover .gallery-overlay-btn {
  opacity: 1;
  transform: translateY(0);
}
.gallery-admin-card:hover .gallery-overlay-btn:nth-child(2) {
  transition-delay: 0.05s;
}
.gallery-overlay-btn:hover { background: var(--bms-gold); color: var(--bms-darker); }
.gallery-overlay-btn.danger {
  color: #e8a0b0;
  border-color: rgba(139,30,63,0.5);
}
.gallery-overlay-btn.danger:hover { background: var(--bms-accent); color: var(--bms-cream); border-color: var(--bms-accent); }

/* Card body */
.gallery-card-body { padding: 0.85rem 1rem 1rem; }
.gallery-card-category {
  font-size: 0.62rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--bms-gold);
  margin-bottom: 0.25rem;
}
.gallery-card-title {
  color: var(--bms-cream);
  font-size: 0.88rem;
  font-weight: 500;
  margin-bottom: 0.65rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Visible actions row (accessible fallback) */
.gallery-card-actions {
  display: flex;
  gap: 0.5rem;
}

/* Sort pill */
.sort-pill {
  position: absolute;
  top: 0.55rem;
  right: 0.55rem;
  font-size: 0.6rem;
  letter-spacing: 0.15em;
  color: var(--bms-muted);
  background: rgba(15,15,30,0.8);
  border: 1px solid rgba(201,169,97,0.22);
  border-radius: 20px;
  padding: 0.15rem 0.5rem;
  z-index: 1;
}

/* Skeleton */
.gallery-skeleton-card {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid rgba(201,169,97,0.08);
  background: rgba(26,26,46,0.5);
}
.gallery-skeleton-thumb {
  aspect-ratio: 16/9;
  background: linear-gradient(90deg,
    rgba(201,169,97,0.05) 25%, rgba(201,169,97,0.1) 50%, rgba(201,169,97,0.05) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.6s ease-in-out infinite;
}
.skeleton-line {
  border-radius: 4px;
  background: linear-gradient(90deg,
    rgba(201,169,97,0.06) 25%, rgba(201,169,97,0.12) 50%, rgba(201,169,97,0.06) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.6s ease-in-out infinite;
}
.skeleton-title { height: 0.75rem; width: 75%; }
.skeleton-sub   { height: 0.6rem; }
@keyframes skeleton-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
