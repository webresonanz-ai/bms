<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useChoirStore } from '../stores/choir'

const choirStore = useChoirStore()
const activeSection = ref('All')
const currentPage = ref(1)
const pageSize = ref(8)
const gridTop = ref(null)

const sections = ['All', 'Soprano', 'Alto', 'Tenor', 'Bass']

// Normalize DB values: role uses 'Sopran', section uses 'Soprano',
// and real data may have different casing, whitespace, or NULL section.
function normalizeSection(value) {
  const v = String(value ?? '').trim().toLowerCase()
  if (!v) return ''
  if (['sopran', 'soprano', 'sopr'].includes(v)) return 'Soprano'
  if (['alto', 'alt'].includes(v)) return 'Alto'
  if (['tenor', 'tenore', 'ten'].includes(v)) return 'Tenor'
  if (['bass', 'basse', 'baritone', 'bariton'].includes(v)) return 'Bass'
  return ''
}

// Section wins; fall back to role when section is empty/unknown.
function memberSection(m) {
  return normalizeSection(m.section) || normalizeSection(m.role)
}

// Only show active members on the public page; passive members stay in the DB/admin.
const activeMembers = computed(() =>
  choirStore.members.filter(m => (m.status ?? 'active') === 'active')
)

const sectionCounts = computed(() => {
  const counts = { Soprano: 0, Alto: 0, Tenor: 0, Bass: 0 }
  for (const m of activeMembers.value) {
    const s = memberSection(m)
    if (counts[s] !== undefined) counts[s] += 1
  }
  return counts
})

const filteredMembers = computed(() => {
  if (activeSection.value === 'All') return activeMembers.value
  return activeMembers.value.filter(m => memberSection(m) === activeSection.value)
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredMembers.value.length / pageSize.value))
)

const paginatedMembers = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredMembers.value.slice(start, start + pageSize.value)
})

const pageRangeText = computed(() => {
  const total = filteredMembers.value.length
  if (!total) return '0 members'
  const start = (currentPage.value - 1) * pageSize.value + 1
  const end = Math.min(currentPage.value * pageSize.value, total)
  return `Showing ${start}–${end} of ${total} members`
})

// Compact page numbers with ellipsis: 1 … 4 5 6 … 12
const visiblePages = computed(() => {
  const total = totalPages.value
  const cur = currentPage.value
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)
  const pages = new Set([1, 2, cur - 1, cur, cur + 1, total - 1, total])
  const sorted = [...pages].filter(p => p >= 1 && p <= total).sort((a, b) => a - b)
  const out = []
  let prev = 0
  for (const p of sorted) {
    if (p - prev > 1) out.push('…')
    out.push(p)
    prev = p
  }
  return out
})

function goToPage(page) {
  const p = Math.min(Math.max(1, page), totalPages.value)
  if (p === currentPage.value) return
  currentPage.value = p
  gridTop.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

// Reset to page 1 when filter or data changes
watch(activeSection, () => { currentPage.value = 1 })
watch(filteredMembers, () => {
  if (currentPage.value > totalPages.value) currentPage.value = totalPages.value
})

// Avatar fallback: photo from DB, otherwise 2-letter initials derived from name.
function avatarText(member) {
  if (!member?.name) return (member?.nickname || '?').slice(0, 2).toUpperCase()
  return member.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
}

onMounted(async () => {
  await choirStore.fetchMembers()
})
</script>

<template>
  <div>
    <div class="page-header">
      <div class="container">
        <p class="hero-subtitle mb-3 fade-in">The Voices</p>
        <h1 class="hero-title fade-in-up">Our Members</h1>
        <div class="gold-divider"></div>
        <p class="mt-4 fade-in" style="max-width: 600px; margin-inline: auto;">
          Meet the talented singers who bring our music to life — a diverse
          community united by their love for choral artistry.
        </p>
      </div>
    </div>

    <section class="section-padding bg-dark-custom">
      <div class="container" ref="gridTop" style="scroll-margin-top: 90px;">
        <!-- Filter -->
        <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap" v-reveal>
          <button
            v-for="section in sections"
            :key="section"
            @click="activeSection = section"
            class="btn"
            :class="activeSection === section ? 'btn-gold' : 'btn-outline-gold'"
            style="padding: 0.5rem 1.5rem; font-size: 0.75rem;"
            :aria-pressed="activeSection === section"
          >
            {{ section }}
            <span class="filter-count">{{ section === 'All' ? activeMembers.length : (sectionCounts[section] ?? 0) }}</span>
          </button>
        </div>

        <!-- Result count -->
        <p v-if="!choirStore.membersLoading && !choirStore.membersError" class="text-center text-muted small mb-4">
          {{ pageRangeText }}
        </p>

        <!-- Loading state (live fetch from database) -->
        <div v-if="choirStore.membersLoading" class="row g-4">
          <div v-for="n in 8" :key="n" class="col-lg-3 col-md-4 col-6">
            <div class="member-card p-4 text-center h-100" aria-hidden="true">
              <div class="member-avatar opacity-50"></div>
              <p class="text-muted small mb-0">Loading…</p>
            </div>
          </div>
        </div>

        <!-- Error state -->
        <div v-else-if="choirStore.membersError" class="text-center py-5">
          <i class="bi bi-exclamation-circle text-gold" style="font-size: 3rem; opacity: 0.5;"></i>
          <p class="text-muted mt-3">{{ choirStore.membersError }}</p>
          <button class="btn btn-outline-gold mt-2" @click="choirStore.fetchMembers()">
            Try again
          </button>
        </div>

        <!-- Empty state -->
        <div v-else-if="!filteredMembers.length" class="text-center py-5">
          <i class="bi bi-people text-gold" style="font-size: 3rem; opacity: 0.5;"></i>
          <p class="text-muted mt-3">
            {{ activeSection === 'All' ? 'No members to display yet.' : `No ${activeSection} members found.` }}
          </p>
        </div>

        <!-- Members grid with FLIP animation on filter -->
        <div v-else class="row g-4 position-relative">
          <TransitionGroup name="members-grid">
            <div
              v-for="(member, index) in paginatedMembers"
              :key="member.id"
              class="col-lg-3 col-md-4 col-6"
              :style="{ transitionDelay: (index % 8) * 40 + 'ms' }"
            >
              <div class="member-card p-4 text-center h-100">
                <div class="member-avatar">
                  <img
                    v-if="member.avatar_url"
                    :src="member.avatar_url"
                    :alt="member.name"
                    loading="lazy"
                  />
                  <span v-else>{{ avatarText(member) }}</span>
                </div>
                <h6 class="text-cream mb-1">{{ member.stage_name || member.name }}</h6>
                <p class="text-gold small mb-2" style="letter-spacing: 0.15em; text-transform: uppercase;">
                  {{ member.role }}
                </p>
                <p class="text-muted mb-0" style="font-size: 0.75rem;">Since {{ member.year_join }}</p>
              </div>
            </div>
          </TransitionGroup>
        </div>

        <!-- Pagination -->
        <nav
          v-if="!choirStore.membersLoading && !choirStore.membersError && totalPages > 1"
          class="d-flex justify-content-center align-items-center gap-2 mt-5 flex-wrap"
          aria-label="Members pages"
        >
          <button
            class="btn btn-outline-gold btn-page"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
            aria-label="Previous page"
          >
            <i class="bi bi-chevron-left"></i>
          </button>

          <template v-for="(p, i) in visiblePages" :key="i">
            <span v-if="p === '…'" class="text-muted px-1">…</span>
            <button
              v-else
              class="btn btn-page"
              :class="p === currentPage ? 'btn-gold' : 'btn-outline-gold'"
              :aria-current="p === currentPage ? 'page' : undefined"
              @click="goToPage(p)"
            >
              {{ p }}
            </button>
          </template>

          <button
            class="btn btn-outline-gold btn-page"
            :disabled="currentPage === totalPages"
            @click="goToPage(currentPage + 1)"
            aria-label="Next page"
          >
            <i class="bi bi-chevron-right"></i>
          </button>
        </nav>
      </div>
    </section>

    <!-- Join CTA -->
    <section class="section-padding bg-darker-custom">
      <div class="container">
        <div class="elegant-card p-5 text-center" v-reveal>
          <i class="bi bi-mic text-gold" style="font-size: 3rem;"></i>
          <h2 class="text-cream fw-bold mt-3 mb-3">Want to Sing With Us?</h2>
          <div class="gold-divider"></div>
          <p class="mb-4 mx-auto" style="max-width: 500px;">
            We hold auditions periodically throughout the year.
            If you share our passion for choral music, we'd love to hear you.
          </p>
          <RouterLink to="/contact" class="btn btn-gold">Audition Info</RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.member-avatar {
  overflow: hidden;
}
.member-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}
.btn-page {
  min-width: 2.5rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.75rem;
}
.btn-page:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}
.filter-count {
  display: inline-block;
  margin-left: 0.5rem;
  padding: 0.1em 0.55em;
  border-radius: 999px;
  font-size: 0.68rem;
  font-weight: 700;
  background: rgba(201, 169, 97, 0.18);
  border: 1px solid rgba(201, 169, 97, 0.35);
  vertical-align: 1px;
}
</style>
