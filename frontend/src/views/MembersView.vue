<script setup>
import { ref, computed } from 'vue'
import { useChoirStore } from '../stores/choir'

const choirStore = useChoirStore()
const activeSection = ref('All')

const sections = ['All', 'Soprano', 'Alto', 'Tenor', 'Bass']

const filteredMembers = computed(() => {
  if (activeSection.value === 'All') return choirStore.members
  return choirStore.members.filter(m => m.section === activeSection.value)
})
</script>

<template>
  <div>
    <div class="page-header">
      <div class="container">
        <p class="hero-subtitle mb-3 fade-in">The Voices</p>
        <h1 class="hero-title fade-in-up">Our Members</h1>
        <div class="gold-divider"></div>
        <p class="mt-4 fade-in" style="max-width: 600px; margin-inline: auto;">
          Meet the talented singers who bring our music to life — a diverse
          community united by their love for choral artistry.
        </p>
      </div>
    </div>

    <section class="section-padding bg-dark-custom">
      <div class="container">
        <!-- Filter -->
        <div class="d-flex justify-content-center gap-2 mb-5 flex-wrap" v-reveal>
          <button
            v-for="section in sections"
            :key="section"
            @click="activeSection = section"
            class="btn"
            :class="activeSection === section ? 'btn-gold' : 'btn-outline-gold'"
            style="padding: 0.5rem 1.5rem; font-size: 0.75rem;"
            :aria-pressed="activeSection === section"
          >
            {{ section }}
          </button>
        </div>

        <!-- Members grid with FLIP animation on filter -->
        <div class="row g-4 position-relative">
          <TransitionGroup name="members-grid">
            <div
              v-for="(member, index) in filteredMembers"
              :key="member.id"
              class="col-lg-3 col-md-4 col-6"
              :style="{ transitionDelay: (index % 8) * 40 + 'ms' }"
            >
              <div class="member-card p-4 text-center h-100">
                <div class="member-avatar">{{ member.initials }}</div>
                <h6 class="text-cream mb-1">{{ member.name }}</h6>
                <p class="text-gold small mb-2" style="letter-spacing: 0.15em; text-transform: uppercase;">
                  {{ member.role }}
                </p>
                <p class="text-muted mb-0" style="font-size: 0.75rem;">Since {{ member.joined }}</p>
              </div>
            </div>
          </TransitionGroup>
        </div>
      </div>
    </section>

    <!-- Join CTA -->
    <section class="section-padding bg-darker-custom">
      <div class="container">
        <div class="elegant-card p-5 text-center" v-reveal>
          <i class="bi bi-mic text-gold" style="font-size: 3rem;"></i>
          <h2 class="text-cream fw-bold mt-3 mb-3">Want to Sing With Us?</h2>
          <div class="gold-divider"></div>
          <p class="mb-4 mx-auto" style="max-width: 500px;">
            We hold auditions periodically throughout the year.
            If you share our passion for choral music, we'd love to hear you.
          </p>
          <RouterLink to="/contact" class="btn btn-gold">Audition Info</RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>
