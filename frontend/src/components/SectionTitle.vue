<script setup>
defineProps({
  subtitle: String,
  title: String
})
</script>

<template>
  <div v-reveal class="text-center mb-5">
    <p class="text-gold text-uppercase mb-2" style="letter-spacing: 0.3em; font-size: 0.8rem;">
      {{ subtitle }}
    </p>
    <h2 class="text-cream fw-bold mb-0">{{ title }}</h2>
    <div class="gold-divider"></div>
  </div>
</template>
