import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const API_BASE = 'http://localhost:8000'

export const useAuthStore = defineStore('auth', () => {
  // ── State ──────────────────────────────────────────────────────────
  const token = ref(localStorage.getItem('bms_token') || null)
  const user  = ref(JSON.parse(localStorage.getItem('bms_user') || 'null'))

  // ── Getters ────────────────────────────────────────────────────────
  const isLoggedIn = computed(() => !!token.value)
  const isAdmin    = computed(() => user.value?.role === 'admin')

  // ── Helpers ────────────────────────────────────────────────────────
  function persist(newToken, newUser) {
    token.value = newToken
    user.value  = newUser
    localStorage.setItem('bms_token', newToken)
    localStorage.setItem('bms_user',  JSON.stringify(newUser))
  }

  async function apiFetch(endpoint, options = {}) {
    const headers = {
      'Content-Type': 'application/json',
      ...(token.value ? { Authorization: `Bearer ${token.value}` } : {}),
      ...(options.headers || {}),
    }

    const res = await fetch(`${API_BASE}${endpoint}`, {
      ...options,
      headers,
    })

    const data = await res.json()

    if (!res.ok) {
      const err = new Error(data.message || 'Request failed')
      err.status = res.status
      err.errors = data.errors || {}
      throw err
    }

    return data
  }

  // ── Actions ────────────────────────────────────────────────────────
  async function register(payload) {
    const data = await apiFetch('/api/v1/auth/register', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
    persist(data.data.token, data.data.user)
    return data.data.user
  }

  async function login(payload) {
    const data = await apiFetch('/api/v1/auth/login', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
    persist(data.data.token, data.data.user)
    return data.data.user
  }

  function logout() {
    token.value = null
    user.value  = null
    localStorage.removeItem('bms_token')
    localStorage.removeItem('bms_user')
  }

  async function fetchMe() {
    try {
      const data = await apiFetch('/api/v1/auth/me')
      user.value = data.data.user
      localStorage.setItem('bms_user', JSON.stringify(user.value))
    } catch {
      logout()
    }
  }

  return { token, user, isLoggedIn, isAdmin, register, login, logout, fetchMe }
})
