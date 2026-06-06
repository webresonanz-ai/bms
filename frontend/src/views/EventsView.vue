<template>
    <div class="events-page">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2">
                            <i class="bi bi-calendar-event-fill me-2"></i>
                            Events Calendar
                        </h2>
                        <p class="text-white-50">Manage and view all choir events</p>
                    </div>
                    <button class="btn btn-gradient" @click="showAddModal = true">
                        <i class="bi bi-plus-lg me-2"></i>
                        Add Event
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Calendar View -->
            <div class="col-lg-8">
                <div class="luxury-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">{{ currentMonthName }} {{ currentYear }}</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-light" @click="previousMonth">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-light" @click="currentMonth = new Date().getMonth()">
                                Today
                            </button>
                            <button class="btn btn-sm btn-outline-light" @click="nextMonth">
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
                                :class="{ 'other-month': !day.currentMonth, 'today': day.isToday, 'has-event': day.hasEvent }">
                                <div class="day-number">{{ day.date }}</div>
                                <div v-if="day.hasEvent" class="event-indicator"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events List -->
            <div class="col-lg-4">
                <div class="luxury-card">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-list-ul me-2"></i>
                        Upcoming Events
                    </h5>
                    <div class="event-list">
                        <div v-for="event in eventsStore.upcomingEvents" :key="event.id" class="event-item">
                            <div class="event-date">
                                <span class="date">{{ formatDay(event.date) }}</span>
                                <span class="month">{{ formatMonth(event.date) }}</span>
                            </div>
                            <div class="event-details ms-3">
                                <h6 class="mb-1">{{ event.title }}</h6>
                                <p class="mb-0 text-white-50 small">
                                    <i class="bi bi-geo-alt me-1"></i>{{ event.location }}
                                </p>
                                <p class="mb-0 text-white-50 small">
                                    <i class="bi bi-clock me-1"></i>{{ event.time }}
                                </p>
                            </div>
                            <div class="d-flex gap-1 ms-auto">
                                <button class="btn btn-sm btn-outline-secondary" @click="openEditModal(event)" title="Edit event">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" @click="openParticipantsModal(event)" title="Add participants">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" @click="deleteEvent(event.id)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                        <button type="button" class="btn btn-outline-light"
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
                            <input type="text" class="form-control border-start-0" 
                                v-model="memberSearchQuery" placeholder="Search by name or nickname..." 
                                style="border-radius: 0 8px 8px 0;">
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
                <button type="submit" class="btn btn-gradient flex-grow-1" :disabled="selectedMemberIds.length === 0">
                    Add {{ selectedMemberIds.length }} Participant(s)
                </button>
                <button type="button" class="btn btn-outline-light"
                    @click="closeParticipantsModal">Cancel</button>
            </div>
        </form>
    </div>
</div>

         <!-- Edit Event Modal -->
         <div v-if="showEditModal" class="modal-overlay">
             <div class="modal-content luxury-card">
                 <h4 class="fw-bold mb-4">Edit Event</h4>
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
                         <button type="button" class="btn btn-outline-light"
                             @click="closeEditModal">Cancel</button>
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

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
const months = ['January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December']

const currentMonthName = computed(() => months[currentMonth.value])

const calendarDays = computed(() => {
    const days = []
    const firstDay = new Date(currentYear.value, currentMonth.value, 1)
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0)
    const startPadding = firstDay.getDay()

    // Previous month padding
    for (let i = startPadding - 1; i >= 0; i--) {
        const date = new Date(currentYear.value, currentMonth.value, -i)
        days.push({
            date: date.getDate(),
            currentMonth: false,
            isToday: false,
            hasEvent: false
        })
    }

    // Current month
    const today = new Date()
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const date = new Date(currentYear.value, currentMonth.value, i)
        const dateString = date.toISOString().split('T')[0]
        days.push({
            date: i,
            currentMonth: true,
            isToday: date.toDateString() === today.toDateString(),
            hasEvent: eventsStore.events.some(e => e.date === dateString)
        })
    }

    // Next month padding
    const endPadding = 42 - days.length
    for (let i = 1; i <= endPadding; i++) {
        days.push({
            date: i,
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
}

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0
        currentYear.value++
    } else {
        currentMonth.value++
    }
}

function formatDay(date) {
    return new Date(date).getDate()
}

function formatMonth(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short' })
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
.calendar-grid {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    overflow: hidden;
}

.calendar-headers,
.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.calendar-headers .calendar-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.calendar-header {
    padding: 0.75rem;
    text-align: center;
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.875rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.calendar-day {
    padding: 0.5rem;
    min-height: 80px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.05);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.calendar-day:hover {
    background: rgba(102, 126, 234, 0.1);
}

.calendar-day.other-month {
    opacity: 0.3;
}

.calendar-day.today {
    background: rgba(102, 126, 234, 0.2);
}

.calendar-day.today .day-number {
    background: var(--primary-gradient);
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.day-number {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.event-indicator {
    width: 8px;
    height: 8px;
    background: var(--primary-gradient);
    border-radius: 50%;
    margin: 0 auto;
}

.event-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.event-item:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateX(5px);
}

.event-date {
    text-align: center;
    min-width: 50px;
}

.event-date .date {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
}

.event-date .month {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: var(--text-secondary);
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
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.form-control,
.form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 8px;
}

.form-control:focus,
.form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-label {
    color: var(--text-secondary);
    font-weight: 500;
}

.form-check-input {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
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
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--text-secondary);
}

.input-group .form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
}

.input-group .form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>