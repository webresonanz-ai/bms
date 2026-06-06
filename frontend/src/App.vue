<template>
  <div class="app-container">
    <Sidebar />
    <div class="main-content" :class="{ 'sidebar-collapsed': isCollapsed }">
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
import { useSidebarCollapse } from './composables/useSidebarCollapse'

const isMobile = ref(false)
const isSidebarOpen = ref(false)
const { isCollapsed } = useSidebarCollapse()

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
  min-height: 100dvh;
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
}

.main-content {
  margin-left: var(--sidebar-width);
  flex: 1;
  display: flex;
  flex-direction: column;
  background: var(--dark-bg);
  transition: margin-left 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  box-sizing: border-box;
  max-width: 100vw;
  overflow-x: hidden;
  width: 100%;
}

.main-content.sidebar-collapsed {
  margin-left: var(--sidebar-collapsed-width);
}

.content-area {
  padding: 2rem;
  flex: 1;
  background: var(--dark-bg);
  box-sizing: border-box;
  width: 100%;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }

  .content-area {
    padding: 1rem;
  }
}

@media (max-width: 400px) {
  .content-area {
    padding: 0.75rem;
  }
}
</style>