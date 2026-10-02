import { defineStore } from 'pinia'
import { ref } from 'vue'

const API_BASE = import.meta.env.VITE_API_BASE_URL

export const useChoirStore = defineStore('choir', () => {
  // ── Members (live from database, public endpoint) ─────────────────
  const members = ref([])
  const membersLoading = ref(false)
  const membersError = ref('')

  async function fetchMembers() {
    membersLoading.value = true
    membersError.value = ''
    try {
      const res = await fetch(`${API_BASE}/api/v1/members`)
      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'Failed to load members.')
      members.value = data.data?.members ?? []
    } catch (err) {
      membersError.value = err.message || 'Failed to load members.'
      members.value = []
    } finally {
      membersLoading.value = false
    }
  }

  // ── Events (live from database, public endpoint) ──────────────────
  const events = ref([])
  const eventsLoading = ref(false)
  const eventsError = ref('')

  async function fetchEvents() {
    // Avoid refetching when data is already loaded
    if (events.value.length || eventsLoading.value) return
    eventsLoading.value = true
    eventsError.value = ''
    try {
      const res = await fetch(`${API_BASE}/api/v1/events`)
      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'Failed to load events.')
      events.value = data.data?.events ?? []
    } catch (err) {
      eventsError.value = err.message || 'Failed to load events.'
      events.value = []
    } finally {
      eventsLoading.value = false
    }
  }

  // ── Gallery (live from database, public endpoint) ─────────────────
  const galleryItems = ref([])
  const galleryLoading = ref(false)
  const galleryError = ref('')

  async function fetchGallery() {
    // Avoid refetching when data is already loaded
    if (galleryItems.value.length || galleryLoading.value) return
    galleryLoading.value = true
    galleryError.value = ''
    try {
      const res = await fetch(`${API_BASE}/api/v1/gallery`)
      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'Failed to load gallery.')
      galleryItems.value = data.data?.items ?? []
    } catch (err) {
      galleryError.value = err.message || 'Failed to load gallery.'
      galleryItems.value = []
    } finally {
      galleryLoading.value = false
    }
  }

  const milestones = ref([
    { year: '2001', title: 'Foundation', description: 'Batavia Madrigal Singers was founded by a group of passionate choral enthusiasts in Jakarta.' },
    { year: '2005', title: 'First International Tour', description: 'Our first overseas performance at the Singapore Choral Festival.' },
    { year: '2010', title: 'National Recognition', description: 'Awarded Best Chamber Choir at the Indonesian Choral Competition.' },
    { year: '2015', title: 'European Debut', description: 'Toured Vienna, Salzburg, and Prague, performing in historic venues.' },
    { year: '2020', title: 'Virtual Concerts', description: 'Pioneered online choral performances during the global pandemic.' },
    { year: '2024', title: 'World Choir Games', description: 'Awarded Gold Medal at the World Choir Games in Auckland.' }
  ])

  return {
    members, membersLoading, membersError, fetchMembers,
    events, eventsLoading, eventsError, fetchEvents,
    galleryItems, galleryLoading, galleryError, fetchGallery,
    milestones,
  }
})