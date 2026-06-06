<template>
    <div class="sidebar">
        <div class="sidebar-header p-4">
            <div class="d-flex align-items-center">
                <div class="logo-icon me-3">
                    <i class="bi bi-music-note-beamed fs-2"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold luxury-heading">BMS</h5>
                    <small class="luxury-text-muted">Management System</small>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <router-link to="/" class="nav-item" exact-active-class="active" @click="closeSidebar">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </router-link>

            <router-link to="/events" class="nav-item" active-class="active" @click="closeSidebar">
                <i class="bi bi-calendar-event-fill"></i>
                <span>Events</span>
            </router-link>

            <router-link to="/members" class="nav-item" active-class="active" @click="closeSidebar">
                <i class="bi bi-people-fill"></i>
                <span>Members</span>
            </router-link>

            <router-link to="/attendance" class="nav-item" active-class="active" @click="closeSidebar">
                <i class="bi bi-check2-circle"></i>
                <span>Attendance</span>
            </router-link>

            <router-link to="/about" class="nav-item" active-class="active" @click="closeSidebar">
                <i class="bi bi-info-circle-fill"></i>
                <span>About Us</span>
            </router-link>

            <router-link to="/profile" class="nav-item" active-class="active" @click="closeSidebar">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </router-link>
        </nav>

        <div class="sidebar-footer p-4">
            <div v-if="user" class="user-card">
                <img :src="user.avatar || defaultAvatar" :alt="user.name" class="avatar">
                <div class="user-info">
                    <h6 class="mb-0 luxury-event-title">{{ user.name }}</h6>
                    <small class="luxury-text-muted">{{ user.role }}</small>
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
const defaultAvatar = 'https://i.pravatar.cc/150?img=5'
</script>

<style scoped>
.sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background: linear-gradient(180deg, var(--dark-bg) 0%, var(--card-bg) 100%);
    border-right: 1px solid rgba(166, 123, 91, 0.2);
    display: flex;
    flex-direction: column;
    z-index: 1000;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    body.sidebar-open .sidebar {
        transform: translateX(0);
    }
}

.logo-icon {
    width: 45px;
    height: 45px;
    background: var(--primary-gradient);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 4px 16px rgba(166, 123, 91, 0.3);
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
    border-radius: 14px;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    font-weight: 500;
    border: 1px solid transparent;
}

.nav-item i {
    font-size: 1.25rem;
    margin-right: 1rem;
    width: 20px;
    text-align: center;
}

.nav-item:hover {
    background: rgba(166, 123, 91, 0.12);
    color: var(--text-primary);
    transform: translateX(8px);
    border-color: rgba(166, 123, 91, 0.2);
}

.nav-item.active {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 6px 20px rgba(166, 123, 91, 0.3);
    border-color: rgba(212, 175, 55, 0.3);
}

.user-card {
    display: flex;
    align-items: center;
    background: rgba(166, 123, 91, 0.08);
    padding: 0.75rem;
    border-radius: 14px;
    border: 1px solid rgba(166, 123, 91, 0.2);
    transition: all 0.3s ease;
}

.user-card:hover {
    background: rgba(166, 123, 91, 0.12);
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 0.75rem;
    border: 2px solid var(--primary-color);
    box-shadow: 0 4px 12px rgba(166, 123, 91, 0.2);
}

.luxury-heading {
    color: var(--text-primary);
}

.luxury-text-muted {
    color: var(--text-secondary);
}

.luxury-event-title {
    color: var(--text-primary);
}

.btn-earth-outline {
    background: transparent;
    border: 1px solid rgba(166, 123, 91, 0.4);
    color: var(--text-secondary);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-earth-outline:hover {
    background: rgba(166, 123, 91, 0.15);
    color: var(--text-primary);
}
</style>