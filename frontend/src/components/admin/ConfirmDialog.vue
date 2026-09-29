<script setup>
/**
 * ConfirmDialog — small destructive-action confirmation modal.
 * Teleports to body, locks scroll while open.
 */
import { watch } from 'vue'

const props = defineProps({
  show:    { type: Boolean, required: true },
  title:   { type: String,  default: 'Confirm Delete' },
  message: { type: String,  required: true },
  busy:    { type: Boolean, default: false },
})

const emit = defineEmits(['confirm', 'cancel'])

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
        role="alertdialog"
        aria-modal="true"
        :aria-label="title"
        @click.self="emit('cancel')"
        @keydown.esc.stop="emit('cancel')"
      >
        <div class="admin-modal-panel confirm">
          <div class="admin-modal-body text-center py-4 px-4">

            <!-- Warning icon -->
            <div class="confirm-icon mb-3">
              <i class="bi bi-exclamation-triangle-fill"></i>
            </div>

            <h5 class="text-cream mb-2">{{ title }}</h5>
            <p class="small mb-4" style="max-width:300px; margin-inline:auto; color:var(--bms-muted-strong);">
              {{ message }}
            </p>

            <div class="d-flex justify-content-center gap-3">
              <button
                type="button"
                class="btn btn-outline-gold btn-sm-admin"
                :disabled="busy"
                @click="emit('cancel')"
              >
                Cancel
              </button>
              <button
                type="button"
                class="btn btn-danger-lux btn-sm-admin"
                :disabled="busy"
                @click="emit('confirm')"
              >
                <span
                  v-if="busy"
                  class="spinner-border spinner-border-sm me-2"
                  role="status"
                  aria-hidden="true"
                ></span>
                Delete
              </button>
            </div>

          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.confirm-icon {
  width: 64px;
  height: 64px;
  margin-inline: auto;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  color: #e8a0b0;
  background: rgba(139, 30, 63, 0.16);
  border: 1px solid rgba(139, 30, 63, 0.45);
  box-shadow: 0 0 0 6px rgba(139, 30, 63, 0.07);
}
</style>
