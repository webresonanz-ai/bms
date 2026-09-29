import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useChoirStore = defineStore('choir', () => {
  const members = ref([
    { id: 1, name: 'Aria Wijaya', role: 'Soprano', section: 'Soprano', joined: 2015, initials: 'AW' },
    { id: 2, name: 'Bunga Lestari', role: 'Soprano', section: 'Soprano', joined: 2016, initials: 'BL' },
    { id: 3, name: 'Citra Dewi', role: 'Soprano', section: 'Soprano', joined: 2018, initials: 'CD' },
    { id: 4, name: 'Damar Pratama', role: 'Alto', section: 'Alto', joined: 2014, initials: 'DP' },
    { id: 5, name: 'Eka Sari', role: 'Alto', section: 'Alto', joined: 2017, initials: 'ES' },
    { id: 6, name: 'Fajar Nugroho', role: 'Tenor', section: 'Tenor', joined: 2013, initials: 'FN' },
    { id: 7, name: 'Gita Permata', role: 'Tenor', section: 'Tenor', joined: 2019, initials: 'GP' },
    { id: 8, name: 'Hadi Santoso', role: 'Bass', section: 'Bass', joined: 2012, initials: 'HS' },
    { id: 9, name: 'Indah Cahaya', role: 'Bass', section: 'Bass', joined: 2020, initials: 'IC' },
    { id: 10, name: 'Joko Widodo', role: 'Baritone', section: 'Bass', joined: 2011, initials: 'JW' },
    { id: 11, name: 'Kartika Sari', role: 'Soprano', section: 'Soprano', joined: 2021, initials: 'KS' },
    { id: 12, name: 'Larasati Putri', role: 'Alto', section: 'Alto', joined: 2019, initials: 'LP' }
  ])

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

  return { members, events, milestones }
})