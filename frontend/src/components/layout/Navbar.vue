<template>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <button class="btn btn-link text-white d-md-none" @click="toggleSidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link text-white dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <span class="badge bg-danger rounded-pill notification-badge">3</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <li>
                            <h6 class="dropdown-header">Notifications</h6>
                        </li>
                        <li><a class="dropdown-item" href="#">Rehearsal tomorrow at 6 PM</a></li>
                        <li><a class="dropdown-item" href="#">New member joined</a></li>
                        <li><a class="dropdown-item" href="#">Event reminder: Annual Concert</a></li>
                    </ul>
                </div>

                <div class="dropdown ms-3">
                    <button v-if="user" class="btn btn-link text-white dropdown-toggle d-flex align-items-center"
                        data-bs-toggle="dropdown">
                        <img :src="user.avatar || defaultAvatar" :alt="user.name" class="nav-avatar me-2">
                        <span class="d-none d-md-inline">{{ user.name }}</span>
                    </button>
                    <button v-else class="btn btn-link text-white" @click="goToLogin">
                        <i class="bi bi-person-circle fs-4"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><router-link to="/profile" class="dropdown-item">Profile</router-link></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
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
    document.querySelector('.sidebar')?.classList.toggle('show')
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
    background: rgba(26, 26, 46, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1rem 2rem;
}

.nav-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid var(--primary-color);
}

.notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    font-size: 0.6rem;
}

.notification-dropdown {
    background: var(--card-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.notification-dropdown .dropdown-item {
    color: var(--text-primary);
}

.notification-dropdown .dropdown-item:hover {
    background: rgba(102, 126, 234, 0.1);
}
</style>