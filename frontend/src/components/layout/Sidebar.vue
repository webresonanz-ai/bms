<template>
    <div class="sidebar">
        <div class="sidebar-header p-4">
            <div class="d-flex align-items-center">
                <div class="logo-icon me-3">
                    <i class="bi bi-music-note-beamed fs-2"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">BMS Choir</h5>
                    <small class="text-white-50">Management System</small>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <router-link to="/" class="nav-item" exact-active-class="active">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </router-link>

            <router-link to="/events" class="nav-item" active-class="active">
                <i class="bi bi-calendar-event-fill"></i>
                <span>Events</span>
            </router-link>

            <router-link to="/members" class="nav-item" active-class="active">
                <i class="bi bi-people-fill"></i>
                <span>Members</span>
            </router-link>

            <router-link to="/about" class="nav-item" active-class="active">
                <i class="bi bi-info-circle-fill"></i>
                <span>About Us</span>
            </router-link>

            <router-link to="/profile" class="nav-item" active-class="active">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </router-link>
        </nav>

        <div class="sidebar-footer p-4">
            <div class="user-card">
                <img :src="user.avatar" :alt="user.name" class="avatar">
                <div class="user-info">
                    <h6 class="mb-0">{{ user.name }}</h6>
                    <small class="text-white-50">{{ user.role }}</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth'
import { computed } from 'vue'

const authStore = useAuthStore()
const user = computed(() => authStore.user)
</script>

<style scoped>
.sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    z-index: 1000;
}

.logo-icon {
    width: 45px;
    height: 45px;
    background: var(--primary-gradient);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.sidebar-nav {
    flex: 1;
    padding: 1rem;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 0.875rem 1.25rem;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-item i {
    font-size: 1.25rem;
    margin-right: 1rem;
    width: 20px;
    text-align: center;
}

.nav-item:hover {
    background: rgba(102, 126, 234, 0.1);
    color: white;
    transform: translateX(5px);
}

.nav-item.active {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.user-card {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.05);
    padding: 0.75rem;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 0.75rem;
    border: 2px solid var(--primary-color);
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
}
</style>