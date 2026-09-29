import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useAuthStore } from './auth'

/**
 * Admin store — CRUD wrapper around the backend content APIs.
 * All endpoints require an admin JWT (handled by auth.apiFetch).
 */
export const useAdminStore = defineStore('admin', () => {
  const members = ref([])
  const events = ref([])
  const galleryItems = ref([])

  const loading = ref(false)
  const error = ref('')

  // ── Internal helpers ─────────────────────────────────────────────
  function setError(err) {
    error.value = err?.message || 'Something went wrong. Please try again.'
  }

  function clearError() {
    error.value = ''
  }

  // ── Members ──────────────────────────────────────────────────────
  async function fetchMembers() {
    loading.value = true
    clearError()
    try {
      const data = await useAuthStore().apiFetch('/api/v1/members')
      members.value = data.data.members
    } catch (err) {
      setError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  async function createMember(payload) {
    const data = await useAuthStore().apiFetch('/api/v1/members', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
    members.value.push(data.data.member)
    return data.data.member
  }

  async function updateMember(id, payload) {
    const data = await useAuthStore().apiFetch(`/api/v1/members/${id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
    const idx = members.value.findIndex(m => m.id === id)
    if (idx !== -1) members.value[idx] = data.data.member
    return data.data.member
  }

  async function deleteMember(id) {
    await useAuthStore().apiFetch(`/api/v1/members/${id}`, { method: 'DELETE' })
    members.value = members.value.filter(m => m.id !== id)
  }

  // ── Events ───────────────────────────────────────────────────────
  async function fetchEvents() {
    loading.value = true
    clearError()
    try {
      const data = await useAuthStore().apiFetch('/api/v1/events')
      events.value = data.data.events
    } catch (err) {
      setError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  async function createEvent(payload) {
    const data = await useAuthStore().apiFetch('/api/v1/events', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
    events.value.push(data.data.event)
    return data.data.event
  }

  async function updateEvent(id, payload) {
    const data = await useAuthStore().apiFetch(`/api/v1/events/${id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
    const idx = events.value.findIndex(e => e.id === id)
    if (idx !== -1) events.value[idx] = data.data.event
    return data.data.event
  }

  async function deleteEvent(id) {
    await useAuthStore().apiFetch(`/api/v1/events/${id}`, { method: 'DELETE' })
    events.value = events.value.filter(e => e.id !== id)
  }

  // ── Gallery ──────────────────────────────────────────────────────
  async function fetchGallery() {
    loading.value = true
    clearError()
    try {
      const data = await useAuthStore().apiFetch('/api/v1/gallery')
      galleryItems.value = data.data.items
    } catch (err) {
      setError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  async function createGalleryItem(payload) {
    const data = await useAuthStore().apiFetch('/api/v1/gallery', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
    galleryItems.value.push(data.data.item)
    return data.data.item
  }

  async function updateGalleryItem(id, payload) {
    const data = await useAuthStore().apiFetch(`/api/v1/gallery/${id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
    const idx = galleryItems.value.findIndex(g => g.id === id)
    if (idx !== -1) galleryItems.value[idx] = data.data.item
    return data.data.item
  }

  async function deleteGalleryItem(id) {
    await useAuthStore().apiFetch(`/api/v1/gallery/${id}`, { method: 'DELETE' })
    galleryItems.value = galleryItems.value.filter(g => g.id !== id)
  }

  return {
    members,
    events,
    galleryItems,
    loading,
    error,
    clearError,
    fetchMembers,
    createMember,
    updateMember,
    deleteMember,
    fetchEvents,
    createEvent,
    updateEvent,
    deleteEvent,
    fetchGallery,
    createGalleryItem,
    updateGalleryItem,
    deleteGalleryItem,
  }
})
