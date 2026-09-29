<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { getIntersectionObserver } from '../utils/observer.js'

const props = defineProps({
  to: { type: Number, required: true },
  duration: { type: Number, default: 1800 },
  suffix: { type: String, default: '' }
})

const display = ref('0')
const el = ref(null)
let observer = null
let started = false

const easeOutQuart = (t) => 1 - Math.pow(1 - t, 4)

const start = () => {
  if (started) return
  started = true
  const t0 = performance.now()
  const tick = (now) => {
    const p = Math.min((now - t0) / props.duration, 1)
    display.value = Math.round(props.to * easeOutQuart(p)).toLocaleString('en-US')
    if (p < 1) requestAnimationFrame(tick)
  }
  requestAnimationFrame(tick)
}

const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches

onMounted(() => {
  if (reduced || !el.value) {
    display.value = props.to.toLocaleString('en-US')
    return
  }
  observer = getIntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting) {
        start()
        observer.disconnect()
      }
    },
    { threshold: 0.4 }
  )
  observer.observe(el.value)
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
  <span ref="el">{{ display }}{{ suffix }}</span>
</template>
