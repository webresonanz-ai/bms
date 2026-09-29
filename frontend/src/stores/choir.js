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

  const events = ref([
    {
      id: 1,
      title: 'Harmony of the Archipelago',
      date: '2026-11-21',
      time: '19:30',
      venue: 'Aula Simfonia Jakarta',
      city: 'Jakarta',
      description: 'A journey through Indonesian folk songs reimagined for chamber choir.',
      status: 'upcoming'
    },
    {
      id: 2,
      title: 'Sacred Voices: Requiem',
      date: '2026-12-13',
      time: '18:00',
      venue: 'Katedral Jakarta',
      city: 'Jakarta',
      description: 'Featuring Fauré\'s Requiem and works by contemporary composers.',
      status: 'upcoming'
    },
    {
      id: 3,
      title: 'Christmas with Batavia',
      date: '2024-12-20',
      time: '20:00',
      venue: 'Balai Kartini',
      city: 'Jakarta',
      description: 'An evening of carols and holiday favorites for the whole family.',
      status: 'past'
    },
    {
      id: 4,
      title: 'Bach Motets Marathon',
      date: '2024-10-05',
      time: '16:00',
      venue: 'Goethe Institut',
      city: 'Jakarta',
      description: 'Complete performance of J.S. Bach\'s six motets.',
      status: 'past'
    }
  ])

  const milestones = ref([
    { year: '2001', title: 'Foundation', description: 'Batavia Madrigal Singers was founded by a group of passionate choral enthusiasts in Jakarta.' },
    { year: '2005', title: 'First International Tour', description: 'Our first overseas performance at the Singapore Choral Festival.' },
    { year: '2010', title: 'National Recognition', description: 'Awarded Best Chamber Choir at the Indonesian Choral Competition.' },
    { year: '2015', title: 'European Debut', description: 'Toured Vienna, Salzburg, and Prague, performing in historic venues.' },
    { year: '2020', title: 'Virtual Concerts', description: 'Pioneered online choral performances during the global pandemic.' },
    { year: '2024', title: 'World Choir Games', description: 'Awarded Gold Medal at the World Choir Games in Auckland.' }
  ])

  return { members, membersLoading, membersError, fetchMembers, events, milestones }
})