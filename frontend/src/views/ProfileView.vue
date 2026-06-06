<template>
    <div class="profile-page">
        <div class="row g-4">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="luxury-card text-center">
                    <img :src="user.avatar" :alt="user.name" class="profile-avatar mb-3">
                    <h3 class="fw-bold mb-1">{{ user.name }}</h3>
                    <p class="text-primary fw-semibold mb-3">{{ user.role }}</p>

                    <div class="profile-info text-start mt-4">
                        <div class="info-item">
                            <i class="bi bi-envelope-fill"></i>
                            <span>{{ user.email }}</span>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-telephone-fill"></i>
                            <span>{{ user.phone }}</span>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>{{ user.location }}</span>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-calendar-check-fill"></i>
                            <span>Joined {{ formatDate(user.joinDate) }}</span>
                        </div>
                    </div>

                    <button class="btn btn-gradient w-100 mt-4" @click="editMode = !editMode">
                        <i class="bi bi-pencil-square me-2"></i>
                        {{ editMode ? 'Cancel Edit' : 'Edit Profile' }}
                    </button>
                </div>
            </div>

            <!-- Profile Details/Edit Form -->
            <div class="col-lg-8">
                <div class="luxury-card">
                    <h4 class="fw-bold mb-4">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        {{ editMode ? 'Edit Profile' : 'Profile Details' }}
                    </h4>

                    <form v-if="editMode" @submit.prevent="saveProfile">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" v-model="editForm.name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" v-model="editForm.email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" v-model="editForm.phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" v-model="editForm.location">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Bio</label>
                                <textarea class="form-control" rows="4" v-model="editForm.bio"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-gradient">
                                    <i class="bi bi-check-lg me-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>

                    <div v-else>
                        <div class="mb-4">
                            <h6 class="text-primary fw-semibold">Bio</h6>
                            <p class="text-white-50">{{ user.bio }}</p>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-primary fw-semibold">Specialization</h6>
                            <p class="text-white-50">{{ user.specialization }}</p>
                        </div>

                        <div>
                            <h6 class="text-primary fw-semibold">Achievements</h6>
                            <ul class="achievement-list">
                                <li v-for="(achievement, index) in user.achievements" :key="index">
                                    <i class="bi bi-trophy-fill text-warning me-2"></i>
                                    {{ achievement }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="luxury-card mt-4">
                    <h4 class="fw-bold mb-4">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Activity Overview
                    </h4>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="activity-stat">
                                <i class="bi bi-calendar-check text-primary"></i>
                                <div class="ms-2">
                                    <h5 class="mb-0">45</h5>
                                    <small class="text-white-50">Events Attended</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="activity-stat">
                                <i class="bi bi-mic-fill text-success"></i>
                                <div class="ms-2">
                                    <h5 class="mb-0">30</h5>
                                    <small class="text-white-50">Performances</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="activity-stat">
                                <i class="bi bi-clock-history text-info"></i>
                                <div class="ms-2">
                                    <h5 class="mb-0">120</h5>
                                    <small class="text-white-50">Practice Hours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const user = computed(() => authStore.user)
const editMode = ref(false)

const editForm = reactive({
    name: '',
    email: '',
    phone: '',
    location: '',
    bio: ''
})

watch(user, (newUser) => {
    if (newUser) {
        editForm.name = newUser.name ?? ''
        editForm.email = newUser.email ?? ''
        editForm.phone = newUser.phone ?? ''
        editForm.location = newUser.location ?? ''
        editForm.bio = newUser.bio ?? ''
    }
}, { immediate: true })

onMounted(() => {
    if (authStore.token && !authStore.user) {
        authStore.fetchProfile()
    }
})

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    })
}

async function saveProfile() {
    try {
        await authStore.updateProfile({ ...editForm })
        editMode.value = false
    } catch (error) {
        alert('Failed to save profile: ' + error.message)
    }
}
</script>

<style scoped>
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid var(--primary-color);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.info-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.info-item:last-child {
    border-bottom: none;
}

.info-item i {
    width: 25px;
    color: var(--primary-color);
    font-size: 1.1rem;
}

.achievement-list {
    list-style: none;
    padding: 0;
}

.achievement-list li {
    padding: 0.5rem 0;
    color: var(--text-secondary);
}

.activity-stat {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
}

.activity-stat i {
    font-size: 2rem;
}

.form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 8px;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-label {
    color: var(--text-secondary);
    font-weight: 500;
}
</style>