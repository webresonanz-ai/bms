<template>
    <div class="events-page">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2 luxury-heading">
                            <i class="bi bi-calendar-event-fill me-2 text-gold"></i>
                            Events Calendar
                        </h2>
                        <p class="luxury-subtitle">Manage and view all choir events</p>
                    </div>
                    <button class="btn btn-gradient luxury-btn" @click="showAddModal = true">
                        <i class="bi bi-plus-lg me-2"></i>
                        Add Event
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Calendar View -->
            <div class="col-lg-8">
                <div class="luxury-card calendar-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 luxury-subheading">{{ currentMonthName }} {{ currentYear }}</h5>
                        <div class="btn-group luxury-group">
                            <button class="btn btn-sm btn-outline-earth" @click="previousMonth">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-earth" @click="goToToday">
                                Today
                            </button>
                            <button class="btn btn-sm btn-outline-earth" @click="nextMonth">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="calendar-grid">
                        <div class="calendar-headers">
                            <div class="calendar-header" v-for="day in daysOfWeek" :key="day">
                                {{ day }}
                            </div>
                        </div>
                        <div class="calendar-days">
                            <div v-for="(day, index) in calendarDays" :key="index" class="calendar-day"
                                :class="{ 'other-month': !day.currentMonth, 'today': day.isToday, 'has-event': day.hasEvent }"
                                @click="openDayDetails(day)">
                                <div class="day-number">{{ day.date }}</div>
                                <div v-if="day.hasEvent" class="event-indicator"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events List -->
            <div class="col-lg-4">
                <div class="luxury-card events-list-card">
                    <h5 class="fw-bold mb-4 luxury-subheading">
                        <i class="bi bi-list-ul me-2 text-gold"></i>
                        Upcoming Events
                    </h5>
                    <div class="event-list">
                        <div v-for="event in eventsStore.upcomingEvents" :key="event.id"
                            class="event-item luxury-event-item" :class="`event-type-${event.type}`">
                            <div class="event-date luxury-date-badge">
                                <span class="date">{{ formatDay(event.date) }}</span>
                                <span class="month">{{ formatMonth(event.date) }}</span>
                            </div>
                            <div class="event-details ms-3">
                                <h6 class="mb-1 luxury-event-title">{{ event.title }}</h6>
                                <p class="mb-0 luxury-text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ event.location }}
                                </p>
                                <p class="mb-0 luxury-text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ event.time }}
                                </p>
                            </div>
                            <div class="d-flex gap-1 ms-auto luxury-actions">
                                <button class="btn btn-sm btn-earth-outline" @click="openEditModal(event)"
                                    title="Edit event">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-gold-outline" @click="openParticipantsModal(event)"
                                    title="Add participants">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                                <button class="btn btn-sm btn-danger-earth" @click="deleteEvent(event.id)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="showCalendarPopup" class="calendar-day-popup-overlay" @click.self="closeDayDetails">
                <div class="calendar-day-popup">
                    <div class="calendar-day-popup-header">
                        <div>
                            <p class="popup-date-title">Events on {{ formatFullDate(selectedCalendarDate) }}</p>
                            <p class="popup-date-subtitle">Click outside to close.</p>
                        </div>
                        <button type="button" class="btn-close" @click="closeDayDetails"
                            aria-label="Close"></button>
                    </div>
                    <div class="popup-events">
                        <div v-for="event in selectedCalendarEvents" :key="event.id" class="popup-event-item">
                            <div class="popup-event-header">
                                <h6 class="popup-event-title">{{ event.title }}</h6>
                                <span class="badge bg-earth text-white">{{ event.type }}</span>
                            </div>
                            <p class="mb-1 text-secondary"><i class="bi bi-geo-alt me-1"></i>{{ event.location }}
                            </p>
                            <p class="mb-0 text-secondary"><i class="bi bi-clock me-1"></i>{{ event.time }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Add Event Modal -->
        <div v-if="showAddModal" class="modal-overlay">
            <div class="modal-content luxury-card">
                <h4 class="fw-bold mb-4">Add New Event</h4>
                <form @submit.prevent="addNewEvent">
                    <div class="mb-3">
                        <label class="form-label">Event Title</label>
                        <input type="text" class="form-control" v-model="newEvent.title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" v-model="newEvent.date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time</label>
                        <input type="time" class="form-control" v-model="newEvent.time" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" v-model="newEvent.location" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" v-model="newEvent.type">
                            <option value="concert">Concert</option>
                            <option value="rehearsal">Rehearsal</option>
                            <option value="performance">Performance</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient flex-grow-1">Add Event</button>
                        <button type="button" class="btn btn-earth-outline"
                            @click="showAddModal = false">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Participants Modal -->
        <div v-if="showParticipantsModal" class="modal-overlay">
            <div class="modal-content luxury-card">
                <h4 class="fw-bold mb-4">Add Participants to "{{ selectedEvent?.title }}"</h4>
                <form @submit.prevent="addEventParticipants">
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col-7">
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-search text-white-50"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" v-model="memberSearchQuery"
                                        placeholder="Search by name or nickname..." style="border-radius: 0 8px 8px 0;">
                                </div>
                            </div>
                            <div class="col-5">
                                <select class="form-select" v-model="memberSectionFilter">
                                    <option v-for="section in memberSections" :key="section" :value="section">
                                        {{ section === 'all' ? 'All Sections' : section }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Members</label>
                        <div class="member-checkbox-list">
                            <div v-for="member in filteredMembers" :key="member.id" class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" :id="'member-' + member.id"
                                    :value="member.id" v-model="selectedMemberIds">
                                <label class="form-check-label d-flex align-items-center" :for="'member-' + member.id">
                                    <span class="me-2">
                                        <span v-if="member.nickname" class="text-primary">{{ member.nickname }}</span>
                                        <span v-else>{{ member.name }}</span>
                                        <span v-if="member.nickname" class="text-white-50"> ({{ member.name }})</span>
                                    </span>
                                    <span class="badge bg-secondary">{{ member.section }}</span>
                                </label>
                            </div>
                            <div v-if="filteredMembers.length === 0" class="text-center text-white-50 py-3">
                                No members found
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient flex-grow-1"
                            :disabled="selectedMemberIds.length === 0">
                            Add {{ selectedMemberIds.length }} Participant(s)
                        </button>
                        <button type="button" class="btn btn-earth-outline"
                            @click="closeParticipantsModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Event Modal -->
        <div v-if="showEditModal" class="modal-overlay">
            <div class="modal-content luxury-card">
                <h4 class="fw-bold mb-4 luxury-subheading">Edit Event</h4>
                <form @submit.prevent="updateEvent">
                    <div class="mb-3">
                        <label class="form-label">Event Title</label>
                        <input type="text" class="form-control" v-model="editingEvent.title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" v-model="editingEvent.date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time</label>
                        <input type="time" class="form-control" v-model="editingEvent.time" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" v-model="editingEvent.location" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" v-model="editingEvent.type">
                            <option value="concert">Concert</option>
                            <option value="rehearsal">Rehearsal</option>
                            <option value="performance">Performance</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient flex-grow-1">Save Changes</button>
                        <button type="button" class="btn btn-earth-outline" @click="closeEditModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useEventsStore } from '../stores/events'
import { useMembersStore } from '../stores/members'
import { api } from '../services/api'

const eventsStore = useEventsStore()
const membersStore = useMembersStore()

const showAddModal = ref(false)
const showEditModal = ref(false)
const showParticipantsModal = ref(false)
const selectedEvent = ref(null)
const selectedMemberIds = ref([])
const memberSearchQuery = ref('')
const memberSectionFilter = ref('all')

const editingEvent = ref({
    id: null,
    title: '',
    date: '',
    time: '',
    location: '',
    type: 'rehearsal'
})

const currentMonth = ref(new Date().getMonth())
const currentYear = ref(new Date().getFullYear())

onMounted(() => {
    eventsStore.fetchUpcomingEvents()
    membersStore.fetchActiveMembers()
})

const newEvent = ref({
    title: '',
    date: '',
    time: '',
    location: '',
    type: 'rehearsal',
    participants: 0
})

const selectedCalendarDate = ref('')

const selectedCalendarEvents = computed(() => {
    if (!selectedCalendarDate.value) return []
    return eventsStore.events.filter(event => toDateKey(event.date) === selectedCalendarDate.value)
})
const showCalendarPopup = computed(() => Boolean(selectedCalendarDate.value && selectedCalendarEvents.value.length > 0))

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
const months = ['January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December']

const currentMonthName = computed(() => months[currentMonth.value])

const calendarDays = computed(() => {
    const days = []
    const firstDay = new Date(currentYear.value, currentMonth.value, 1)
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0)
    const startPadding = firstDay.getDay()

    for (let i = startPadding - 1; i >= 0; i--) {
        const date = new Date(currentYear.value, currentMonth.value, -i)
        days.push({
            date: date.getDate(),
            dateKey: toDateKey(date),
            currentMonth: false,
            isToday: false,
            hasEvent: false
        })
    }

    const today = new Date()
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const date = new Date(currentYear.value, currentMonth.value, i)
        const dateKey = toDateKey(date)
        days.push({
            date: i,
            dateKey,
            currentMonth: true,
            isToday: date.toDateString() === today.toDateString(),
            hasEvent: eventsStore.events.some(e => toDateKey(e.date) === dateKey)
        })
    }

    const endPadding = 42 - days.length
    for (let i = 1; i <= endPadding; i++) {
        const date = new Date(currentYear.value, currentMonth.value + 1, i)
        days.push({
            date: i,
            dateKey: toDateKey(date),
            currentMonth: false,
            isToday: false,
            hasEvent: false
        })
    }

    return days
})

const memberSections = computed(() => {
    const sections = new Set()
    membersStore.members.forEach(m => {
        if (m.section) sections.add(m.section)
    })
    return ['all', ...Array.from(sections).sort()]
})

const filteredMembers = computed(() => {
    let members = membersStore.members

    if (memberSectionFilter.value !== 'all') {
        members = members.filter(m => m.section === memberSectionFilter.value)
    }

    if (memberSearchQuery.value) {
        const query = memberSearchQuery.value.toLowerCase()
        members = members.filter(m =>
            (m.name && m.name.toLowerCase().includes(query)) ||
            (m.nickname && m.nickname.toLowerCase().includes(query))
        )
    }

    return members
})

function previousMonth() {
    if (currentMonth.value === 0) {
        currentMonth.value = 11
        currentYear.value--
    } else {
        currentMonth.value--
    }
    closeDayDetails()
}

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0
        currentYear.value++
    } else {
        currentMonth.value++
    }
    closeDayDetails()
}

function goToToday() {
    const now = new Date()
    currentMonth.value = now.getMonth()
    currentYear.value = now.getFullYear()
    closeDayDetails()
}

function openDayDetails(day) {
    if (!day.hasEvent) return
    if (selectedCalendarDate.value === day.dateKey) {
        closeDayDetails()
    } else {
        selectedCalendarDate.value = day.dateKey
    }
}

function closeDayDetails() {
    selectedCalendarDate.value = ''
}

function pad(value) {
    return String(value).padStart(2, '0')
}

function formatFullDate(dateValue) {
    const date = getLocalDate(dateValue)
    if (!date || Number.isNaN(date.getTime())) return ''
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
}

function getLocalDate(dateValue) {
    if (!dateValue) return null

    if (dateValue instanceof Date) {
        return dateValue
    }

    if (typeof dateValue === 'string') {
        const datePart = dateValue.split('T')[0].split(' ')[0]
        if (/^\d{4}-\d{2}-\d{2}$/.test(datePart)) {
            const [year, month, day] = datePart.split('-').map(Number)
            return new Date(year, month - 1, day)
        }
    }

    return new Date(dateValue)
}

function toDateKey(dateValue) {
    const date = getLocalDate(dateValue)
    if (!date || Number.isNaN(date.getTime())) return ''
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`
}

function formatDay(date) {
    const localDate = getLocalDate(date)
    return localDate ? localDate.getDate() : ''
}

function formatMonth(date) {
    const localDate = getLocalDate(date)
    return localDate ? localDate.toLocaleDateString('en-US', { month: 'short' }) : ''
}

async function addNewEvent() {
    try {
        await eventsStore.addEvent({ ...newEvent.value })
        showAddModal.value = false
        newEvent.value = {
            title: '',
            date: '',
            time: '',
            location: '',
            type: 'rehearsal',
            participants: 0
        }
    } catch (error) {
        alert('Failed to add event: ' + error.message)
    }
}

async function deleteEvent(id) {
    if (confirm('Are you sure you want to delete this event?')) {
        try {
            await eventsStore.deleteEvent(id)
        } catch (error) {
            alert('Failed to delete event: ' + error.message)
        }
    }
}

async function openParticipantsModal(event) {
    selectedEvent.value = event
    selectedMemberIds.value = []
    showParticipantsModal.value = true
    await fetchExistingParticipants(event.id)
}

async function fetchExistingParticipants(eventId) {
    try {
        const response = await api.getEventParticipants(eventId)
        const existingIds = response.members?.map(m => m.member_id) || []
        selectedMemberIds.value = existingIds
    } catch (error) {
        console.error('Failed to fetch participants:', error)
    }
}

function closeParticipantsModal() {
    showParticipantsModal.value = false
    selectedEvent.value = null
    selectedMemberIds.value = []
    memberSearchQuery.value = ''
    memberSectionFilter.value = 'all'
}

async function addEventParticipants() {
    if (!selectedEvent.value) return

    try {
        await eventsStore.addParticipants(selectedEvent.value.id, selectedMemberIds.value)
        const event = eventsStore.events.find(e => e.id === selectedEvent.value.id)
        if (event) {
            event.participants = selectedMemberIds.value.length
        }
        closeParticipantsModal()
    } catch (error) {
        alert('Failed to add participants: ' + error.message)
    }
}

function openEditModal(event) {
    editingEvent.value = {
        id: event.id,
        title: event.title,
        date: event.date,
        time: event.time,
        location: event.location,
        type: event.type
    }
    showEditModal.value = true
}

function closeEditModal() {
    showEditModal.value = false
    editingEvent.value = {
        id: null,
        title: '',
        date: '',
        time: '',
        location: '',
        type: 'rehearsal'
    }
}

async function updateEvent() {
    try {
        await eventsStore.updateEvent(editingEvent.value.id, {
            title: editingEvent.value.title,
            date: editingEvent.value.date,
            time: editingEvent.value.time,
            location: editingEvent.value.location,
            type: editingEvent.value.type
        })
        closeEditModal()
    } catch (error) {
        alert('Failed to update event: ' + error.message)
    }
}
</script>

<style scoped>
.calendar-card {
    position: relative;
    overflow: visible;
}

.calendar-grid {
    background: rgba(166, 123, 91, 0.05);
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(166, 123, 91, 0.15);
}

.calendar-day-popup-overlay {
    position: fixed;
    inset: 0;
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(26, 18, 10, 0.7);
    backdrop-filter: blur(6px);
    padding: 1rem;
}

.calendar-day-popup {
    width: min(520px, 100%);
    background: #1a110a;
    border: 1px solid rgba(212, 175, 55, 0.35);
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(212, 175, 55, 0.1);
    max-height: 80vh;
    overflow-y: auto;
    color: #fff;
}

.calendar-day-popup-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.popup-date-title {
    margin: 0 0 0.25rem;
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
}

.popup-date-subtitle {
    margin: 0;
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.85rem;
}

.popup-events {
    display: grid;
    gap: 0.75rem;
}

.popup-event-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 0.95rem 1rem;
    color: #fff;
}

.popup-event-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.popup-event-title {
    margin: 0;
    font-size: 0.95rem;
    color: #fff;
}

.btn-close {
    border: none;
    background: transparent;
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    cursor: pointer;
}

.btn-close:hover {
    color: #fff;
}

.calendar-headers,
.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.calendar-headers .calendar-header {
    border-bottom: 1px solid rgba(166, 123, 91, 0.2);
}

.calendar-header {
    padding: 0.875rem;
    text-align: center;
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.875rem;
    border-bottom: 1px solid rgba(166, 123, 91, 0.15);
    background: rgba(166, 123, 91, 0.08);
}

.calendar-day {
    padding: 0.75rem 0.5rem;
    min-height: 90px;
    text-align: center;
    border: 1px solid rgba(166, 123, 91, 0.1);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    background: rgba(255, 255, 255, 0.02);
}

.calendar-day:hover {
    background: rgba(166, 123, 91, 0.15);
    transform: scale(1.03);
    z-index: 2;
}

.calendar-day.other-month {
    opacity: 0.4;
    background: transparent;
}

.calendar-day.today {
    background: rgba(166, 123, 91, 0.2);
    border: 1px solid rgba(212, 175, 55, 0.4);
}

.calendar-day.today .day-number {
    background: var(--primary-gradient);
    color: white;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(166, 123, 91, 0.3);
}

.day-number {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.event-indicator {
    width: 10px;
    height: 10px;
    background: var(--primary-gradient);
    border-radius: 50%;
    margin: 0 auto;
    box-shadow: 0 2px 8px rgba(166, 123, 91, 0.4);
}

.event-item {
    display: flex;
    align-items: center;
    padding: 1.25rem;
    border-radius: 16px;
    margin-bottom: 0.875rem;
    background: rgba(166, 123, 91, 0.05);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid rgba(166, 123, 91, 0.1);
}

.event-item:hover {
    background: rgba(166, 123, 91, 0.12);
    transform: translateX(8px);
}

.event-date {
    text-align: center;
    min-width: 56px;
}

.event-date .date {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.event-date .month {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: var(--text-secondary);
}

.luxury-date-badge {
    background: rgba(166, 123, 91, 0.1);
    border-radius: 12px;
    padding: 0.5rem;
}

.luxury-event-title {
    color: var(--text-primary);
    font-weight: 600;
}

.luxury-text-muted {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.luxury-actions .btn {
    border-radius: 10px;
    transition: all 0.2s ease;
}

.luxury-actions .btn:hover {
    transform: translateY(-2px);
}

.event-type-concert .luxury-date-badge {
    background: rgba(212, 175, 55, 0.15);
}

.event-type-rehearsal .luxury-date-badge {
    background: rgba(95, 115, 76, 0.15);
}

.event-type-performance .luxury-date-badge {
    background: rgba(166, 123, 91, 0.2);
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
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.form-control,
.form-select {
    background: rgba(166, 123, 91, 0.08);
    border: 1px solid rgba(166, 123, 91, 0.3);
    color: var(--text-primary);
    border-radius: 12px;
    padding: 0.875rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    background: rgba(166, 123, 91, 0.12);
    border-color: var(--primary-color);
    color: var(--text-primary);
    box-shadow: 0 0 0 0.25rem rgba(166, 123, 91, 0.25);
}

.form-label {
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-check-input {
    background-color: rgba(166, 123, 91, 0.1);
    border: 1px solid rgba(166, 123, 91, 0.3);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.form-check-label {
    color: var(--text-secondary);
    cursor: pointer;
}

.member-checkbox-list {
    max-height: 300px;
    overflow-y: auto;
    padding: 0.5rem;
}

.input-group-text {
    background: rgba(166, 123, 91, 0.08);
    border: 1px solid rgba(166, 123, 91, 0.3);
    color: var(--text-secondary);
    border-radius: 12px 0 0 12px;
}

.input-group .form-control {
    background: rgba(166, 123, 91, 0.08);
    border: 1px solid rgba(166, 123, 91, 0.3);
    color: var(--text-primary);
    border-radius: 0 12px 12px 0;
}

.input-group .form-control:focus {
    background: rgba(166, 123, 91, 0.12);
    border-color: var(--primary-color);
    color: var(--text-primary);
    box-shadow: 0 0 0 0.25rem rgba(166, 123, 91, 0.25);
}
</style>
