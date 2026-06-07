<template>
    <div class="dashboard">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-2 luxury-heading">
                    <i class="bi bi-music-note-list me-2 text-gold"></i>
                    Dashboard Overview
                </h2>
                <p class="luxury-subtitle">Welcome back, {{ user?.name || 'Guest' }}! Here's your choir's overview.</p>
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
                        <span class="badge-earth">+12%</span>
                    </div>
                    <h3 class="stat-value">{{ activeMembersCount }}</h3>
                    <p class="luxury-text-muted mb-0">Active Members</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-check-fill text-white"></i>
                        </div>
                        <span class="badge-gold">{{ upcomingEventsCount }}</span>
                    </div>
                    <h3 class="stat-value">{{ upcomingEventsCount }}</h3>
                    <p class="luxury-text-muted mb-0">Upcoming Events</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-mic-fill text-white"></i>
                        </div>
                        <span class="badge-earth-outline">Active</span>
                    </div>
                    <h3 class="stat-value">{{ totalPerformances }}</h3>
                    <p class="luxury-text-muted mb-0">Total Performances</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-star-fill text-white"></i>
                        </div>
                        <span class="badge-gold">Rating</span>
                    </div>
                    <h3 class="stat-value">4.9</h3>
                    <p class="luxury-text-muted mb-0">Overall Rating</p>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="luxury-card chart-card">
                    <h5 class="fw-bold mb-4 luxury-subheading">
                        <i class="bi bi-graph-up me-2 text-gold"></i>
                        Performance Analytics
                    </h5>
                    <div class="chart-placeholder">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center luxury-text-muted">
                                <i class="bi bi-bar-chart-fill display-4 mb-3 text-gold"></i>
                                <p>Performance chart will be displayed here</p>
                                <p class="small">Monthly performance statistics</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="luxury-card activity-card">
                    <h5 class="fw-bold mb-4 luxury-subheading">
                        <i class="bi bi-clock-history me-2 text-gold"></i>
                        Recent Activities
                    </h5>
                    <div class="activity-list">
                        <div v-for="(activity, index) in recentActivities" :key="index" class="activity-item luxury-activity-item">
                            <div class="activity-dot"></div>
                            <div class="ms-3">
                                <p class="mb-0 fw-semibold luxury-event-title">{{ activity.title }}</p>
                                <small class="luxury-text-muted">{{ activity.time }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events Quick View -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="luxury-card events-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 luxury-subheading">
                            <i class="bi bi-calendar-event me-2 text-gold"></i>
                            Upcoming Events
                        </h5>
                        <router-link to="/events" class="btn btn-gradient btn-sm luxury-btn-sm">
                            View All
                            <i class="bi bi-arrow-right ms-2"></i>
                        </router-link>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover luxury-table">
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
                                <tr v-for="event in (upcomingEvents || []).slice(0, 3)" :key="event.id" class="luxury-table-row">
                                    <td>{{ event.title }}</td>
                                    <td>{{ formatDate(event.date) }}</td>
                                    <td>{{ event.location }}</td>
                                    <td>{{ event.participants }}</td>
                                    <td>
                                        <span class="badge-earth">Upcoming</span>
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
import { computed, onMounted } from 'vue'
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

onMounted(() => {
    eventsStore.fetchUpcomingEvents()
    membersStore.fetchActiveMembers()
})

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
    background: rgba(166, 123, 91, 0.05);
    border-radius: 16px;
    border: 2px dashed rgba(166, 123, 91, 0.2);
    backdrop-filter: blur(5px);
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(166, 123, 91, 0.15);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-dot {
    width: 8px;
    height: 8px;
    background: var(--primary-gradient);
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(166, 123, 91, 0.4);
}

.luxury-table {
    --bs-table-bg: transparent;
    --bs-table-color: var(--text-primary);
    --bs-table-border-color: rgba(166, 123, 91, 0.15);
}

.luxury-table thead th {
    border-bottom: 2px solid rgba(166, 123, 91, 0.2);
    color: var(--text-secondary);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
}

.luxury-table tbody tr {
    transition: all 0.3s ease;
}

.luxury-table tbody tr:hover {
    background: rgba(166, 123, 91, 0.1);
}

.luxury-btn-sm {
    padding: 0.5rem 1.25rem;
    font-size: 0.875rem;
}

.luxury-text-muted {
  color: var(--text-secondary);
}

.luxury-event-title {
  color: var(--text-primary);
}

@media (max-width: 576px) {
  .chart-placeholder {
    height: 200px;
    border-radius: 12px;
  }

  .chart-placeholder .display-4 {
    font-size: 2.5rem !important;
  }

  .activity-item {
    padding: 0.75rem 0;
  }

  .activity-dot {
    width: 6px;
    height: 6px;
  }

  .luxury-btn-sm {
    padding: 0.4rem 0.75rem;
    font-size: 0.75rem;
  }

  .luxury-table thead th {
    font-size: 0.7rem;
    padding: 0.5rem;
  }

  .luxury-table tbody td {
    padding: 0.5rem 0.25rem;
    font-size: 0.85rem;
  }

  .luxury-table .bi {
    font-size: 0.9rem;
  }
}

@media (max-width: 400px) {
  .chart-placeholder {
    height: 150px;
  }

  .chart-placeholder .display-4 {
    font-size: 2rem !important;
  }

  .luxury-btn-sm {
    padding: 0.3rem 0.5rem;
    font-size: 0.7rem;
  }

  .luxury-table thead th {
    font-size: 0.65rem;
    padding: 0.4rem;
  }

  .luxury-table tbody td {
    padding: 0.4rem 0.2rem;
    font-size: 0.8rem;
  }
}
</style>