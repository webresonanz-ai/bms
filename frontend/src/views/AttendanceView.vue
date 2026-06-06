<template>
    <div class="attendance-page">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2">
                            <i class="bi bi-check2-circle me-2"></i>
                            Attendance Management
                        </h2>
                        <p class="text-white-50">Track member attendance for each event</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
                <div class="luxury-card">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Select Event</label>
                            <select class="form-select" v-model="selectedEventId" @change="onEventChange">
                                <option value="">-- Choose an event --</option>
                                <option v-for="event in eventsStore.events" :key="event.id" :value="event.id">
                                    {{ event.title }} &mdash; {{ formatDate(event.date) }} ({{ event.participants || 0 }} participants)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3" v-if="selectedEventId">
                            <button class="btn btn-gradient" @click="openTakeAttendanceModal">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Take Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1" v-if="selectedEventId">
            <div class="col-12">
                <div class="luxury-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-people me-2"></i>
                            Members - {{ selectedEvent?.title }}
                        </h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" style="width: auto; background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;" v-model="sectionFilter" @change="applyLocalFilters">
                                <option value="All">All Sections</option>
                                <option v-for="section in attendanceStore.getSections()" :key="section" :value="section">{{ section }}</option>
                            </select>
                            <select class="form-select form-select-sm" style="width: auto; background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;" v-model="statusFilter" @change="applyLocalFilters">
                                <option value="All">All Status</option>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Late">Late</option>
                                <option value="Excused">Excused</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Member</th>
                                    <th>Section</th>
                                    <th>Role</th>
                                    <th>Attendance</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(member, index) in attendanceStore.filteredParticipants" :key="member.id || member.member_id">
                                    <td>{{ index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img :src="member.avatar || defaultAvatar" class="avatar-sm me-2" alt="">
                                            <div>
                                                <div class="fw-semibold">{{ member.name }}</div>
                                                <small class="text-white-50">{{ member.email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ member.section || member.section_name || '-' }}</td>
                                    <td>{{ member.role || '-' }}</td>
                                    <td>
                                        <span class="badge" :class="attendanceBadgeClass(member)">
                                            {{ getAttendanceStatus(member) }}
                                        </span>
                                    </td>
                                    <td>{{ member.attendance?.notes || '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-light me-1" @click="openRecordModal(member)">
                                            <i class="bi bi-pencil"></i> Record
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!attendanceStore.filteredParticipants.length">
                                    <td colspan="7" class="text-center text-white-50 py-4">No members found for this event.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-white-50">Showing {{ attendanceStore.filteredParticipants.length }} of {{ attendanceStore.participantList.length }} members</small>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-light btn-sm" @click="attendanceStore.resetFilters()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                            </button>
                            <button class="btn btn-gradient btn-sm" @click="openTakeAttendanceModal">
                                <i class="bi bi-clipboard-check me-1"></i> Take Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1" v-if="!selectedEventId">
            <div class="col-12">
                <div class="luxury-card text-center py-5">
                    <i class="bi bi-calendar-event fs-1 text-white-50 mb-3"></i>
                    <p class="text-white-50 mb-0">Please select an event above to view and manage attendance records.</p>
                </div>
            </div>
        </div>

        <!-- Take Attendance Modal -->
        <div v-if="showTakeModal" class="modal-overlay" @click.self="closeTakeModal">
            <div class="modal-content luxury-card">
                <h4 class="fw-bold mb-2">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Take Attendance
                </h4>
                <p class="text-white-50 mb-4">Mark attendance for all members in <strong>{{ selectedEvent?.title }}</strong></p>

                <div class="mb-3">
                    <label class="form-label">Search Member</label>
                    <input type="text" class="form-control" v-model="searchMember" placeholder="Type member name...">
                </div>

                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Section</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="member in filteredTakeList" :key="member.id || member.member_id">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img :src="member.avatar || defaultAvatar" class="avatar-xs me-2" alt="">
                                        <span class="fw-semibold">{{ member.name }}</span>
                                    </div>
                                </td>
                                <td>{{ member.section || member.section_name || '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" :id="'present-'+member.id" :name="'status-'+member.id" value="Present" v-model="takeList[member.id || member.member_id]" autocomplete="off">
                                        <label class="btn btn-outline-success" :for="'present-'+member.id">P</label>

                                        <input type="radio" class="btn-check" :id="'absent-'+member.id" :name="'status-'+member.id" value="Absent" v-model="takeList[member.id || member.member_id]" autocomplete="off">
                                        <label class="btn btn-outline-danger" :for="'absent-'+member.id">A</label>

                                        <input type="radio" class="btn-check" :id="'late-'+member.id" :name="'status-'+member.id" value="Late" v-model="takeList[member.id || member.member_id]" autocomplete="off">
                                        <label class="btn btn-outline-warning" :for="'late-'+member.id">L</label>

                                        <input type="radio" class="btn-check" :id="'excused-'+member.id" :name="'status-'+member.id" value="Excused" v-model="takeList[member.id || member.member_id]" autocomplete="off">
                                        <label class="btn btn-outline-info" :for="'excused-'+member.id">E</label>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!filteredTakeList.length">
                                <td colspan="3" class="text-center text-white-50 py-3">No members match your search.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-gradient flex-grow-1" @click="submitTakeAttendance" :disabled="submitting">
                        <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                        Save Attendance
                    </button>
                    <button type="button" class="btn btn-outline-light" @click="closeTakeModal" :disabled="submitting">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Record Attendance Modal (per member) -->
        <div v-if="showRecordModal" class="modal-overlay" @click.self="closeRecordModal">
            <div class="modal-content luxury-card">
                <h4 class="fw-bold mb-3">
                    <i class="bi bi-pencil me-2"></i>
                    Record Attendance
                </h4>
                <div v-if="selectedMember" class="mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <img :src="selectedMember.avatar || defaultAvatar" class="avatar-sm me-3" alt="">
                        <div>
                            <div class="fw-semibold">{{ selectedMember.name }}</div>
                            <small class="text-white-50">{{ selectedMember.section || selectedMember.section_name || '' }} &middot; {{ selectedMember.role || '' }}</small>
                        </div>
                    </div>
                </div>
                <form @submit.prevent="saveMemberAttendance">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" v-model="recordForm.status" required>
                            <option value="">-- Select status --</option>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Late">Late</option>
                            <option value="Excused">Excused</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" v-model="recordForm.notes" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient flex-grow-1">Save</button>
                        <button type="button" class="btn btn-outline-light" @click="closeRecordModal" :disabled="submitting">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useEventsStore } from '../stores/events'
import { useAttendanceStore } from '../stores/attendance'
import { api } from '../services/api'

const eventsStore = useEventsStore()
const attendanceStore = useAttendanceStore()

const selectedEventId = ref('')
const showTakeModal = ref(false)
const showRecordModal = ref(false)
const searchMember = ref('')
const submitting = ref(false)

const sectionFilter = ref('All')
const statusFilter = ref('All')

const selectedMember = ref(null)
const recordForm = ref({
  status: '',
  notes: ''
})

const takeList = ref({})

const defaultAvatar = 'https://i.pravatar.cc/150?img=5'

const selectedEvent = computed(() => {
  return eventsStore.events.find(e => e.id === selectedEventId.value) || attendanceStore.currentEvent
})

const filteredTakeList = computed(() => {
  const list = attendanceStore.filteredParticipants
  if (!searchMember.value) return list
  const q = searchMember.value.toLowerCase()
  return list.filter(p => (p.name || '').toLowerCase().includes(q))
})

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

function attendanceBadgeClass(member) {
  const status = getAttendanceStatus(member)
  switch (status) {
    case 'Present':
      return 'bg-success'
    case 'Absent':
      return 'bg-danger'
    case 'Late':
      return 'bg-warning text-dark'
    case 'Excused':
      return 'bg-info text-dark'
    default:
      return 'bg-secondary'
  }
}

function getAttendanceStatus(member) {
  if (member.attendance && member.attendance.status) {
    return member.attendance.status
  }
  if (member.status === 'attended') return 'Present'
  if (member.status === 'absent') return 'Absent'
  return 'Not Recorded'
}

async function onEventChange() {
  sectionFilter.value = 'All'
  statusFilter.value = 'All'
  if (!selectedEventId.value) {
    attendanceStore.reset()
    return
  }
  try {
    await attendanceStore.fetchEventParticipants(selectedEventId.value)
  } catch (error) {
    alert('Failed to load event participants: ' + error.message)
  }
}

function applyLocalFilters() {
  attendanceStore.selectedSection = sectionFilter.value
  attendanceStore.selectedStatus = statusFilter.value
  attendanceStore.applyFilters()
}

function openTakeAttendanceModal() {
  if (!selectedEventId.value) return
  takeList.value = {}
  const list = attendanceStore.participantList
  list.forEach(p => {
    const id = p.id || p.member_id
    const status = p.attendance?.status
    if (status) {
      takeList.value[id] = status
    }
  })
  showTakeModal.value = true
}

function closeTakeModal() {
  showTakeModal.value = false
  searchMember.value = ''
}

async function submitTakeAttendance() {
  try {
    submitting.value = true
    const records = Object.entries(takeList.value)
      .filter(([_, status]) => !!status)
      .map(([memberId, status]) => ({
        member_id: parseInt(memberId),
        status,
        recorded_at: new Date().toISOString()
      }))

    if (!records.length) {
      alert('Please mark at least one member\'s attendance.')
      return
    }

    await attendanceStore.submitBulkAttendance(selectedEventId.value, records)
    closeTakeModal()
  } catch (error) {
    alert('Failed to submit attendance: ' + error.message)
  } finally {
    submitting.value = false
  }
}

function openRecordModal(member) {
  selectedMember.value = member
  recordForm.value = {
    status: member.attendance?.status || '',
    notes: member.attendance?.notes || ''
  }
  showRecordModal.value = true
}

function closeRecordModal() {
  showRecordModal.value = false
  selectedMember.value = null
  recordForm.value = {
    status: '',
    notes: ''
  }
}

async function saveMemberAttendance() {
  if (!selectedMember.value || !recordForm.value.status) return
  try {
    submitting.value = true
    const memberId = selectedMember.value.id || selectedMember.value.member_id
    await attendanceStore.recordAttendance(selectedEventId.value, memberId, {
      status: recordForm.value.status,
      notes: recordForm.value.notes,
      recorded_at: new Date().toISOString()
    })
    closeRecordModal()
  } catch (error) {
    alert('Failed to save attendance: ' + error.message)
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  eventsStore.fetchEvents()
})
</script>

<style scoped>
.attendance-page {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.luxury-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.5rem;
}

.form-select,
.form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 8px;
}

.form-select:focus,
.form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-select option {
    background: #1a1a2e;
    color: white;
}

.table-dark {
    --bs-table-bg: transparent;
}

.table-dark td,
.table-dark th {
    border-color: rgba(255, 255, 255, 0.1);
}

.avatar-sm {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(102, 126, 234, 0.5);
}

.avatar-xs {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid rgba(102, 126, 234, 0.5);
}

.badge {
    padding: 0.4rem 0.75rem;
    border-radius: 50px;
    font-weight: 500;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.modal-content {
    width: 90%;
    max-width: 650px;
    max-height: 85vh;
    overflow-y: auto;
}

.form-label {
    color: var(--text-secondary);
    font-weight: 500;
}

.text-secondary {
    color: var(--text-secondary) !important;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}
</style>
