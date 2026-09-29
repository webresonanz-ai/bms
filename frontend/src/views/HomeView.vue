<script setup>
import HeroSection from '../components/HeroSection.vue'
import SectionTitle from '../components/SectionTitle.vue'
import CountUp from '../components/CountUp.vue'
import { useChoirStore } from '../stores/choir'

const choirStore = useChoirStore()

const highlights = [
  { icon: 'bi-trophy', title: 'Gold Medalist', text: 'World Choir Games 2024' },
  { icon: 'bi-globe', title: '20+ Countries', text: 'International tours' },
  { icon: 'bi-people', title: '40+ Members', text: 'From across Indonesia' },
  { icon: 'bi-calendar-check', title: '24 Years', text: 'Of choral excellence' }
]

const stats = [
  { to: 24, suffix: '', label: 'Years of Harmony' },
  { to: 20, suffix: '+', label: 'Countries Visited' },
  { to: 40, suffix: '+', label: 'Choir Members' },
  { to: 120, suffix: '+', label: 'Concerts Performed' }
]

const marqueeItems = [
  'Renaissance', 'Baroque', 'Romantic', 'Contemporary',
  'A Cappella', 'Madrigals', 'Indonesian Repertoire', 'Sacred Works'
]

const handleCardSpotlight = (e) => {
  const rect = e.currentTarget.getBoundingClientRect()
  e.currentTarget.style.setProperty('--mx', `${e.clientX - rect.left}px`)
  e.currentTarget.style.setProperty('--my', `${e.clientY - rect.top}px`)
}
</script>

<template>
  <div>
    <HeroSection />

    <!-- Marquee -->
    <div class="marquee-wrap" aria-hidden="true">
      <div class="marquee-track">
        <span v-for="(item, i) in [...marqueeItems, ...marqueeItems]" :key="i" class="marquee-item">
          {{ item }} <i class="bi bi-music-note"></i>
        </span>
    </div>
    </div>

    <!-- Highlights -->
    <section id="highlights" class="section-padding bg-dark-custom">
      <div class="container">
        <div class="row g-4">
          <div
            v-for="(item, index) in highlights"
            :key="item.title"
            class="col-6 col-lg-3"
            v-reveal="{ delay: index * 100 }"
          >
            <div class="elegant-card p-4 text-center h-100" @mousemove="handleCardSpotlight">
              <span class="card-spotlight"></span>
              <i :class="`bi ${item.icon} text-gold`" style="font-size: 2.5rem;"></i>
              <h5 class="text-cream mt-3 mb-1">{{ item.title }}</h5>
              <p class="small text-muted mb-0">{{ item.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats -->
    <section class="section-padding bg-darker-custom" style="padding-block: 4.5rem;">
      <div class="container">
        <div class="row g-4 text-center">
          <div
            v-for="(stat, index) in stats"
            :key="stat.label"
            class="col-6 col-lg-3"
            v-reveal="{ delay: index * 100 }"
          >
            <div class="stat-number">
              <CountUp :to="stat.to" :suffix="stat.suffix" />
            </div>
            <p class="text-uppercase small mt-2 mb-0" style="letter-spacing: 0.25em; color: var(--bms-muted);">
              {{ stat.label }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- About preview -->
    <section class="section-padding bg-dark-custom">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-6" v-reveal.left>
            <div class="position-relative">
              <div class="elegant-card p-5 text-center" @mousemove="handleCardSpotlight">
                <span class="card-spotlight"></span>
                <i class="bi bi-music-note-beamed text-gold" style="font-size: 8rem; opacity: 0.3;"></i>
                <div class="position-absolute top-50 start-50 translate-middle">
                  <h3 class="text-gold fw-bold mb-0">Since</h3>
                  <h1 class="text-cream fw-bold" style="font-size: 5rem;">2001</h1>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6" v-reveal.right>
            <p class="text-gold text-uppercase mb-2" style="letter-spacing: 0.3em; font-size: 0.8rem;">
              About the Choir
            </p>
            <h2 class="text-cream fw-bold mb-4">The Art of Harmonious Excellence</h2>
            <div class="gold-divider-left"></div>
            <p class="mb-4">
              Batavia Madrigal Singers is a premier chamber choir based in Jakarta, Indonesia.
              Founded in 2001, we have dedicated ourselves to the performance of choral music
              from the Renaissance to contemporary works, with a special focus on Indonesian
              repertoire.
            </p>
            <p class="mb-4">
              Our name "Batavia" honors the historic name of Jakarta, connecting our modern
              artistry with the rich cultural heritage of our city. Through meticulous
              musicianship and passionate performance, we strive to touch hearts and
              elevate the human spirit.
            </p>
            <RouterLink to="/about" class="btn btn-outline-gold">Read More</RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Upcoming events -->
    <section class="section-padding bg-darker-custom">
      <div class="container">
        <SectionTitle subtitle="Concerts" title="Upcoming Performances" />

        <div class="row g-4 justify-content-center">
          <div
            v-for="(event, index) in choirStore.events.filter(e => e.status === 'upcoming')"
            :key="event.id"
            class="col-lg-4 col-md-6"
            v-reveal="{ delay: index * 150 }"
          >
            <div class="elegant-card p-4 h-100" @mousemove="handleCardSpotlight">
              <span class="card-spotlight"></span>
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="event-date-block text-gold">
                  <div class="fs-4 fw-bold">{{ new Date(event.date).getDate() }}</div>
                  <div class="text-uppercase small" style="letter-spacing: 0.1em;">
                    {{ new Date(event.date).toLocaleString('en', { month: 'short' }) }}
                  </div>
                </div>
                <span class="badge-gold">Upcoming</span>
              </div>
              <h5 class="text-cream mb-2">{{ event.title }}</h5>
              <p class="small mb-3" style="line-height: 1.7;">{{ event.description }}</p>
              <div class="small mb-3 text-muted">
                <div class="mb-1"><i class="bi bi-geo-alt text-gold me-2"></i>{{ event.venue }}</div>
                <div><i class="bi bi-clock text-gold me-2"></i>{{ event.time }} WIB</div>
              </div>
              <RouterLink to="/events" class="text-gold text-decoration-none small">
                Details <i class="bi bi-arrow-right ms-1"></i>
              </RouterLink>
            </div>
          </div>
        </div>

        <div class="text-center mt-5" v-reveal>
          <RouterLink to="/events" class="btn btn-gold">View All Events</RouterLink>
        </div>
      </div>
    </section>

    <!-- Quote -->
    <section class="section-padding bg-dark-custom">
      <div class="container">
        <div v-reveal class="lux-quote">
          <span class="quote-mark">&ldquo;</span>
          <blockquote>
            Music is the divine way to tell beautiful, poetic things to the heart.
          </blockquote>
          <div class="gold-divider"></div>
          <p class="text-uppercase small mb-0" style="letter-spacing: 0.3em; color: var(--bms-gold);">
            Pablo Casals
          </p>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="section-padding bg-darker-custom">
      <div class="container">
        <div class="elegant-card p-5 text-center" v-reveal @mousemove="handleCardSpotlight">
          <span class="card-spotlight"></span>
          <h2 class="text-cream fw-bold mb-3">Join Our Journey</h2>
          <div class="gold-divider"></div>
          <p class="mb-4 mx-auto" style="max-width: 600px;">
            Whether you're a singer looking for a musical home, an event organizer
            seeking world-class entertainment, or a music lover wanting to support
            the arts — we'd love to hear from you.
          </p>
          <RouterLink to="/contact" class="btn btn-gold">Get in Touch</RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>
