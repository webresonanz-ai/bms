import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)

  const isAuthenticated = computed(() => !!user.value && !!token.value)

  async function login(credentials) {
    const response = await api.login(credentials)
    user.value = response.user
    token.value = response.token
    localStorage.setItem('token', response.token)
    return response
  }

  async function register(data) {
    const response = await api.register(data)
    user.value = response.user
    token.value = response.token
    localStorage.setItem('token', response.token)
    return response
  }

  async function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
  }

  async function fetchProfile() {
    if (!token.value) return
    try {
      const response = await api.getProfile()
      user.value = response
    } catch (error) {
      console.error('Failed to fetch profile:', error)
    }
  }

  async function updateProfile(profileData) {
    if (!token.value) return
    await api.updateProfile(profileData)
    user.value = { ...user.value, ...profileData }
  }

  function initFromStorage() {
    const storedToken = localStorage.getItem('token')
    if (storedToken) {
      token.value = storedToken
      fetchProfile()
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchProfile,
    updateProfile,
    initFromStorage
  }
})