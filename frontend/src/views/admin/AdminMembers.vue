<script setup>
/**
 * AdminMembers — manage choir members (list, search, create, edit, delete).
 */
import { ref, reactive, computed, onMounted } from 'vue'
import { useAdminStore } from '../../stores/admin'
import { useAuthStore } from '../../stores/auth'
import AdminModal from '../../components/admin/AdminModal.vue'
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue'

const admin = useAdminStore()
const auth  = useAuthStore()

const search     = ref('')
const showForm   = ref(false)
const editingId  = ref(null)
const saving     = ref(false)
const deleting   = ref(false)

const confirmState = reactive({ show: false, id: null, name: '' })

const form = reactive({
  name: '', role: 'Sopran', section: 'Soprano',
  year_join: new Date().getFullYear(), nickname: '',
  email: '', stage_name: '', birth_place: '', birth_date: '',
  domicile: '', phone: '', field_of_work: '',
  join_date: '', status: 'active', performances: 0,
  avatar_url: 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg',
})
const fieldErrors = reactive({})
const apiError    = ref('')

const sections = ['Soprano', 'Alto', 'Tenor', 'Bass']

const sectionClass = {
  Soprano: 'section-soprano',
  Alto:    'section-alto',
  Tenor:   'section-tenor',
  Bass:    'section-bass',
}

const filteredMembers = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return admin.members
  return admin.members.filter(m =>
    [m.name, m.role, m.section, String(m.year_join)].some(v => String(v).toLowerCase().includes(q))
  )
})

// Group count by section for the summary pills
const sectionCounts = computed(() => {
  const counts = {}
  for (const s of sections) counts[s] = admin.members.filter(m => m.section === s).length
  return counts
})

function openCreate() {
  editingId.value = null
  Object.assign(form, { name: '', role: 'Sopran', section: 'Soprano',
    year_join: new Date().getFullYear(), nickname: '',
    email: '', stage_name: '', birth_place: '', birth_date: '',
    domicile: '', phone: '', field_of_work: '',
    join_date: '', status: 'active', performances: 0,
    avatar_url: 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg' })
  clearErrors()
  showForm.value = true
}

function openEdit(member) {
  editingId.value = member.id
  Object.assign(form, {
    name: member.name, role: member.role, section: member.section,
    year_join: member.year_join ? Number(member.year_join) : new Date().getFullYear(),
    nickname: member.nickname, email: member.email, stage_name: member.stage_name,
    birth_place: member.birth_place, birth_date: member.birth_date,
    domicile: member.domicile, phone: member.phone, field_of_work: member.field_of_work,
    join_date: member.join_date, status: member.status, performances: member.performances,
    avatar_url: member.avatar_url
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
    const payload = {
      name: form.name, role: form.role, section: form.section,
      year_join: Number(form.year_join), nickname: form.nickname,
      email: form.email, stage_name: form.stage_name, birth_place: form.birth_place,
      birth_date: form.birth_date, domicile: form.domicile, phone: form.phone,
      field_of_work: form.field_of_work, join_date: form.join_date,
      status: form.status, performances: Number(form.performances),
      avatar_url: form.avatar_url
    }
    if (editingId.value === null) {
      await admin.createMember(payload)
    } else {
      await admin.updateMember(editingId.value, payload)
    }
    showForm.value = false
  } catch (err) {
    if (err.errors && Object.keys(err.errors).length) Object.assign(fieldErrors, err.errors)
    else apiError.value = err.message || 'Save failed. Please try again.'
  } finally {
    saving.value = false
  }
}

function askDelete(member) {
  confirmState.id = member.id
  confirmState.name = member.name
  confirmState.show = true
}

async function handleDelete() {
  deleting.value = true
  try {
    await admin.deleteMember(confirmState.id)
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
  try { await admin.fetchMembers() } catch { /* handled by admin.error */ }
})
</script>

<template>
  <div>
    <!-- ── Header ────────────────────────────────────────────── -->
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
      <div>
        <p class="admin-kicker mb-1">Management</p>
        <h1 class="admin-title mb-0">Members</h1>
      </div>
      <button class="btn btn-gold btn-sm-admin" @click="openCreate">
        <i class="bi bi-plus-lg me-2"></i>Add Member
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

    <!-- Section summary pills -->
    <div v-if="admin.members.length" class="d-flex flex-wrap gap-2 mb-4" v-reveal>
      <div
        v-for="s in sections" :key="s"
        class="section-summary-pill"
        :class="sectionClass[s]"
      >
        <span class="section-summary-count">{{ sectionCounts[s] }}</span>
        {{ s }}
      </div>
    </div>

    <!-- Search + count -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
      <div class="admin-search">
        <i class="bi bi-search"></i>
        <input
          v-model="search"
          type="search"
          class="form-control lux"
          placeholder="Search by name, role, section…"
          aria-label="Search members"
        />
      </div>
      <span class="text-muted small">
        {{ filteredMembers.length }}<span class="text-gold"> / </span>{{ admin.members.length }} members
      </span>
    </div>

    <!-- ── Table ─────────────────────────────────────────────── -->
    <div class="elegant-card admin-table-wrap" v-reveal>

      <!-- Loading skeleton -->
      <div v-if="admin.loading && !admin.members.length" class="p-4">
        <div v-for="n in 5" :key="n" class="table-skeleton-row">
          <div class="skeleton-avatar-sm"></div>
          <div class="flex-grow-1">
            <div class="skeleton-line skeleton-title mb-1"></div>
            <div class="skeleton-line skeleton-sub" style="width:40%"></div>
          </div>
          <div class="skeleton-line" style="width:80px;height:0.8rem;"></div>
          <div class="skeleton-line skeleton-badge"></div>
          <div class="d-flex gap-2">
            <div class="skeleton-btn"></div>
            <div class="skeleton-btn"></div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="!filteredMembers.length" class="admin-empty-state">
        <div class="admin-empty-icon">
          <i class="bi bi-people"></i>
        </div>
        <p class="text-cream mb-1">{{ search ? 'No members match your search.' : 'No members yet.' }}</p>
        <p class="text-muted small mb-3">{{ search ? 'Try a different keyword.' : 'Get started by adding your first choir member.' }}</p>
        <button v-if="!search" class="btn btn-gold btn-sm-admin" @click="openCreate">
          <i class="bi bi-plus-lg me-2"></i>Add Member
        </button>
      </div>

      <!-- Table -->
      <div v-else class="table-responsive">
        <table class="table admin-table align-middle mb-0">
          <thead>
            <tr>
              <th>Member</th>
              <th class="d-none d-md-table-cell">Role</th>
              <th>Section</th>
              <th class="d-none d-sm-table-cell">Joined</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="member in filteredMembers" :key="member.id">
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="member-table-avatar" :class="sectionClass[member.section]">
                    {{ member.name.split(' ').map(w => w[0]).join('').slice(0,2) }}
                  </div>
                  <div>
                    <div class="text-cream fw-bold small">{{ member.name }}</div>
                    <div class="d-md-none text-muted" style="font-size:0.72rem;">{{ member.role }}</div>
                  </div>
                </div>
              </td>
              <td class="small text-muted d-none d-md-table-cell">{{ member.role }}</td>
              <td>
                <span class="badge-section" :class="sectionClass[member.section]">
                  {{ member.section }}
                </span>
              </td>
              <td class="small text-muted d-none d-sm-table-cell">{{ member.year_join }}</td>
              <td>
                <div class="d-flex justify-content-end gap-2">
                  <button class="btn-action" aria-label="Edit member" title="Edit" @click="openEdit(member)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action danger" aria-label="Delete member" title="Delete" @click="askDelete(member)">
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
      :title="editingId === null ? 'Add Member' : 'Edit Member'"
      icon="bi-person-plus"
      @close="showForm = false"
    >
      <form @submit.prevent="handleSubmit" novalidate>
        <div v-if="apiError" class="admin-alert mb-3" role="alert">
          <i class="bi bi-exclamation-circle me-2"></i>{{ apiError }}
        </div>

        <div class="row g-3">
          <div class="col-md-8">
            <label for="member-name" class="form-label-lux">Full Name</label>
            <input
              id="member-name" v-model="form.name" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.name }"
              placeholder="e.g. Aria Wijaya" required
            />
            <p v-if="fieldErrors.name" class="field-error mt-1">{{ fieldErrors.name }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-nickname" class="form-label-lux">Nickname</label>
            <input
              id="member-nickname" v-model="form.nickname" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.nickname }"
              placeholder="Aria" maxlength="50"
            />
            <p v-if="fieldErrors.nickname" class="field-error mt-1">{{ fieldErrors.nickname }}</p>
          </div>
          <div class="col-md-6">
            <label for="member-role" class="form-label-lux">Role / Voice Part</label>
            <input
              id="member-role" v-model="form.role" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.role }"
              placeholder="e.g. Lead Soprano" required
            />
            <p v-if="fieldErrors.role" class="field-error mt-1">{{ fieldErrors.role }}</p>
          </div>
          <div class="col-md-3">
            <label for="member-section" class="form-label-lux">Section</label>
            <select
              id="member-section" v-model="form.section"
              class="form-select lux" :class="{ 'border-danger-lux': fieldErrors.section }"
            >
              <option v-for="s in sections" :key="s" :value="s">{{ s }}</option>
            </select>
            <p v-if="fieldErrors.section" class="field-error mt-1">{{ fieldErrors.section }}</p>
          </div>
          <div class="col-md-3">
            <label for="member-year-join" class="form-label-lux">Year Joined</label>
            <input
              id="member-year-join" v-model.number="form.year_join" type="number"
              min="1990" :max="new Date().getFullYear()"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.year_join }"
              required
            />
            <p v-if="fieldErrors.year_join" class="field-error mt-1">{{ fieldErrors.year_join }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-email" class="form-label-lux">Email</label>
            <input
              id="member-email" v-model="form.email" type="email"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.email }"
              placeholder="email@domain.com" required
            />
            <p v-if="fieldErrors.email" class="field-error mt-1">{{ fieldErrors.email }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-stage-name" class="form-label-lux">Stage Name</label>
            <input
              id="member-stage-name" v-model="form.stage_name" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.stage_name }"
              placeholder="e.g. Aria"
            />
            <p v-if="fieldErrors.stage_name" class="field-error mt-1">{{ fieldErrors.stage_name }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-birth-place" class="form-label-lux">Birth Place</label>
            <input
              id="member-birth-place" v-model="form.birth_place" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.birth_place }"
              placeholder="Jakarta"
            />
            <p v-if="fieldErrors.birth_place" class="field-error mt-1">{{ fieldErrors.birth_place }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-birth-date" class="form-label-lux">Birth Date</label>
            <input
              id="member-birth-date" v-model="form.birth_date" type="date"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.birth_date }"
            />
            <p v-if="fieldErrors.birth_date" class="field-error mt-1">{{ fieldErrors.birth_date }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-domicile" class="form-label-lux">Domicile</label>
            <input
              id="member-domicile" v-model="form.domicile" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.domicile }"
              placeholder="Jakarta"
            />
            <p v-if="fieldErrors.domicile" class="field-error mt-1">{{ fieldErrors.domicile }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-phone" class="form-label-lux">Phone</label>
            <input
              id="member-phone" v-model="form.phone" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.phone }"
              placeholder="081234567890"
            />
            <p v-if="fieldErrors.phone" class="field-error mt-1">{{ fieldErrors.phone }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-field-of-work" class="form-label-lux">Field of Work</label>
            <input
              id="member-field-of-work" v-model="form.field_of_work" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.field_of_work }"
              placeholder="Performance"
            />
            <p v-if="fieldErrors.field_of_work" class="field-error mt-1">{{ fieldErrors.field_of_work }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-join-date" class="form-label-lux">Join Date</label>
            <input
              id="member-join-date" v-model="form.join_date" type="date"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.join_date }"
            />
            <p v-if="fieldErrors.join_date" class="field-error mt-1">{{ fieldErrors.join_date }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-status" class="form-label-lux">Status</label>
            <select
              id="member-status" v-model="form.status"
              class="form-select lux" :class="{ 'border-danger-lux': fieldErrors.status }"
            >
              <option value="active">Active</option>
              <option value="passive">Passive</option>
            </select>
            <p v-if="fieldErrors.status" class="field-error mt-1">{{ fieldErrors.status }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-performances" class="form-label-lux">Performances</label>
            <input
              id="member-performances" v-model.number="form.performances" type="number"
              min="0" class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.performances }"
              placeholder="0"
            />
            <p v-if="fieldErrors.performances" class="field-error mt-1">{{ fieldErrors.performances }}</p>
          </div>
          <div class="col-md-4">
            <label for="member-avatar-url" class="form-label-lux">Avatar URL</label>
            <input
              id="member-avatar-url" v-model="form.avatar_url" type="text"
              class="form-control lux" :class="{ 'border-danger-lux': fieldErrors.avatar_url }"
              value="https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg"
            />
            <p v-if="fieldErrors.avatar_url" class="field-error mt-1">{{ fieldErrors.avatar_url }}</p>
          </div>
        </div>

        <!-- Preview -->
        <div v-if="form.name" class="member-preview mt-3">
          <div class="member-preview-avatar" :class="sectionClass[form.section]">
            {{ form.name.split(' ').map(w => w[0]).join('').slice(0,2) }}
          </div>
          <div>
            <div class="text-cream small fw-bold">{{ form.name }}</div>
            <div class="text-muted" style="font-size:0.72rem;">
              {{ form.role || 'Role' }} ·
              <span :class="['badge-section', sectionClass[form.section]]">{{ form.section }}</span>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4">
          <button type="button" class="btn btn-outline-gold btn-sm-admin" @click="showForm = false">
            Cancel
          </button>
          <button type="submit" class="btn btn-gold btn-sm-admin" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            {{ editingId === null ? 'Add Member' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </AdminModal>

    <!-- ── Delete confirmation ───────────────────────────────── -->
    <ConfirmDialog
      :show="confirmState.show"
      :busy="deleting"
      :message="`Delete ${confirmState.name}? This action cannot be undone.`"
      @confirm="handleDelete"
      @cancel="confirmState.show = false"
    />
  </div>
</template>

<style scoped>
/* Section summary pills */
.section-summary-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.68rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.35rem 0.8rem;
  border-radius: 20px;
  font-weight: 500;
}
.section-summary-count {
  font-family: var(--font-display);
  font-size: 1rem;
  font-weight: 700;
  line-height: 1;
}

/* Section color variants (shared with Overview) */
.section-soprano { color:#e8a0b0; background:rgba(139,30,63,0.14); border:1px solid rgba(139,30,63,0.35); }
.section-alto    { color:#c9a961; background:rgba(201,169,97,0.12); border:1px solid rgba(201,169,97,0.30); }
.section-tenor   { color:#7ec8e3; background:rgba(126,200,227,0.12); border:1px solid rgba(126,200,227,0.28); }
.section-bass    { color:#9b8ec4; background:rgba(155,142,196,0.12); border:1px solid rgba(155,142,196,0.28); }

/* Avatar in table — color-coded ring */
.member-table-avatar {
  width: 40px;
  height: 40px;
  flex-shrink: 0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-size: 0.9rem;
  font-weight: 600;
  background: linear-gradient(135deg, var(--bms-gold), var(--bms-accent));
  color: var(--bms-cream);
  border: 2px solid rgba(201,169,97,0.35);
  transition: box-shadow 0.3s ease, transform 0.3s ease;
}
tr:hover .member-table-avatar {
  box-shadow: 0 0 0 3px rgba(201,169,97,0.25);
  transform: scale(1.08);
}
.member-table-avatar.section-soprano { background: linear-gradient(135deg,#8b1e3f,#6b1530); border-color:rgba(139,30,63,0.5); }
.member-table-avatar.section-alto    { background: linear-gradient(135deg,#c9a961,#a88840); border-color:rgba(201,169,97,0.5); color:var(--bms-darker); }
.member-table-avatar.section-tenor   { background: linear-gradient(135deg,#2a7a9b,#1a5a78); border-color:rgba(126,200,227,0.4); }
.member-table-avatar.section-bass    { background: linear-gradient(135deg,#4a3a7a,#342a5e); border-color:rgba(155,142,196,0.4); }

/* Badge section (inline span variant) */
.badge-section {
  display: inline-block;
  font-size: 0.62rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.3em 0.65em;
  border-radius: 2px;
  font-weight: 500;
}

/* Member preview in modal */
.member-preview {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.85rem 1rem;
  border-radius: 8px;
  background: rgba(201,169,97,0.04);
  border: 1px solid rgba(201,169,97,0.12);
}
.member-preview-avatar {
  width: 42px; height: 42px;
  flex-shrink: 0;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-display);
  font-size: 1rem; font-weight: 600;
  background: linear-gradient(135deg, var(--bms-gold), var(--bms-accent));
  color: var(--bms-cream);
  border: 2px solid rgba(201,169,97,0.4);
}
.member-preview-avatar.section-soprano { background:linear-gradient(135deg,#8b1e3f,#6b1530); border-color:rgba(139,30,63,0.5); }
.member-preview-avatar.section-alto    { background:linear-gradient(135deg,#c9a961,#a88840); border-color:rgba(201,169,97,0.5); color:var(--bms-darker); }
.member-preview-avatar.section-tenor   { background:linear-gradient(135deg,#2a7a9b,#1a5a78); border-color:rgba(126,200,227,0.4); }
.member-preview-avatar.section-bass    { background:linear-gradient(135deg,#4a3a7a,#342a5e); border-color:rgba(155,142,196,0.4); }

/* Skeleton loaders */
.table-skeleton-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.9rem 1.25rem;
  border-bottom: 1px solid rgba(201,169,97,0.06);
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
.skeleton-title { height: 0.75rem; width: 50%; }
.skeleton-sub   { height: 0.6rem; }
.skeleton-badge { height: 1.4rem; width: 60px; border-radius: 2px; flex-shrink: 0; }
.skeleton-btn   { width: 34px; height: 34px; border-radius: 6px; background: rgba(201,169,97,0.07); flex-shrink: 0; }
.skeleton-avatar-sm {
  width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(90deg,
    rgba(201,169,97,0.06) 25%, rgba(201,169,97,0.12) 50%, rgba(201,169,97,0.06) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.6s ease-in-out infinite;
}
@keyframes skeleton-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
