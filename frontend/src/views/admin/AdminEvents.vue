<script setup>
/**
 * AdminEvents — manage choir events (list, filter, create, edit, delete).
 */
import { ref, reactive, computed, onMounted } from 'vue'
import { useAdminStore } from '../../stores/admin'
import { useAuthStore } from '../../stores/auth'
import AdminModal from '../../components/admin/AdminModal.vue'
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue'

const admin = useAdminStore()
const auth  = useAuthStore()

const statusFilter = ref('all')
const search       = ref('')
const showForm     = ref(false)
const editingId    = ref(null)
const saving       = ref(false)
const deleting     = ref(false)

const confirmState = reactive({ show: false, id: null, title: '' })

const emptyForm = () => ({
  title: '', date: '', time: '', venue: '', city: '', description: '', status: 'upcoming',
})

const form        = reactive(emptyForm())
const fieldErrors = reactive({})
const apiError    = ref('')

// Per-filter counts for the filter bar
const filterCounts = computed(() => ({
  all:      admin.events.length,
  upcoming: admin.events.filter(e => e.status === 'upcoming').length,
  past:     admin.events.filter(e => e.status === 'past').length,
}))

const filteredEvents = computed(() => {
  let list = [...admin.events].sort((a, b) => new Date(b.date) - new Date(a.date))
  if (statusFilter.value !== 'all') list = list.filter(e => e.status === statusFilter.value)
  const q = search.value.trim().toLowerCase()
  if (q) list = list.filter(e =>
    [e.title, e.venue, e.city, e.description].some(v => String(v).toLowerCase().includes(q))
  )
  return list
})

function openCreate() {
  editingId.value = null
  Object.assign(form, emptyForm())
  clearErrors()
  showForm.value = true
}

function openEdit(event) {
  editingId.value = event.id
  Object.assign(form, {
    title: event.title,
    date: event.date?.slice(0, 10) || '',
    time: event.time?.slice(0, 5) || '',
    venue: event.venue,
    city: event.city,
    description: event.description,
    status: event.status,
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
    if (editingId.value === null) await admin.createEvent({ ...form })
    else await admin.updateEvent(editingId.value, { ...form })
    showForm.value = false
  } catch (err) {
    if (err.errors && Object.keys(err.errors).length) Object.assign(fieldErrors, err.errors)
    else apiError.value = err.message || 'Save failed. Please try again.'
  } finally {
    saving.value = false
  }
}

function askDelete(event) {
  confirmState.id    = event.id
  confirmState.title = event.title
  confirmState.show  = true
}

async function handleDelete() {
  deleting.value = true
  try {
    await admin.deleteEvent(confirmState.id)
    confirmState.show = false
  } catch (err) {
    apiError.value = err.message || 'Delete failed.'
    confirmState.show = false
  } finally {
    deleting.value = false
  }
}

const formatDate = (d) =>
  new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })

onMounted(async () => {
  if (!auth.isAdmin) return
  try { await admin.fetchEvents() } catch { /* handled by admin.error */ }
})
</script>

<template>
  <div>
    <!-- ── Header ────────────────────────────────────────────── -->
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
      <div>
        <p class="admin-kicker mb-1">Management</p>
        <h1 class="admin-title mb-0">Events</h1>
      </div>
      <button class="btn btn-gold btn-sm-admin" @click="openCreate">
        <i class="bi bi-plus-lg me-2"></i>Add Event
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

    <!-- ── Filter + search toolbar ──────────────────────────── -->
    <div class="events-toolbar mb-4">
      <!-- Status filters -->
      <div class="events-filter-group" role="group" aria-label="Filter events by status">
        <button
          v-for="f in ['all', 'upcoming', 'past']"
          :key="f"
          class="events-filter-btn"
          :class="{ active: statusFilter === f }"
          :aria-pressed="statusFilter === f"
          @click="statusFilter = f"
        >
          <span class="filter-label">{{ f.charAt(0).toUpperCase() + f.slice(1) }}</span>
          <span class="filter-count">{{ filterCounts[f] }}</span>
        </button>
      </div>

      <!-- Search -->
      <div class="admin-search ms-auto">
        <i class="bi bi-search"></i>
        <input
          v-model="search"
          type="search"
          class="form-control lux"
          placeholder="Search events…"
          aria-label="Search events"
        />
      </div>
    </div>

    <!-- ── Table ─────────────────────────────────────────────── -->
    <div class="elegant-card admin-table-wrap" v-reveal>

      <!-- Loading skeleton -->
      <div v-if="admin.loading && !admin.events.length" class="p-4">
        <div v-for="n in 4" :key="n" class="table-skeleton-row">
          <div class="flex-grow-1">
            <div class="skeleton-line skeleton-title mb-1"></div>
            <div class="skeleton-line skeleton-sub" style="width:55%"></div>
          </div>
          <div class="skeleton-line" style="width:90px;height:0.8rem;flex-shrink:0;"></div>
          <div class="skeleton-line" style="width:80px;height:0.8rem;flex-shrink:0;"></div>
          <div class="skeleton-line skeleton-badge flex-shrink-0"></div>
          <div class="d-flex gap-2">
            <div class="skeleton-btn"></div>
            <div class="skeleton-btn"></div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="!filteredEvents.length" class="admin-empty-state">
        <div class="admin-empty-icon">
          <i class="bi bi-calendar-x"></i>
        </div>
        <p class="text-cream mb-1">
          {{ search ? 'No events match your search.' : `No ${statusFilter === 'all' ? '' : statusFilter + ' '}events yet.` }}
        </p>
        <p class="text-muted small mb-3">{{ search ? 'Try a different keyword or filter.' : 'Add your first event to get started.' }}</p>
        <button v-if="!search" class="btn btn-gold btn-sm-admin" @click="openCreate">
          <i class="bi bi-plus-lg me-2"></i>Add Event
        </button>
      </div>

      <!-- Table -->
      <div v-else class="table-responsive">
        <table class="table admin-table align-middle mb-0">
          <thead>
            <tr>
              <th>Event</th>
              <th class="d-none d-md-table-cell">Date & Time</th>
              <th class="d-none d-lg-table-cell">Location</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="event in filteredEvents" :key="event.id">
              <td style="min-width:200px;">
                <div class="text-cream fw-bold small">{{ event.title }}</div>
                <div class="text-muted text-truncate d-md-none" style="max-width:200px;font-size:0.72rem;">
                  {{ formatDate(event.date) }}
                </div>
                <div class="text-muted text-truncate" style="max-width:280px;font-size:0.72rem;">
                  {{ event.description }}
                </div>
              </td>
              <td class="d-none d-md-table-cell" style="white-space:nowrap;">
                <div class="small text-cream">{{ formatDate(event.date) }}</div>
                <div class="text-muted" style="font-size:0.72rem;">
                  <i class="bi bi-clock me-1"></i>{{ event.time.slice(0, 5) }} WIB
                </div>
              </td>
              <td class="d-none d-lg-table-cell">
                <div class="small text-cream">{{ event.venue }}</div>
                <div class="text-muted" style="font-size:0.72rem;">
                  <i class="bi bi-geo-alt me-1"></i>{{ event.city }}
                </div>
              </td>
              <td>
                <span :class="event.status === 'upcoming' ? 'badge-gold' : 'badge-muted'">
                  {{ event.status === 'upcoming' ? 'Upcoming' : 'Past' }}
                </span>
              </td>
              <td>
                <div class="d-flex justify-content-end gap-2">
                  <button class="btn-action" aria-label="Edit event" title="Edit" @click="openEdit(event)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action danger" aria-label="Delete event" title="Delete" @click="askDelete(event)">
                    <i class="bi bi-trash3"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Create / Edit modal ───────────────────────────────── -->
    <AdminModal
      :show="showForm"
      :title="editingId === null ? 'Add Event' : 'Edit Event'"
      icon="bi-calendar-event"
      wide
      @close="showForm = false"
    >
      <form @submit.prevent="handleSubmit" novalidate>
        <div v-if="apiError" class="admin-alert mb-3" role="alert">
          <i class="bi bi-exclamation-circle me-2"></i>{{ apiError }}
        </div>

        <div class="row g-3">
          <div class="col-12">
            <label for="event-title" class="form-label-lux">Event Title</label>
            <input
              id="event-title" v-model="form.title" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.title }"
              placeholder="e.g. Harmony of the Archipelago" required
            />
            <p v-if="fieldErrors.title" class="field-error mt-1">{{ fieldErrors.title }}</p>
          </div>
          <div class="col-md-4">
            <label for="event-date" class="form-label-lux">Date</label>
            <input
              id="event-date" v-model="form.date" type="date"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.date }" required
            />
            <p v-if="fieldErrors.date" class="field-error mt-1">{{ fieldErrors.date }}</p>
          </div>
          <div class="col-md-4">
            <label for="event-time" class="form-label-lux">Time (WIB)</label>
            <input
              id="event-time" v-model="form.time" type="time"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.time }" required
            />
            <p v-if="fieldErrors.time" class="field-error mt-1">{{ fieldErrors.time }}</p>
          </div>
          <div class="col-md-4">
            <label for="event-status" class="form-label-lux">Status</label>
            <select
              id="event-status" v-model="form.status"
              class="form-select lux" :class="{ 'border-danger-lux': fieldErrors.status }"
            >
              <option value="upcoming">Upcoming</option>
              <option value="past">Past</option>
            </select>
            <p v-if="fieldErrors.status" class="field-error mt-1">{{ fieldErrors.status }}</p>
          </div>
          <div class="col-md-6">
            <label for="event-venue" class="form-label-lux">Venue</label>
            <input
              id="event-venue" v-model="form.venue" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.venue }"
              placeholder="e.g. Aula Simfonia Jakarta" required
            />
            <p v-if="fieldErrors.venue" class="field-error mt-1">{{ fieldErrors.venue }}</p>
          </div>
          <div class="col-md-6">
            <label for="event-city" class="form-label-lux">City</label>
            <input
              id="event-city" v-model="form.city" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.city }"
              placeholder="e.g. Jakarta" required
            />
            <p v-if="fieldErrors.city" class="field-error mt-1">{{ fieldErrors.city }}</p>
          </div>
          <div class="col-12">
            <label for="event-description" class="form-label-lux">Description</label>
            <textarea
              id="event-description" v-model="form.description"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.description }"
              rows="3" placeholder="A short description of this performance…" required
            ></textarea>
            <p v-if="fieldErrors.description" class="field-error mt-1">{{ fieldErrors.description }}</p>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4">
          <button type="button" class="btn btn-outline-gold btn-sm-admin" @click="showForm = false">Cancel</button>
          <button type="submit" class="btn btn-gold btn-sm-admin" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            {{ editingId === null ? 'Add Event' : 'Save Changes' }}
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
/* ── Events toolbar ─────────────────────────────────────────── */
.events-toolbar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.events-filter-group {
  display: flex;
  gap: 0.35rem;
  flex-wrap: wrap;
}

.events-filter-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.45rem 1rem;
  border-radius: 6px;
  border: 1px solid rgba(201, 169, 97, 0.25);
  background: transparent;
  color: var(--bms-muted-strong);
  font-size: 0.72rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  cursor: pointer;
  transition: all 0.25s ease;
}
.events-filter-btn:hover {
  border-color: rgba(201, 169, 97, 0.5);
  color: var(--bms-cream);
  background: rgba(201, 169, 97, 0.06);
}
.events-filter-btn.active {
  background: linear-gradient(135deg, rgba(201, 169, 97, 0.18), rgba(201, 169, 97, 0.06));
  border-color: rgba(201, 169, 97, 0.45);
  color: var(--bms-gold);
}

.filter-count {
  min-width: 20px;
  height: 18px;
  border-radius: 10px;
  background: rgba(201, 169, 97, 0.12);
  border: 1px solid rgba(201, 169, 97, 0.2);
  color: var(--bms-gold);
  font-size: 0.62rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0 0.35rem;
  transition: background 0.25s ease;
}
.events-filter-btn.active .filter-count {
  background: rgba(201, 169, 97, 0.25);
}

/* Skeleton loaders */
.table-skeleton-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid rgba(201, 169, 97, 0.06);
}
.table-skeleton-row:last-child { border-bottom: none; }
.skeleton-line {
  border-radius: 4px;
  background: linear-gradient(90deg,
    rgba(201,169,97,0.06) 25%, rgba(201,169,97,0.12) 50%, rgba(201,169,97,0.06) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.6s ease-in-out infinite;
}
.skeleton-title { height: 0.75rem; width: 55%; }
.skeleton-sub   { height: 0.6rem; }
.skeleton-badge { height: 1.4rem; width: 60px; border-radius: 2px; }
.skeleton-btn   { width: 34px; height: 34px; border-radius: 6px; background: rgba(201,169,97,0.07); flex-shrink: 0; }

@keyframes skeleton-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
