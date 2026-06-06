import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useMembersStore = defineStore('members', () => {
  const members = ref([
    {
      id: 1,
      name: 'Sarah Johnson',
      role: 'Lead Soprano',
      section: 'Soprano',
      joinDate: '2024-01-15',
      status: 'active',
      avatar: 'https://i.pravatar.cc/150?img=1',
      email: 'sarah.j@bms.com',
      phone: '+1 234-567-8901',
      performances: 25
    },
    {
      id: 2,
      name: 'Michael Chen',
      role: 'Tenor Section Leader',
      section: 'Tenor',
      joinDate: '2023-09-20',
      status: 'active',
      avatar: 'https://i.pravatar.cc/150?img=2',
      email: 'michael.c@bms.com',
      phone: '+1 234-567-8902',
      performances: 30
    },
    {
      id: 3,
      name: 'Emily Davis',
      role: 'Alto',
      section: 'Alto',
      joinDate: '2024-03-10',
      status: 'active',
      avatar: 'https://i.pravatar.cc/150?img=3',
      email: 'emily.d@bms.com',
      phone: '+1 234-567-8903',
      performances: 18
    }
  ])

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

  function addMember(member) {
    members.value.push({
      id: members.value.length + 1,
      ...member,
      joinDate: new Date().toISOString().split('T')[0],
      status: 'active',
      performances: 0
    })
  }

  function updateMemberStatus(id, status) {
    const member = members.value.find(m => m.id === id)
    if (member) {
      member.status = status
    }
  }

  return {
    members,
    activeMembers,
    membersBySection,
    addMember,
    updateMemberStatus
  }
})