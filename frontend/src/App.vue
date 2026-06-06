<template>
  <div class="app-container">
    <Sidebar />
    <div class="main-content">
      <Navbar />
      <div class="content-area">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </div>
    </div>
    <div v-if="isMobile" class="sidebar-overlay" :class="{ active: isSidebarOpen }" @click="closeSidebar"></div>
  </div>
</template>

<script setup>
import Sidebar from './components/layout/Sidebar.vue'
import Navbar from './components/layout/Navbar.vue'
import { ref, onMounted, onUnmounted } from 'vue'

const isMobile = ref(false)
const isSidebarOpen = ref(false)

function checkMobile() {
  isMobile.value = window.innerWidth <= 768
}

function closeSidebar() {
  document.body.classList.remove('sidebar-open')
  isSidebarOpen.value = false
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<style>
.app-container {
  display: flex;
  min-height: 100vh;
}

.main-content {
  margin-left: var(--sidebar-width);
  flex: 1;
  display: flex;
  flex-direction: column;
  background: var(--dark-bg);
  transition: margin-left 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.content-area {
  padding: 2rem;
  flex: 1;
  background: var(--dark-bg);
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
  
  .content-area {
    padding: 1rem;
  }
}
</style>