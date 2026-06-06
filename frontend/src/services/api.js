import { useAuthStore } from '../stores/auth'

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000/api'

export async function apiFetch(endpoint, options = {}) {
  const authStore = useAuthStore()
  const token = authStore.token

  const config = {
    headers: {
      'Content-Type': 'application/json',
      ...options.headers
    },
    ...options
  }

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  const response = await fetch(`${API_BASE}${endpoint}`, config)
  const data = await response.json()

  if (!response.ok) {
    throw new Error(data.error || 'API request failed')
  }

  return data
}

export const api = {
  login: (credentials) => apiFetch('/auth/login', {
    method: 'POST',
    body: JSON.stringify(credentials)
  }),

  register: (data) => apiFetch('/auth/register', {
    method: 'POST',
    body: JSON.stringify(data)
  }),

  getProfile: () => apiFetch('/auth/profile'),

  updateProfile: (data) => apiFetch('/auth/profile', {
    method: 'PUT',
    body: JSON.stringify(data)
  }),

  getMembers: () => apiFetch('/members'),

  getActiveMembers: () => apiFetch('/members/active'),

  getMembersBySection: () => apiFetch('/members/section'),

  createMember: (data) => apiFetch('/members', {
    method: 'POST',
    body: JSON.stringify(data)
  }),

  updateMemberStatus: (id, status) => apiFetch(`/members/${id}/status`, {
    method: 'PATCH',
    body: JSON.stringify({ status })
  }),

  updateMember: (id, data) => apiFetch(`/members/${id}`, {
    method: 'PUT',
    body: JSON.stringify(data)
  }),

  deleteMember: (id) => apiFetch(`/members/${id}`, {
    method: 'DELETE'
  }),

  getEvents: () => apiFetch('/events'),

  getUpcomingEvents: () => apiFetch('/events/upcoming'),

createEvent: (data) => apiFetch('/events', {
    method: 'POST',
    body: JSON.stringify(data)
  }),

   updateEvent: (id, data) => apiFetch(`/events/${id}`, {
     method: 'PUT',
     body: JSON.stringify(data)
   }),

   deleteEvent: (id) => apiFetch(`/events/${id}`, {
    method: 'DELETE'
  }),

  getEventParticipants: (eventId) => apiFetch(`/events/${eventId}/participants`),

   addEventParticipant: (eventId, memberId) => apiFetch(`/events/${eventId}/participants`, {
     method: 'POST',
     body: JSON.stringify({ member_id: memberId })
   }),

   addEventParticipantsBulk: (eventId, memberIds) => apiFetch(`/events/${eventId}/participants/bulk`, {
     method: 'POST',
     body: JSON.stringify({ member_ids: memberIds })
   }),

  submitAttendance: (eventId, data) => apiFetch(`/events/${eventId}/attendance`, {
    method: 'POST',
    body: JSON.stringify(data)
  }),

  getAttendance: (eventId) => apiFetch(`/events/${eventId}/attendance`),

  setAttendance: (eventId, memberId, data) => apiFetch(`/events/${eventId}/attendance/${memberId}`, {
    method: 'PATCH',
    body: JSON.stringify(data)
  })
}
