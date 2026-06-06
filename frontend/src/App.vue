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
  </div>
</template>

<script setup>
import Sidebar from './components/layout/Sidebar.vue'
import Navbar from './components/layout/Navbar.vue'
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
}

.content-area {
  padding: 2rem;
  flex: 1;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
}
</style>