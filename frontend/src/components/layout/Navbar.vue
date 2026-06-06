<template>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <button class="btn btn-link text-white d-md-none sidebar-toggle" @click="toggleSidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown me-2 d-none d-md-block">
                    <button class="btn btn-link text-white position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <li>
                            <h6 class="dropdown-header luxury-heading">Notifications</h6>
                        </li>
                        <li><a class="dropdown-item luxury-text-muted" href="#">Rehearsal tomorrow at 6 PM</a></li>
                        <li><a class="dropdown-item luxury-text-muted" href="#">New member joined</a></li>
                        <li><a class="dropdown-item luxury-text-muted" href="#">Event reminder: Annual Concert</a></li>
                    </ul>
                </div>

                <div class="dropdown ms-3">
                    <button v-if="user" class="btn btn-link text-white dropdown-toggle d-flex align-items-center user-dropdown"
                        data-bs-toggle="dropdown">
                        <img :src="user.avatar || defaultAvatar" :alt="user.name" class="nav-avatar me-2">
                        <span class="d-none d-md-inline luxury-event-title">{{ user.name }}</span>
                    </button>
                    <button v-else class="btn btn-link text-white" @click="goToLogin">
                        <i class="bi bi-person-circle fs-4"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><router-link to="/profile" class="dropdown-item luxury-text-muted">Profile</router-link></li>
                        <li><a class="dropdown-item luxury-text-muted" href="#">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#" @click="logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth'
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const user = computed(() => authStore.user)
const defaultAvatar = 'https://i.pravatar.cc/150?img=5'

const toggleSidebar = () => {
    document.body.classList.toggle('sidebar-open')
}

const goToLogin = () => {
    router.push('/login')
}

const logout = () => {
    authStore.logout()
    router.push('/login')
}
</script>

<style scoped>
.navbar {
    background: rgba(26, 18, 10, 0.95);
    backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(166, 123, 91, 0.2);
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
}

.sidebar-toggle {
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.sidebar-toggle:hover {
    color: var(--gold-text);
    transform: scale(1.1);
}

.nav-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid var(--primary-color);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 0.6rem;
    background: var(--danger-color);
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(160, 82, 45, 0.4);
}

.notification-dropdown {
    background: var(--card-gradient);
    border: 1px solid rgba(166, 123, 91, 0.3);
    border-radius: 16px;
    backdrop-filter: blur(15px);
}

.notification-dropdown .dropdown-item {
    color: var(--text-primary);
    transition: all 0.2s ease;
}

.notification-dropdown .dropdown-item:hover {
    background: rgba(166, 123, 91, 0.15);
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

.user-dropdown {
    transition: all 0.3s ease;
}

.user-dropdown:hover {
    color: var(--gold-text);
}
</style>