<script setup>
/**
 * AdminOverview — dashboard landing page with content stats
 * and a glance at the latest records.
 */
import { ref, computed, onMounted } from 'vue'
import { useAdminStore } from '../../stores/admin'
import { useAuthStore } from '../../stores/auth'

const admin = useAdminStore()
const auth  = useAuthStore()

const loaded = ref(false)

const memberCount   = computed(() => admin.members.length)
const eventCount    = computed(() => admin.events.length)
const upcomingCount = computed(() => admin.events.filter(e => e.status === 'upcoming').length)
const galleryCount  = computed(() => admin.galleryItems.length)

const upcomingEvents = computed(() =>
  admin.events
    .filter(e => e.status === 'upcoming')
    .sort((a, b) => new Date(a.date) - new Date(b.date))
    .slice(0, 4)
)

const recentMembers = computed(() =>
  [...admin.members].sort((a, b) => Number(b.year_join) - Number(a.year_join)).slice(0, 5)
)

const sectionColors = {
  Soprano: 'section-soprano',
  Alto:    'section-alto',
  Tenor:   'section-tenor',
  Bass:    'section-bass',
}

const formatDate = (dateStr) =>
  new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })

const greetingHour = new Date().getHours()
const greeting = greetingHour < 12 ? 'Good morning' : greetingHour < 18 ? 'Good afternoon' : 'Good evening'

onMounted(async () => {
  if (!auth.isAdmin) return
  try {
    await Promise.all([admin.fetchMembers(), admin.fetchEvents(), admin.fetchGallery()])
  } catch {
    /* errors surface via admin.error */
  } finally {
    loaded.value = true
  }
})
</script>

<template>
  <div>
    <!-- ── Page header ───────────────────────────────────────── -->
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
      <div>
        <p class="admin-kicker mb-1">Dashboard</p>
        <h1 class="admin-title mb-1">
          {{ greeting }},
          <span class="text-gold">{{ auth.user?.name?.split(' ')[0] || 'Admin' }}</span>
        </h1>
        <p class="text-muted mb-0" style="font-size:0.8rem;">
          Here's what's happening with Batavia Madrigal Singers today.
        </p>
      </div>
      <RouterLink to="/" class="btn btn-outline-gold btn-sm-admin align-self-start">
        <i class="bi bi-box-arrow-up-left me-2"></i>View Site
      </RouterLink>
    </div>

    <div class="gold-divider-left mb-5"></div>

    <!-- Error banner -->
    <transition name="fade">
      <div v-if="admin.error" class="admin-alert mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-2 flex-shrink-0"></i>
        <span>{{ admin.error }}</span>
        <button type="button" class="btn-close btn-close-white ms-auto" aria-label="Dismiss" @click="admin.clearError()"></button>
      </div>
    </transition>

    <!-- ── Stat cards ────────────────────────────────────────── -->
    <div class="row g-4 mb-5">
      <!-- Members -->
      <div class="col-md-6 col-xl-3">
        <RouterLink to="/admin/members" class="text-decoration-none d-block h-100">
          <div class="stat-card" v-reveal>
            <div class="stat-card-inner">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <span class="stat-trend up" v-if="!admin.loading">
                  <i class="bi bi-arrow-up-right"></i> Active
                </span>
              </div>
              <div v-if="admin.loading && !loaded" class="skeleton-line skeleton-num mb-2"></div>
              <div v-else class="stat-number-sm">{{ memberCount }}</div>
              <p class="stat-label">Total Members</p>
              <div class="stat-card-footer">
                <span>View all <i class="bi bi-arrow-right ms-1"></i></span>
              </div>
            </div>
          </div>
        </RouterLink>
      </div>

      <!-- Total Events -->
      <div class="col-md-6 col-xl-3">
        <RouterLink to="/admin/events" class="text-decoration-none d-block h-100">
          <div class="stat-card" v-reveal="{ delay: 80 }">
            <div class="stat-card-inner">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                <span class="stat-trend neutral" v-if="!admin.loading">
                  <i class="bi bi-calendar3"></i> Total
                </span>
              </div>
              <div v-if="admin.loading && !loaded" class="skeleton-line skeleton-num mb-2"></div>
              <div v-else class="stat-number-sm">{{ eventCount }}</div>
              <p class="stat-label">All Events</p>
              <div class="stat-card-footer">
                <span>View all <i class="bi bi-arrow-right ms-1"></i></span>
              </div>
            </div>
          </div>
        </RouterLink>
      </div>

      <!-- Upcoming Events -->
      <div class="col-md-6 col-xl-3">
        <RouterLink to="/admin/events" class="text-decoration-none d-block h-100">
          <div class="stat-card stat-card--accent" v-reveal="{ delay: 160 }">
            <div class="stat-card-inner">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-icon stat-icon--accent"><i class="bi bi-hourglass-split"></i></div>
                <span class="stat-trend up" v-if="!admin.loading && upcomingCount > 0">
                  <i class="bi bi-stars"></i> Live
                </span>
              </div>
              <div v-if="admin.loading && !loaded" class="skeleton-line skeleton-num mb-2"></div>
              <div v-else class="stat-number-sm text-gold">{{ upcomingCount }}</div>
              <p class="stat-label">Upcoming Events</p>
              <div class="stat-card-footer">
                <span>View upcoming <i class="bi bi-arrow-right ms-1"></i></span>
              </div>
            </div>
          </div>
        </RouterLink>
      </div>

      <!-- Gallery -->
      <div class="col-md-6 col-xl-3">
        <RouterLink to="/admin/gallery" class="text-decoration-none d-block h-100">
          <div class="stat-card" v-reveal="{ delay: 240 }">
            <div class="stat-card-inner">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-icon"><i class="bi bi-images"></i></div>
                <span class="stat-trend neutral" v-if="!admin.loading">
                  <i class="bi bi-grid-3x3-gap"></i> Items
                </span>
              </div>
              <div v-if="admin.loading && !loaded" class="skeleton-line skeleton-num mb-2"></div>
              <div v-else class="stat-number-sm">{{ galleryCount }}</div>
              <p class="stat-label">Gallery Items</p>
              <div class="stat-card-footer">
                <span>View all <i class="bi bi-arrow-right ms-1"></i></span>
              </div>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>

    <!-- ── Detail panels ─────────────────────────────────────── -->
    <div class="row g-4">

      <!-- Next Performances -->
      <div class="col-lg-6">
        <div class="elegant-card p-0 h-100 overflow-hidden" v-reveal>
          <div class="overview-panel-header">
            <div class="d-flex align-items-center gap-2">
              <div class="overview-panel-icon">
                <i class="bi bi-stars"></i>
              </div>
              <h5 class="text-cream mb-0">Next Performances</h5>
            </div>
            <RouterLink to="/admin/events" class="overview-panel-link">
              Manage <i class="bi bi-arrow-right ms-1"></i>
            </RouterLink>
          </div>

          <div class="overview-panel-body">
            <!-- Loading skeleton -->
            <template v-if="admin.loading && !loaded">
              <div v-for="n in 3" :key="n" class="overview-row">
                <div class="flex-grow-1">
                  <div class="skeleton-line skeleton-title mb-1"></div>
                  <div class="skeleton-line skeleton-sub"></div>
                </div>
                <div class="skeleton-line skeleton-badge"></div>
              </div>
            </template>

            <template v-else-if="upcomingEvents.length">
              <div
                v-for="event in upcomingEvents"
                :key="event.id"
                class="overview-row"
              >
                <div class="overview-row-date">
                  <div class="overview-date-month">
                    {{ new Date(event.date).toLocaleDateString('en-US', { month: 'short' }) }}
                  </div>
                  <div class="overview-date-day">
                    {{ new Date(event.date).getDate() }}
                  </div>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <div class="text-cream small fw-bold text-truncate">{{ event.title }}</div>
                  <div class="text-muted" style="font-size:0.75rem;">
                    {{ event.time.slice(0, 5) }} · {{ event.city }}
                  </div>
                </div>
                <span class="badge-gold flex-shrink-0">Upcoming</span>
              </div>
            </template>

            <div v-else class="overview-empty">
              <i class="bi bi-calendar-x"></i>
              <p>No upcoming events scheduled.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Newest Members -->
      <div class="col-lg-6">
        <div class="elegant-card p-0 h-100 overflow-hidden" v-reveal="{ delay: 100 }">
          <div class="overview-panel-header">
            <div class="d-flex align-items-center gap-2">
              <div class="overview-panel-icon">
                <i class="bi bi-person-plus"></i>
              </div>
              <h5 class="text-cream mb-0">Newest Members</h5>
            </div>
            <RouterLink to="/admin/members" class="overview-panel-link">
              Manage <i class="bi bi-arrow-right ms-1"></i>
            </RouterLink>
          </div>

          <div class="overview-panel-body">
            <!-- Loading skeleton -->
            <template v-if="admin.loading && !loaded">
              <div v-for="n in 4" :key="n" class="overview-row">
                <div class="skeleton-avatar"></div>
                <div class="flex-grow-1">
                  <div class="skeleton-line skeleton-title mb-1"></div>
                  <div class="skeleton-line skeleton-sub"></div>
                </div>
                <div class="skeleton-line skeleton-badge"></div>
              </div>
            </template>

            <template v-else-if="recentMembers.length">
              <div
                v-for="member in recentMembers"
                :key="member.id"
                class="overview-row"
              >
                <div class="overview-avatar">{{ member.nickname }}</div>
                <div class="flex-grow-1 min-w-0">
                  <div class="text-cream small fw-bold text-truncate">{{ member.name }}</div>
                  <div class="text-muted" style="font-size:0.75rem;">
                    {{ member.role }} · Joined {{ member.year_join }}
                  </div>
                </div>
                <span :class="['badge-section', sectionColors[member.section] || 'badge-muted']">
                  {{ member.section }}
                </span>
              </div>
            </template>

            <div v-else class="overview-empty">
              <i class="bi bi-people"></i>
              <p>No members yet.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ── Stat card enhancements ─────────────────────────────────── */
.stat-card-inner { display: flex; flex-direction: column; height: 100%; }

.stat-trend {
  font-size: 0.62rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.25rem 0.6rem;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  white-space: nowrap;
}
.stat-trend.up {
  color: #7ec89a;
  background: rgba(126, 200, 154, 0.12);
  border: 1px solid rgba(126, 200, 154, 0.25);
}
.stat-trend.neutral {
  color: var(--bms-muted);
  background: rgba(168, 162, 147, 0.08);
  border: 1px solid rgba(168, 162, 147, 0.18);
}

.stat-card--accent {
  border-color: rgba(201, 169, 97, 0.3) !important;
  background: linear-gradient(145deg, rgba(30, 26, 46, 0.95), rgba(18, 15, 30, 0.98)) !important;
}

.stat-icon--accent {
  background: rgba(201, 169, 97, 0.18) !important;
  border-color: rgba(201, 169, 97, 0.45) !important;
}

.stat-card-footer {
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid rgba(201, 169, 97, 0.08);
  font-size: 0.7rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--bms-muted);
  transition: color 0.25s ease;
}
a:hover .stat-card-footer { color: var(--bms-gold); }

/* ── Panel headers ───────────────────────────────────────────── */
.overview-panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(201, 169, 97, 0.1);
  background: rgba(201, 169, 97, 0.03);
}
.overview-panel-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(201, 169, 97, 0.12);
  border: 1px solid rgba(201, 169, 97, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--bms-gold);
  font-size: 0.9rem;
}
.overview-panel-link {
  font-size: 0.72rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--bms-muted);
  text-decoration: none;
  transition: color 0.25s ease;
  white-space: nowrap;
}
.overview-panel-link:hover { color: var(--bms-gold); }

.overview-panel-body {
  padding: 0.5rem 0;
}

/* ── Overview rows ───────────────────────────────────────────── */
.overview-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1.5rem;
  border-bottom: 1px solid rgba(201, 169, 97, 0.06);
  transition: background 0.2s ease;
}
.overview-row:last-child { border-bottom: none; }
.overview-row:hover { background: rgba(201, 169, 97, 0.04); }

/* Date badge for events */
.overview-row-date {
  width: 44px;
  flex-shrink: 0;
  text-align: center;
  padding: 0.4rem 0.3rem;
  border-radius: 6px;
  background: rgba(201, 169, 97, 0.08);
  border: 1px solid rgba(201, 169, 97, 0.18);
}
.overview-date-month {
  font-size: 0.55rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--bms-gold);
  line-height: 1;
}
.overview-date-day {
  font-family: var(--font-display);
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--bms-cream);
  line-height: 1.1;
}

/* Empty state */
.overview-empty {
  padding: 3rem 1.5rem;
  text-align: center;
  color: var(--bms-muted);
}
.overview-empty i { font-size: 2.2rem; opacity: 0.3; display: block; margin-bottom: 0.75rem; }
.overview-empty p { font-size: 0.85rem; margin: 0; }

/* ── Section color badges ────────────────────────────────────── */
.badge-section {
  font-size: 0.62rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.3em 0.65em;
  border-radius: 2px;
  font-weight: 500;
  white-space: nowrap;
}
.section-soprano {
  color: #e8a0b0;
  background: rgba(139, 30, 63, 0.14);
  border: 1px solid rgba(139, 30, 63, 0.35);
}
.section-alto {
  color: #c9a961;
  background: rgba(201, 169, 97, 0.12);
  border: 1px solid rgba(201, 169, 97, 0.3);
}
.section-tenor {
  color: #7ec8e3;
  background: rgba(126, 200, 227, 0.12);
  border: 1px solid rgba(126, 200, 227, 0.28);
}
.section-bass {
  color: #9b8ec4;
  background: rgba(155, 142, 196, 0.12);
  border: 1px solid rgba(155, 142, 196, 0.28);
}

/* ── Skeleton loaders ────────────────────────────────────────── */
.skeleton-line {
  border-radius: 4px;
  background: linear-gradient(90deg,
    rgba(201,169,97,0.06) 25%,
    rgba(201,169,97,0.12) 50%,
    rgba(201,169,97,0.06) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.6s ease-in-out infinite;
}
.skeleton-num   { height: 2.4rem; width: 60px; }
.skeleton-title { height: 0.75rem; width: 60%; }
.skeleton-sub   { height: 0.65rem; width: 40%; }
.skeleton-badge { height: 1.4rem; width: 56px; border-radius: 2px; }
.skeleton-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  flex-shrink: 0;
  background: linear-gradient(90deg,
    rgba(201,169,97,0.06) 25%,
    rgba(201,169,97,0.12) 50%,
    rgba(201,169,97,0.06) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.6s ease-in-out infinite;
}
@keyframes skeleton-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
