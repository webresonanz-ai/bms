import { getIntersectionObserver } from '../utils/observer.js'

const observer = getIntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-revealed')
        observer.unobserve(entry.target)
      }
    })
  },
  { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
)

export default {
  mounted(el, binding) {
    if (binding.modifiers.fade) el.classList.add('reveal-fade')
    else if (binding.modifiers.left) el.classList.add('reveal-left')
    else if (binding.modifiers.right) el.classList.add('reveal-right')
    else if (binding.modifiers.zoom) el.classList.add('reveal-zoom')
    else el.classList.add('reveal-up')

    if (binding.value?.delay) el.style.transitionDelay = `${binding.value.delay}ms`

    observer.observe(el)
  },
  unmounted(el) {
    observer.unobserve(el)
  }
}
