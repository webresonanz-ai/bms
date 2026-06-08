<template>
    <div class="attendance-page">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2 luxury-heading">
                            <i class="bi bi-check2-circle me-2 text-gold"></i>
                            Attendance Management
                        </h2>
                        <p class="luxury-subtitle">Track member attendance for each event</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
                <div class="luxury-card attendance-card">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Select Event</label>
                            <select class="form-select" v-model="selectedEventId" @change="onEventChange">
                                <option value="">-- Choose an event --</option>
                                <option v-for="event in eventsStore.events" :key="event.id" :value="event.id">
                                    {{ event.title }} &mdash; {{ formatDate(event.date) }} ({{ event.participants || 0
                                    }} participants)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3" v-if="selectedEventId">
                            <button v-if="isMusicDirector || isSectionLeader" class="btn btn-gradient luxury-btn"
                                @click="openTakeAttendanceModal">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Take Attendance
                            </button>
                            <div v-else>
                                <button class="btn btn-gradient luxury-btn" disabled
                                    title="Requires Music Director or Section Leader">
                                    <i class="bi bi-clipboard-check me-2"></i>
                                    Take Attendance
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1" v-if="selectedEventId">
            <div class="col-12">
                <div class="luxury-card attendance-table-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                        <h5 class="fw-bold mb-0 luxury-subheading">
                            <i class="bi bi-people me-2 text-gold"></i>
                            Members - {{ selectedEvent?.title }}
                        </h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm"
                                style="width: auto; background: rgba(166,123,91,0.08); border-color: rgba(166,123,91,0.3); color: var(--text-primary);"
                                v-model="sectionFilter" @change="applyLocalFilters">
                                <option value="All">All Sections</option>
                                <option v-for="section in attendanceStore.getSections()" :key="section"
                                    :value="section">{{ section }}</option>
                            </select>
                            <input type="date" class="form-control form-control-sm"
                                style="width: auto; background: rgba(166,123,91,0.08); border-color: rgba(166,123,91,0.3); color: var(--text-primary); border-radius:12px; padding:0.375rem 0.75rem;"
                                v-model="viewDate" @change="applyLocalFilters" :max="todayDate" />
                        </div>
                    </div>

                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-dark table-hover align-middle luxury-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Member</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(member, index) in attendanceStore.filteredParticipants"
                                    :key="member.id || member.member_id">
                                    <td>{{ index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img :src="member.avatar || defaultAvatar" class="avatar-sm me-2" alt="">
                                            <div>
                                                <div class="fw-semibold luxury-event-title">{{
                                                    truncate(getDisplayName(member), 25)
                                                }}</div>
                                                <small class="luxury-text-muted">{{ truncate(member.phone, 35)
                                                }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ member.section || member.section_name || '-' }}</td>
                                    <td>
                                        <span
                                            :class="['badge', 'rounded-pill', getStatusBadgeClass(getMemberStatusForDate(member))]">{{
                                                getMemberStatusForDate(member) }}</span>
                                    </td>
                                </tr>
                                <tr v-if="!attendanceStore.filteredParticipants.length">
                                    <td colspan="4" class="text-center luxury-text-muted py-4">No members found for this
                                        event.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="luxury-text-muted">Showing {{ attendanceStore.filteredParticipants.length }} of {{
                            attendanceStore.participantList.length }} members</small>
                        <div class="d-flex gap-2">
                            <button class="btn btn-earth-outline btn-sm" @click="resetFilters">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                            </button>
                            <button v-if="isMusicDirector || isSectionLeader"
                                class="btn btn-gradient btn-sm luxury-btn-sm" @click="openTakeAttendanceModal">
                                <i class="bi bi-clipboard-check me-1"></i> Take Attendance
                            </button>
                            <button v-else class="btn btn-gradient btn-sm luxury-btn-sm" disabled
                                title="Requires Music Director or Section Leader">
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
                    <i class="bi bi-calendar-event fs-1 luxury-text-muted mb-3"></i>
                    <p class="luxury-text-muted mb-0">Please select an event above to view and manage attendance
                        records.</p>
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
                <p class="luxury-text-muted mb-4">Mark attendance for all members in <strong>{{ selectedEvent?.title
                }}</strong></p>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Attendance Date</label>
                        <input type="date" class="form-control" v-model="selectedAttendanceDate" :max="todayDate" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Search Member</label>
                    <input type="text" class="form-control" v-model="searchMember"
                        placeholder="Type nickname or member name...">
                </div>

                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto; overflow-x: auto;">
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
                                        <div>
                                            <span class="fw-semibold">{{ getDisplayName(member) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ member.section || member.section_name || '-' }}</td>
                                <td class="text-center">
                                    <div v-if="isPresentMarked(member)">
                                        <span class="badge rounded-pill bg-success">Present</span>
                                    </div>
                                    <div v-else class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox"
                                            :id="'present-checkbox-' + (member.id || member.member_id)"
                                            @change="markPresent(member)" />
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!filteredTakeList.length">
                                <td colspan="3" class="text-center luxury-text-muted py-3">No members match your search.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-gradient flex-grow-1" @click="submitTakeAttendance"
                        :disabled="submitting">
                        <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                        Save Attendance
                    </button>
                    <button type="button" class="btn btn-outline-light" @click="closeTakeModal"
                        :disabled="submitting">Cancel</button>
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
                            <div class="fw-semibold">{{ getDisplayName(selectedMember) }}</div>
                            <small class="luxury-text-muted">{{ selectedMember.section || selectedMember.section_name ||
                                '' }} &middot; {{ selectedMember.role || '' }}</small>
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
                        <textarea class="form-control" v-model="recordForm.notes" rows="3"
                            placeholder="Optional notes..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient flex-grow-1">Save</button>
                        <button type="button" class="btn btn-outline-light" @click="closeRecordModal"
                            :disabled="submitting">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useEventsStore } from '../stores/events'
import { useAttendanceStore } from '../stores/attendance'
import { useAuthStore } from '../stores/auth'

const eventsStore = useEventsStore()
const attendanceStore = useAttendanceStore()
const authStore = useAuthStore()

const selectedEventId = ref('')
const showTakeModal = ref(false)
const showRecordModal = ref(false)
const searchMember = ref('')
const submitting = ref(false)

const sectionFilter = ref('All')

const userRole = computed(() => authStore.user?.role || '')
const isMusicDirector = computed(() => userRole.value === 'Music Director')
const isSectionLeader = computed(() => userRole.value === 'Section Leader')

const selectedMember = ref(null)
const recordForm = ref({
    status: '',
    notes: ''
})

const takeList = ref({})
const selectedAttendanceDate = ref(formatDateInputValue(new Date()))
const todayDate = ref(formatDateInputValue(new Date()))
const viewDate = ref(formatDateInputValue(new Date()))

const defaultAvatar = 'https://media.istockphoto.com/id/2151669184/vector/vector-flat-illustration-in-grayscale-avatar-user-profile-person-icon-gender-neutral.jpg?s=612x612&w=0&k=20&c=UEa7oHoOL30ynvmJzSCIPrwwopJdfqzBs0q69ezQoM8='

const selectedEvent = computed(() => {
    return eventsStore.events.find(e => e.id === selectedEventId.value) || attendanceStore.currentEvent
})

const filteredTakeList = computed(() => {
    const list = attendanceStore.participantList
    if (!searchMember.value) return list
    const q = searchMember.value.toLowerCase()
    return list.filter(p => {
        const name = (p.stage_name || '').toLowerCase()
        const nickname = ((p.nickname || p.nick) || '').toLowerCase()
        return name.includes(q) || nickname.includes(q)
    })
})

function getDisplayName(member) {
    const name = member?.stage_name || ''
    const nickname = member?.nickname || member?.nick || ''
    if (nickname) return `${nickname} (${name})`
    return name || '-'
}

function truncate(value, max) {
    if (!value) return '-'
    const string = String(value)
    if (string.length <= max) return string
    return string.slice(0, max - 1) + '…'
}

function formatDate(date) {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

function formatTimestampForServer(date) {
    const pad = (value) => value.toString().padStart(2, '0')
    const d = date instanceof Date ? date : new Date(date)
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`
}

function formatDateInputValue(date) {
    const d = date instanceof Date ? date : new Date(date)
    const pad = (value) => value.toString().padStart(2, '0')
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
}

function getDateFromInput(value) {
    const [year, month, day] = (value || '').split('-').map(Number)
    if (!year || !month || !day) return null
    return new Date(year, month - 1, day, 0, 0, 0)
}

function isAttendanceOnDate(member, date) {
    const recordedAt = member.attendance?.recorded_at
    if (!recordedAt) return false
    const recorded = new Date(recordedAt)
    if (Number.isNaN(recorded.getTime())) return false

    const compareDate = date instanceof Date ? date : new Date(date)
    return recorded.getFullYear() === compareDate.getFullYear() && recorded.getMonth() === compareDate.getMonth() && recorded.getDate() === compareDate.getDate()
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
    viewDate.value = todayDate.value
    if (!selectedEventId.value) {
        attendanceStore.reset()
        return
    }
    try {
        await attendanceStore.fetchEventParticipants(selectedEventId.value)
        await attendanceStore.fetchAttendance(selectedEventId.value)
    } catch (error) {
        alert('Failed to load event participants: ' + error.message)
    }
}

function applyLocalFilters() {
    attendanceStore.selectedSection = sectionFilter.value
    attendanceStore.selectedStatus = 'All'
    attendanceStore.applyFilters()
}

function resetFilters() {
    attendanceStore.resetFilters()
    sectionFilter.value = 'All'
    viewDate.value = todayDate.value
}

function isRecordOnDate(record, date) {
    const recordedAt = record?.recorded_at
    if (!recordedAt) return false
    const recorded = new Date(recordedAt)
    if (Number.isNaN(recorded.getTime())) return false
    const compareDate = date instanceof Date ? date : new Date(date)
    return recorded.getFullYear() === compareDate.getFullYear() && recorded.getMonth() === compareDate.getMonth() && recorded.getDate() === compareDate.getDate()
}

function getMemberStatusForDate(member) {
    const selectedDate = getDateFromInput(viewDate.value)
    if (!selectedDate) return 'Absent'
    const id = member.member_id || member.id
    const found = attendanceStore.attendanceRecords.find(r => (r.member_id || r.id) === id && isRecordOnDate(r, selectedDate) && r.status === 'Present')
    return found ? 'Present' : 'Absent'
}

function getStatusBadgeClass(status) {
    return status === 'Present' ? 'bg-success' : 'bg-danger'
}

function openTakeAttendanceModal() {
    if (!(isMusicDirector.value || isSectionLeader.value)) {
        alert('Only the Music Director or Section Leaders can take attendance.')
        return
    }
    if (!selectedEventId.value) return
    selectedAttendanceDate.value = todayDate.value
    takeList.value = {}
    const selectedDate = getDateFromInput(selectedAttendanceDate.value)

    attendanceStore.participantList.forEach(p => {
        const id = p.id || p.member_id
        if (selectedDate && isAttendanceOnDate(p, selectedDate) && p.attendance?.status === 'Present') {
            takeList.value[id] = 'Present'
        }
    })
    showTakeModal.value = true
}

watch(selectedAttendanceDate, (newValue, oldValue) => {
    if (!showTakeModal.value) return
    const selectedDate = getDateFromInput(newValue)
    takeList.value = {}

    attendanceStore.participantList.forEach(p => {
        const id = p.id || p.member_id
        if (selectedDate && isAttendanceOnDate(p, selectedDate) && p.attendance?.status === 'Present') {
            takeList.value[id] = 'Present'
        }
    })
})

function closeTakeModal() {
    showTakeModal.value = false
    searchMember.value = ''
}

function isPresentMarked(member) {
    const id = member.id || member.member_id
    const selectedDate = getDateFromInput(selectedAttendanceDate.value)
    return takeList.value[id] === 'Present' || (member.attendance?.status === 'Present' && selectedDate && isAttendanceOnDate(member, selectedDate))
}

function isAttendanceToday(member) {
    const recordedAt = member.attendance?.recorded_at
    if (!recordedAt) return false
    const recorded = new Date(recordedAt)
    if (Number.isNaN(recorded.getTime())) return false
    const today = new Date()
    return recorded.getFullYear() === today.getFullYear() && recorded.getMonth() === today.getMonth() && recorded.getDate() === today.getDate()
}

function markPresent(member) {
    const id = member.id || member.member_id
    takeList.value = {
        ...takeList.value,
        [id]: 'Present'
    }
}

async function submitTakeAttendance() {
    if (!(isMusicDirector.value || isSectionLeader.value)) {
        alert('Only the Music Director or Section Leaders can submit attendance.')
        return
    }
    try {
        submitting.value = true
        const selectedDate = getDateFromInput(selectedAttendanceDate.value)
        if (!selectedDate) {
            alert('Please select a valid attendance date.')
            return
        }

        const today = getDateFromInput(todayDate.value)
        if (today && selectedDate > today) {
            alert('Attendance date cannot be in the future.')
            return
        }

        const records = Object.entries(takeList.value)
            .filter(([, status]) => status === 'Present')
            .map(([memberId, status]) => ({
                member_id: parseInt(memberId),
                status,
                recorded_at: formatTimestampForServer(selectedDate)
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
            recorded_at: formatTimestampForServer(new Date())
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
    animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
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
    background: var(--card-gradient);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(166, 123, 91, 0.2);
    border-radius: 24px;
    padding: 1.75rem;
}

.form-select,
.form-control {
    background: rgba(166, 123, 91, 0.08);
    border: 1px solid rgba(166, 123, 91, 0.3);
    color: var(--text-primary);
    border-radius: 12px;
    padding: 0.875rem 1rem;
    transition: all 0.3s ease;
}

.form-select:focus,
.form-control:focus {
    background: rgba(166, 123, 91, 0.12);
    border-color: var(--primary-color);
    color: var(--text-primary);
    box-shadow: 0 0 0 0.25rem rgba(166, 123, 91, 0.25);
}

.form-select option {
    background: var(--card-bg);
    color: var(--text-primary);
}

.form-label {
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.table-dark {
    --bs-table-bg: transparent;
}

.table-dark td,
.table-dark th {
    border-color: rgba(166, 123, 91, 0.15);
}

.luxury-table tbody tr {
    transition: all 0.3s ease;
}

.luxury-table tbody tr:hover {
    background: rgba(166, 123, 91, 0.08);
}

.avatar-sm {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
    border: 2px solid var(--primary-color);
}

.avatar-xs {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
    border: 1px solid var(--primary-color);
}

.luxury-badge {
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
    background: rgba(26, 18, 10, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(8px);
}

.modal-content {
    width: 90%;
    max-width: 650px;
    max-height: 85vh;
    overflow-y: auto;
}

.text-secondary {
    color: var(--text-secondary) !important;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.luxury-event-title {
    color: var(--text-primary);
}

.luxury-subtitle {
    color: var(--text-secondary);
}

.luxury-heading {
    color: var(--text-primary);
}

.luxury-subheading {
    color: var(--text-primary);
}

@media (max-width: 576px) {
    .luxury-card {
        padding: 1rem;
    }

    .attendance-card .row.g-3 {
        --bs-gutter-x: 0.75rem;
        --bs-gutter-y: 0.75rem;
    }

    .btn-gradient.luxury-btn {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
    }

    .avatar-xs {
        width: 22px;
        height: 22px;
    }

    .table-dark td,
    .table-dark th {
        padding: 0.4rem 0.2rem;
        font-size: 0.8rem;
    }

    .luxury-table tbody tr:hover {
        transform: none;
    }

    .modal-content {
        max-width: 95vw;
    }
}

@media (max-width: 400px) {
    .luxury-card {
        padding: 0.75rem;
    }

    .attendance-card .row.g-3 {
        --bs-gutter-x: 0.5rem;
        --bs-gutter-y: 0.5rem;
    }

    .btn-gradient.luxury-btn {
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
    }

    .table-dark td,
    .table-dark th {
        padding: 0.3rem 0.1rem;
        font-size: 0.75rem;
    }

    .avatar-sm {
        width: 28px;
        height: 28px;
    }

    .avatar-xs {
        width: 20px;
        height: 20px;
    }
}
</style>
