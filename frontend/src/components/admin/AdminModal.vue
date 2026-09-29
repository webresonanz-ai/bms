<script setup>
/**
 * AdminModal — reusable animated modal wrapper for admin dialogs.
 * Teleports to body, locks scroll while open, traps Escape key.
 */
import { watch } from 'vue'

const props = defineProps({
  show:  { type: Boolean, required: true },
  title: { type: String,  required: true },
  icon:  { type: String,  default: 'bi-pencil-square' },
  wide:  { type: Boolean, default: false },
})

const emit = defineEmits(['close'])

// Lock body scroll while modal is open
watch(() => props.show, (open) => {
  document.body.style.overflow = open ? 'hidden' : ''
}, { immediate: true })
</script>

<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="show"
        class="admin-modal-backdrop"
        role="dialog"
        aria-modal="true"
        :aria-label="title"
        @click.self="emit('close')"
        @keydown.esc.stop="emit('close')"
      >
        <div class="admin-modal-panel" :class="{ wide }">

          <!-- Header -->
          <div class="admin-modal-header">
            <div class="d-flex align-items-center gap-2">
              <div class="modal-header-icon">
                <i class="bi" :class="icon"></i>
              </div>
              <h5 class="text-cream mb-0">{{ title }}</h5>
            </div>
            <button
              type="button"
              class="admin-modal-close"
              aria-label="Close dialog"
              @click="emit('close')"
            >
              <i class="bi bi-x-lg"></i>
            </button>
          </div>

          <!-- Body -->
          <div class="admin-modal-body">
            <slot />
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-header-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(201, 169, 97, 0.12);
  border: 1px solid rgba(201, 169, 97, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--bms-gold);
  font-size: 0.9rem;
  flex-shrink: 0;
}

.admin-modal-close {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid rgba(201, 169, 97, 0.15);
  background: rgba(201, 169, 97, 0.04);
  color: var(--bms-muted);
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: color 0.25s ease, transform 0.25s ease, background 0.25s ease, border-color 0.25s ease;
}
.admin-modal-close:hover {
  color: var(--bms-gold);
  border-color: rgba(201, 169, 97, 0.4);
  background: rgba(201, 169, 97, 0.08);
  transform: rotate(90deg);
}
</style>
