<script setup>
import SectionTitle from '../components/SectionTitle.vue'
import { useChoirStore } from '../stores/choir'

const choirStore = useChoirStore()
</script>

<template>
  <div>
    <div class="page-header">
      <div class="container">
        <p class="hero-subtitle mb-3 fade-in">Our Story</p>
        <h1 class="hero-title fade-in-up">About Us</h1>
        <div class="gold-divider"></div>
      </div>
    </div>

    <!-- Mission -->
    <section class="section-padding bg-dark-custom">
      <div class="container">
        <div class="row g-5 align-items-center">
          <div class="col-lg-6" v-reveal.left>
            <p class="text-gold text-uppercase mb-2" style="letter-spacing: 0.3em; font-size: 0.8rem;">Our Mission</p>
            <h2 class="text-cream fw-bold mb-4">Elevating Choral Artistry</h2>
            <div class="gold-divider-left"></div>
            <p class="mb-4">
              We are committed to the pursuit of choral excellence, performing
              works that span centuries and continents. Our mission is to bring
              the transformative power of choral music to audiences in Indonesia
              and around the world.
            </p>
            <p>
              Through innovative programming, collaborations with renowned artists,
              and a dedication to nurturing young talent, we continue to shape
              the future of choral music in our region.
            </p>
          </div>
          <div class="col-lg-6" v-reveal.right>
            <div class="row g-3">
              <div class="col-6">
                <div class="elegant-card p-4 text-center">
                  <i class="bi bi-award text-gold" style="font-size: 2rem;"></i>
                  <h6 class="text-cream mt-3 mb-0">Award-Winning</h6>
                </div>
              </div>
              <div class="col-6">
                <div class="elegant-card p-4 text-center">
                  <i class="bi bi-globe-americas text-gold" style="font-size: 2rem;"></i>
                  <h6 class="text-cream mt-3 mb-0">World Tours</h6>
                </div>
              </div>
              <div class="col-6">
                <div class="elegant-card p-4 text-center">
                  <i class="bi bi-mortarboard text-gold" style="font-size: 2rem;"></i>
                  <h6 class="text-cream mt-3 mb-0">Education</h6>
                </div>
              </div>
              <div class="col-6">
                <div class="elegant-card p-4 text-center">
                  <i class="bi bi-heart text-gold" style="font-size: 2rem;"></i>
                  <h6 class="text-cream mt-3 mb-0">Community</h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Timeline -->
    <section class="section-padding bg-darker-custom">
      <div class="container">
        <SectionTitle subtitle="Our Journey" title="Milestones Through the Years" />

        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div
              v-for="(milestone, index) in choirStore.milestones"
              :key="milestone.year"
              class="timeline-item"
              v-reveal="{ delay: index * 80 }"
            >
              <div class="text-gold fw-bold mb-1" style="font-size: 1.5rem;">{{ milestone.year }}</div>
              <h5 class="text-cream mb-2">{{ milestone.title }}</h5>
              <p class="small mb-0">{{ milestone.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Values -->
    <section class="section-padding bg-dark-custom">
      <div class="container">
        <SectionTitle subtitle="What Drives Us" title="Our Core Values" />

        <div class="row g-4">
          <div class="col-lg-4 col-md-6" v-reveal="{ delay: 0 }">
            <div class="elegant-card p-5 h-100 text-center">
              <i class="bi bi-stars text-gold" style="font-size: 3rem;"></i>
              <h4 class="text-cream mt-4 mb-3">Excellence</h4>
              <p class="small mb-0">
                We pursue the highest standards of musicality, continuously refining
                our craft through dedicated rehearsal and study.
              </p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6" v-reveal="{ delay: 150 }">
            <div class="elegant-card p-5 h-100 text-center">
              <i class="bi bi-people text-gold" style="font-size: 3rem;"></i>
              <h4 class="text-cream mt-4 mb-3">Community</h4>
              <p class="small mb-0">
                We believe music builds bridges between people, cultures, and
                generations, fostering understanding and friendship.
              </p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6" v-reveal="{ delay: 300 }">
            <div class="elegant-card p-5 h-100 text-center">
              <i class="bi bi-book text-gold" style="font-size: 3rem;"></i>
              <h4 class="text-cream mt-4 mb-3">Heritage</h4>
              <p class="small mb-0">
                We honor the choral tradition while championing Indonesian
                repertoire and supporting contemporary composers.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
