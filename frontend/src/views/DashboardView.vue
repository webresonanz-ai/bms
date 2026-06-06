<template>
    <div class="dashboard">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-2">
                    <i class="bi bi-music-note-list me-2"></i>
                    Dashboard Overview
                </h2>
                <p class="text-white-50">Welcome back, {{ user.name }}! Here's your choir's overview.</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-people-fill text-white"></i>
                        </div>
                        <span class="badge bg-success">+12%</span>
                    </div>
                    <h3 class="stat-value">{{ activeMembersCount }}</h3>
                    <p class="text-white-50 mb-0">Active Members</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-check-fill text-white"></i>
                        </div>
                        <span class="badge bg-info">{{ upcomingEventsCount }}</span>
                    </div>
                    <h3 class="stat-value">{{ upcomingEventsCount }}</h3>
                    <p class="text-white-50 mb-0">Upcoming Events</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-mic-fill text-white"></i>
                        </div>
                        <span class="badge bg-warning">Active</span>
                    </div>
                    <h3 class="stat-value">{{ totalPerformances }}</h3>
                    <p class="text-white-50 mb-0">Total Performances</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-star-fill text-white"></i>
                        </div>
                        <span class="badge bg-primary">Rating</span>
                    </div>
                    <h3 class="stat-value">4.9</h3>
                    <p class="text-white-50 mb-0">Overall Rating</p>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="luxury-card">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-graph-up me-2"></i>
                        Performance Analytics
                    </h5>
                    <div class="chart-placeholder">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center text-white-50">
                                <i class="bi bi-bar-chart-fill display-4 mb-3"></i>
                                <p>Performance chart will be displayed here</p>
                                <p class="small">Monthly performance statistics</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="luxury-card">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-clock-history me-2"></i>
                        Recent Activities
                    </h5>
                    <div class="activity-list">
                        <div v-for="(activity, index) in recentActivities" :key="index" class="activity-item">
                            <div class="activity-dot"></div>
                            <div class="ms-3">
                                <p class="mb-0 fw-semibold">{{ activity.title }}</p>
                                <small class="text-white-50">{{ activity.time }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events Quick View -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="luxury-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-calendar-event me-2"></i>
                            Upcoming Events
                        </h5>
                        <router-link to="/events" class="btn btn-gradient btn-sm">
                            View All
                            <i class="bi bi-arrow-right ms-2"></i>
                        </router-link>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Participants</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="event in upcomingEvents.slice(0, 3)" :key="event.id">
                                    <td>{{ event.title }}</td>
                                    <td>{{ formatDate(event.date) }}</td>
                                    <td>{{ event.location }}</td>
                                    <td>{{ event.participants }}</td>
                                    <td>
                                        <span class="badge bg-success">Upcoming</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useMembersStore } from '../stores/members'
import { useEventsStore } from '../stores/events'

const authStore = useAuthStore()
const membersStore = useMembersStore()
const eventsStore = useEventsStore()

const user = computed(() => authStore.user)
const activeMembersCount = computed(() => membersStore.activeMembers.length)
const upcomingEvents = computed(() => eventsStore.upcomingEvents)
const upcomingEventsCount = computed(() => upcomingEvents.value.length)
const totalPerformances = computed(() =>
    membersStore.members.reduce((sum, member) => sum + member.performances, 0)
)

const recentActivities = [
    { title: 'New member joined: David Wilson', time: '2 hours ago' },
    { title: 'Rehearsal completed', time: '5 hours ago' },
    { title: 'Event updated: Annual Concert', time: '1 day ago' },
    { title: 'New performance added', time: '2 days ago' }
]

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}
</script>

<style scoped>
.chart-placeholder {
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    border: 2px dashed rgba(255, 255, 255, 0.1);
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-dot {
    width: 8px;
    height: 8px;
    background: var(--primary-gradient);
    border-radius: 50%;
}

.table-dark {
    --bs-table-bg: transparent;
    --bs-table-color: var(--text-primary);
}

.table-dark thead th {
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    color: var(--text-secondary);
}
</style>