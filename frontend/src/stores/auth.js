import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref({
    id: 1,
    name: 'Alexandra Williams',
    email: 'alexandra.w@bms.com',
    role: 'Music Director',
    avatar: 'https://i.pravatar.cc/150?img=5',
    joinDate: '2023-01-15',
    bio: 'Passionate music director with 15 years of experience in choral conducting. Leading BMS Choir to new heights of musical excellence.',
    phone: '+1 234-567-8900',
    location: 'New York, USA',
    specialization: 'Choral Conducting',
    achievements: [
      'International Choir Competition Gold Medalist 2025',
      'Best Conductor Award 2024',
      'Published "Modern Choral Techniques" 2023'
    ]
  })

  const isAuthenticated = computed(() => !!user.value)

  function updateProfile(profileData) {
    user.value = { ...user.value, ...profileData }
  }

  return {
    user,
    isAuthenticated,
    updateProfile
  }
})