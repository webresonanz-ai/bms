<script setup>
const year = new Date().getFullYear()
</script>

<template>
  <footer class="footer-custom">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4 col-md-6" v-reveal>
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-music-note-beamed text-gold fs-3 me-2"></i>
            <div>
              <div class="text-cream fw-bold" style="letter-spacing: 0.15em;">BATAVIA</div>
              <div class="text-gold" style="letter-spacing: 0.3em; font-size: 0.65rem;">MADRIGAL SINGERS</div>
            </div>
          </div>
          <p class="small" style="line-height: 1.8;">
            A premier chamber choir dedicated to the art of choral music,
            bringing harmonious excellence from Jakarta to the world.
          </p>
        </div>

        <div class="col-lg-2 col-md-6" v-reveal="{ delay: 100 }">
          <h6 class="text-gold text-uppercase mb-3" style="letter-spacing: 0.15em; font-size: 0.8rem;">Explore</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><RouterLink to="/about" class="text-muted text-decoration-none small footer-link">About Us</RouterLink></li>
            <li class="mb-2"><RouterLink to="/members" class="text-muted text-decoration-none small footer-link">Members</RouterLink></li>
            <li class="mb-2"><RouterLink to="/gallery" class="text-muted text-decoration-none small footer-link">Gallery</RouterLink></li>
            <li class="mb-2"><RouterLink to="/events" class="text-muted text-decoration-none small footer-link">Events</RouterLink></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6" v-reveal="{ delay: 200 }">
          <h6 class="text-gold text-uppercase mb-3" style="letter-spacing: 0.15em; font-size: 0.8rem;">Contact</h6>
          <ul class="list-unstyled small text-muted">
            <li class="mb-2"><i class="bi bi-geo-alt text-gold me-2"></i>Jakarta, Indonesia</li>
            <li class="mb-2"><i class="bi bi-envelope text-gold me-2"></i>info@bataviamadrigal.com</li>
            <li class="mb-2"><i class="bi bi-telephone text-gold me-2"></i>+62 21 1234 5678</li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6" v-reveal="{ delay: 300 }">
          <h6 class="text-gold text-uppercase mb-3" style="letter-spacing: 0.15em; font-size: 0.8rem;">Follow Us</h6>
          <div class="d-flex gap-2">
            <a href="#" class="social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="social-icon" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="#" class="social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="social-icon" aria-label="Spotify"><i class="bi bi-spotify"></i></a>
          </div>
        </div>
      </div>

      <div class="gold-divider"></div>

      <div class="text-center small text-muted">
        <p class="mb-0">&copy; {{ year }} Batavia Madrigal Singers. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>
