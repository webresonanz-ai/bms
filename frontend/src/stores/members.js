import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../services/api'

export const useMembersStore = defineStore('members', () => {
  const members = ref([])

  const activeMembers = computed(() =>
    members.value.filter(member => member.status === 'active')
  )

  const membersBySection = computed(() => {
    const sections = {}
    members.value.forEach(member => {
      if (!sections[member.section]) {
        sections[member.section] = []
      }
      sections[member.section].push(member)
    })
    return sections
  })

  async function fetchMembers() {
    try {
      const response = await api.getMembers()
      members.value = response
    } catch (error) {
      console.error('Failed to fetch members:', error)
    }
  }

  async function fetchActiveMembers() {
    try {
      const response = await api.getActiveMembers()
      members.value = response
    } catch (error) {
      console.error('Failed to fetch active members:', error)
    }
  }

  async function fetchMembersBySection() {
    try {
      const response = await api.getMembersBySection()
      const allMembers = Object.values(response).flat()
      members.value = allMembers
    } catch (error) {
      console.error('Failed to fetch members by section:', error)
    }
  }

  async function addMember(member) {
    try {
      const response = await api.createMember(member)
      members.value.push(response)
    } catch (error) {
      console.error('Failed to create member:', error)
      throw error
    }
  }

  async function updateMemberStatus(id, status) {
    try {
      await api.updateMemberStatus(id, status)
      const member = members.value.find(m => m.id === id)
      if (member) {
        member.status = status
      }
    } catch (error) {
      console.error('Failed to update member status:', error)
      throw error
    }
  }

  async function updateMember(id, data) {
    try {
      const response = await api.updateMember(id, data)
      const index = members.value.findIndex(m => m.id === id)
      if (index !== -1) {
        members.value[index] = response
      }
    } catch (error) {
      console.error('Failed to update member:', error)
      throw error
    }
  }

  async function deleteMember(id) {
    try {
      await api.deleteMember(id)
      members.value = members.value.filter(m => m.id !== id)
    } catch (error) {
      console.error('Failed to delete member:', error)
      throw error
    }
  }

  return {
    members,
    activeMembers,
    membersBySection,
    fetchMembers,
    fetchActiveMembers,
    fetchMembersBySection,
    addMember,
    updateMemberStatus,
    deleteMember
  }
})