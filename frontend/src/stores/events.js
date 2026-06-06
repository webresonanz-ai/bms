import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useEventsStore = defineStore('events', () => {
  const events = ref([
    {
      id: 1,
      title: 'Annual Concert 2026',
      date: '2026-06-15',
      time: '19:00',
      location: 'Grand Symphony Hall',
      type: 'concert',
      status: 'upcoming',
      participants: 45
    },
    {
      id: 2,
      title: 'Rehearsal Session',
      date: '2026-06-10',
      time: '18:00',
      location: 'Studio A',
      type: 'rehearsal',
      status: 'upcoming',
      participants: 30
    },
    {
      id: 3,
      title: 'Community Performance',
      date: '2026-06-20',
      time: '16:00',
      location: 'City Park Amphitheater',
      type: 'performance',
      status: 'upcoming',
      participants: 25
    }
  ])

  const upcomingEvents = computed(() => 
    events.value.filter(event => event.status === 'upcoming')
      .sort((a, b) => new Date(a.date) - new Date(b.date))
  )

  function addEvent(event) {
    events.value.push({
      id: events.value.length + 1,
      ...event,
      status: 'upcoming'
    })
  }

  function deleteEvent(id) {
    events.value = events.value.filter(event => event.id !== id)
  }

  return {
    events,
    upcomingEvents,
    addEvent,
    deleteEvent
  }
})