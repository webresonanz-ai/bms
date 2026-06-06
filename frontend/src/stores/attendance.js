import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '../services/api'

export const useAttendanceStore = defineStore('attendance', () => {
  const attendanceRecords = ref([])
  const participantList = ref([])
  const currentEvent = ref(null)
  const loading = ref(false)

  const filteredParticipants = ref([])
  const selectedSection = ref('All')
  const selectedStatus = ref('All')

  async function fetchEventParticipants(eventId) {
    loading.value = true
    try {
      const response = await api.getEventParticipants(eventId)
      participantList.value = response.members || response
      currentEvent.value = response.event || null
      applyFilters()
    } catch (error) {
      console.error('Failed to fetch participants:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function fetchAttendance(eventId) {
    loading.value = true
    try {
      const response = await api.getAttendance(eventId)
      attendanceRecords.value = response
      mergeAttendanceToParticipants()
    } catch (error) {
      console.error('Failed to fetch attendance:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  function mergeAttendanceToParticipants() {
    const attendanceMap = new Map(attendanceRecords.value.map(record => [record.member_id || record.id, record]))
    participantList.value = participantList.value.map(participant => ({
      ...participant,
      attendance: attendanceMap.get(participant.member_id || participant.id) || participant.attendance || null
    }))
    applyFilters()
  }

  async function recordAttendance(eventId, memberId, data) {
    try {
      const response = await api.setAttendance(eventId, memberId, data)
      const index = attendanceRecords.value.findIndex(r => r.member_id === memberId)
      if (index !== -1) {
        attendanceRecords.value[index] = response
      } else {
        attendanceRecords.value.push(response)
      }
      const pIndex = participantList.value.findIndex(p => p.member_id === memberId || p.id === memberId)
      if (pIndex !== -1) {
        participantList.value[pIndex] = { ...participantList.value[pIndex], attendance: response }
      }
      applyFilters()
    } catch (error) {
      console.error('Failed to record attendance:', error)
      throw error
    }
  }

  async function submitBulkAttendance(eventId, records) {
    try {
      const payload = Array.isArray(records)
        ? { records }
        : records && Array.isArray(records.records)
          ? records
          : { records: [] }
      const response = await api.submitAttendance(eventId, payload)

      if (!response || typeof response !== 'object') {
        throw new Error('Invalid attendance response from server')
      }

      attendanceRecords.value = response.attendance || response
      mergeAttendanceToParticipants()
      return response
    } catch (error) {
      console.error('Failed to submit attendance:', error)
      throw error
    }
  }

  function applyFilters() {
    let result = [...participantList.value]
    if (selectedSection.value !== 'All') {
      result = result.filter(p => (p.section || p.section_name) === selectedSection.value)
    }
    if (selectedStatus.value !== 'All') {
      result = result.filter(p => {
        const status = p.attendance?.status || (p.status === 'attended' ? 'Present' : 'Absent')
        return status === selectedStatus.value
      })
    }
    filteredParticipants.value = result
  }

  function setSectionFilter(section) {
    selectedSection.value = section
    applyFilters()
  }

  function setStatusFilter(status) {
    selectedStatus.value = status
    applyFilters()
  }

  function getSections() {
    const sections = new Set()
    participantList.value.forEach(p => {
      if (p.section || p.section_name) {
        sections.add(p.section || p.section_name)
      }
    })
    return Array.from(sections)
  }

  function resetFilters() {
    selectedSection.value = 'All'
    selectedStatus.value = 'All'
    applyFilters()
  }

  function reset() {
    attendanceRecords.value = []
    participantList.value = []
    currentEvent.value = null
    filteredParticipants.value = []
    selectedSection.value = 'All'
    selectedStatus.value = 'All'
  }

  return {
    attendanceRecords,
    participantList,
    currentEvent,
    loading,
    filteredParticipants,
    selectedSection,
    selectedStatus,
    fetchEventParticipants,
    fetchAttendance,
    recordAttendance,
    submitBulkAttendance,
    applyFilters,
    setSectionFilter,
    setStatusFilter,
    getSections,
    resetFilters,
    reset
  }
})
