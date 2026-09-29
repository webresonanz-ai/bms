<script setup>
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const submitted = ref(false)
const sending = ref(false)

const handleSubmit = () => {
  sending.value = true
  // Simulate a network round-trip so the button's loading state is visible
  setTimeout(() => {
    sending.value = false
    submitted.value = true
    setTimeout(() => {
      submitted.value = false
      form.value = { name: '', email: '', subject: '', message: '' }
    }, 3500)
  }, 900)
}
</script>

<template>
  <div>
    <div class="page-header">
      <div class="container">
        <p class="hero-subtitle mb-3 fade-in">Get in Touch</p>
        <h1 class="hero-title fade-in-up">Contact Us</h1>
        <div class="gold-divider"></div>
      </div>
    </div>

    <section class="section-padding bg-dark-custom">
      <div class="container">
        <div class="row g-5">
          <div class="col-lg-5" v-reveal.left>
            <h3 class="text-cream fw-bold mb-3">Let's Connect</h3>
            <div class="gold-divider-left"></div>
            <p class="mb-5">
              For booking inquiries, auditions, collaborations, or simply to
              say hello — we'd be delighted to hear from you.
            </p>

            <div class="mb-4 d-flex align-items-start" v-reveal>
              <div class="me-3">
                <i class="bi bi-geo-alt text-gold fs-4"></i>
              </div>
              <div>
                <h6 class="text-cream mb-1">Location</h6>
                <p class="small text-muted mb-0">Jakarta, Indonesia</p>
              </div>
            </div>

            <div class="mb-4 d-flex align-items-start" v-reveal="{ delay: 100 }">
              <div class="me-3">
                <i class="bi bi-envelope text-gold fs-4"></i>
              </div>
              <div>
                <h6 class="text-cream mb-1">Email</h6>
                <p class="small text-muted mb-0">info@bataviamadrigal.com</p>
              </div>
            </div>

            <div class="mb-4 d-flex align-items-start" v-reveal="{ delay: 200 }">
              <div class="me-3">
                <i class="bi bi-telephone text-gold fs-4"></i>
              </div>
              <div>
                <h6 class="text-cream mb-1">Phone</h6>
                <p class="small text-muted mb-0">+62 21 1234 5678</p>
              </div>
            </div>

            <div class="d-flex gap-2 mt-5">
              <a href="#" class="social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
              <a href="#" class="social-icon" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
              <a href="#" class="social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-icon" aria-label="Spotify"><i class="bi bi-spotify"></i></a>
            </div>
          </div>

          <div class="col-lg-7" v-reveal.right>
            <div class="elegant-card p-4 p-md-5">
              <h4 class="text-cream mb-4">Send a Message</h4>

              <Transition name="fade">
                <div
                  v-if="submitted"
                  class="alert badge-gold d-flex align-items-center"
                  role="status"
                >
                  <i class="bi bi-check-circle me-2"></i>Thank you! Your message has been sent.
                </div>
              </Transition>

              <form @submit.prevent="handleSubmit">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label-lux" for="contact-name">Name</label>
                    <input
                      id="contact-name"
                      v-model="form.name"
                      type="text"
                      class="form-control lux"
                      placeholder="Your full name"
                      required
                    />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-lux" for="contact-email">Email</label>
                    <input
                      id="contact-email"
                      v-model="form.email"
                      type="email"
                      class="form-control lux"
                      placeholder="you@example.com"
                      required
                    />
                  </div>
                  <div class="col-12">
                    <label class="form-label-lux" for="contact-subject">Subject</label>
                    <input
                      id="contact-subject"
                      v-model="form.subject"
                      type="text"
                      class="form-control lux"
                      placeholder="Booking, audition, collaboration..."
                      required
                    />
                  </div>
                  <div class="col-12">
                    <label class="form-label-lux" for="contact-message">Message</label>
                    <textarea
                      id="contact-message"
                      v-model="form.message"
                      rows="5"
                      class="form-control lux"
                      placeholder="Tell us about your event or inquiry..."
                      required
                    ></textarea>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-gold w-100" :disabled="sending">
                      <span v-if="sending" class="d-inline-flex align-items-center gap-2">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Sending...
                      </span>
                      <span v-else>Send Message</span>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
