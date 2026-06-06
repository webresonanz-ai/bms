import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../services/api'

export const useEventsStore = defineStore('events', () => {
  const events = ref([])

  const upcomingEvents = computed(() =>
    events.value.filter(event => event.status === 'upcoming')
      .sort((a, b) => new Date(a.date) - new Date(b.date))
  )

  async function fetchEvents() {
    try {
      const response = await api.getEvents()
      events.value = response
    } catch (error) {
      console.error('Failed to fetch events:', error)
    }
  }

  async function fetchUpcomingEvents() {
    try {
      const response = await api.getUpcomingEvents()
      events.value = response
    } catch (error) {
      console.error('Failed to fetch upcoming events:', error)
    }
  }

  async function addEvent(event) {
    try {
      const response = await api.createEvent(event)
      events.value.push(response)
    } catch (error) {
      console.error('Failed to create event:', error)
      throw error
    }
  }

  async function deleteEvent(id) {
    try {
      await api.deleteEvent(id)
      events.value = events.value.filter(event => event.id !== id)
    } catch (error) {
      console.error('Failed to delete event:', error)
      throw error
    }
  }

  return {
    events,
    upcomingEvents,
    fetchEvents,
    fetchUpcomingEvents,
    addEvent,
    deleteEvent
  }
})