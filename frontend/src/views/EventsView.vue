<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useChoirStore } from '../stores/choir'

const choirStore = useChoirStore()
const activeTab = ref('upcoming')

const filteredEvents = computed(() => {
  return choirStore.events
    .filter(e => e.status === activeTab.value)
    .sort((a, b) => {
      const da = new Date(a.date).getTime()
      const db = new Date(b.date).getTime()
      return activeTab.value === 'upcoming' ? da - db : db - da
    })
})

const nextEvent = computed(() => {
  return choirStore.events
    .filter(e => e.status === 'upcoming')
    .sort((a, b) => new Date(a.date) - new Date(b.date))[0]
})

const countdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 })
let timer = null

const updateCountdown = () => {
  if (!nextEvent.value) return
  const diff = new Date(nextEvent.value.date + 'T' + nextEvent.value.time).getTime() - Date.now()
  if (diff <= 0) {
    countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
    return
  }
  countdown.value = {
    days: Math.floor(diff / 86400000),
    hours: Math.floor(diff / 3600000) % 24,
    minutes: Math.floor(diff / 60000) % 60,
    seconds: Math.floor(diff / 1000) % 60
  }
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// DB stores TIME as HH:MM:SS — display as HH:MM
const formatTime = (timeStr) => String(timeStr ?? '').slice(0, 5)

onMounted(async () => {
  await choirStore.fetchEvents()
  updateCountdown()
  timer = setInterval(updateCountdown, 1000)
})

// Restart the countdown once live data arrives
watch(nextEvent, () => updateCountdown())

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <div>
    <div class="page-header">
      <div class="container">
        <p class="hero-subtitle mb-3 fade-in">Performances</p>
        <h1 class="hero-title fade-in-up">Events</h1>
        <div class="gold-divider"></div>
      </div>
    </div>

    <!-- Countdown to next concert -->
    <section class="pb-5 bg-darker-custom" v-if="nextEvent">
      <div class="container">
        <div class="elegant-card p-4 p-md-5" v-reveal>
          <div class="row align-items-center g-4">
            <div class="col-lg-5 text-center text-lg-start">
              <p class="text-gold text-uppercase mb-2" style="letter-spacing: 0.3em; font-size: 0.75rem;">
                Next Performance
              </p>
              <h3 class="text-cream mb-1">{{ nextEvent.title }}</h3>
              <p class="small text-muted mb-0">{{ formatDate(nextEvent.date) }} · {{ formatTime(nextEvent.time) }} WIB</p>
            </div>
            <div class="col-lg-7">
              <div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-md-3 flex-wrap">
                <div class="countdown-pill text-center" v-for="unit in ['days', 'hours', 'minutes', 'seconds']" :key="unit">
                  <div class="countdown-num">{{ String(countdown[unit]).padStart(2, '0') }}</div>
                  <div class="countdown-label">{{ unit }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section-padding bg-dark-custom" :class="{ 'pt-0': nextEvent }">
      <div class="container">
        <div class="d-flex justify-content-center gap-3 mb-5" v-reveal>
          <button
            @click="activeTab = 'upcoming'"
            class="btn"
            :class="activeTab === 'upcoming' ? 'btn-gold' : 'btn-outline-gold'"
            style="padding: 0.65rem 2rem; font-size: 0.78rem;"
            :aria-pressed="activeTab === 'upcoming'"
          >
            Upcoming
          </button>
          <button
            @click="activeTab = 'past'"
            class="btn"
            :class="activeTab === 'past' ? 'btn-gold' : 'btn-outline-gold'"
            style="padding: 0.65rem 2rem; font-size: 0.78rem;"
            :aria-pressed="activeTab === 'past'"
          >
            Past
          </button>
        </div>

        <!-- Loading state (live fetch from database) -->
        <div v-if="choirStore.eventsLoading" class="row justify-content-center">
          <div class="col-lg-9">
            <div v-for="n in 3" :key="n" class="elegant-card p-4 p-md-5 mb-4" aria-hidden="true">
              <p class="text-muted small mb-0">Loading events…</p>
            </div>
          </div>
        </div>

        <!-- Error state -->
        <div v-else-if="choirStore.eventsError" class="text-center py-5">
          <i class="bi bi-exclamation-circle text-gold" style="font-size: 3rem; opacity: 0.5;"></i>
          <p class="text-muted mt-3">{{ choirStore.eventsError }}</p>
          <button class="btn btn-outline-gold mt-2" @click="choirStore.fetchEvents()">
            Try again
          </button>
        </div>

        <div v-else class="row justify-content-center">
          <div class="col-lg-9">
            <div
              v-for="(event, index) in filteredEvents"
              :key="event.id"
              class="elegant-card p-4 p-md-5 mb-4"
              v-reveal="{ delay: index * 100 }"
            >
              <div class="row g-4 align-items-center">
                <div class="col-md-3 text-center text-md-start">
                  <div class="event-date-block">
                    <div class="text-gold fw-bold" style="font-size: 3rem; line-height: 1;">
                      {{ new Date(event.date).getDate() }}
                    </div>
                    <div class="text-cream text-uppercase" style="letter-spacing: 0.2em; font-size: 0.9rem;">
                      {{ new Date(event.date).toLocaleString('en', { month: 'short' }) }}
                    </div>
                    <div class="text-muted small">{{ new Date(event.date).getFullYear() }}</div>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <h4 class="text-cream mb-0">{{ event.title }}</h4>
                    <span :class="event.status === 'upcoming' ? 'badge-gold' : 'badge-muted'">
                      {{ event.status === 'upcoming' ? 'Upcoming' : 'Past' }}
                    </span>
                  </div>
                  <p class="small mb-3">{{ event.description }}</p>
                  <div class="d-flex flex-wrap gap-3 small text-muted">
                    <span><i class="bi bi-geo-alt text-gold me-2"></i>{{ event.venue }}, {{ event.city }}</span>
                    <span><i class="bi bi-clock text-gold me-2"></i>{{ formatTime(event.time) }} WIB</span>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="filteredEvents.length === 0" class="text-center py-5">
              <i class="bi bi-calendar-x text-gold" style="font-size: 3rem; opacity: 0.5;"></i>
              <p class="text-muted mt-3">No events to display.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
