<template>
    <div class="members-page">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2">
                            <i class="bi bi-people-fill me-2"></i>
                            Choir Members
                        </h2>
                        <p class="text-white-50">Manage active members and sections</p>
                    </div>
                    <button class="btn btn-gradient" @click="showAddModal = true">
                        <i class="bi bi-person-plus me-2"></i>
                        Add Member
                    </button>
                </div>
            </div>
        </div>

        <!-- Section Overview -->
        <div class="row g-3 mb-4">
            <div v-for="(members, section) in membersBySection" :key="section" class="col-md-3 col-sm-6">
                <div class="section-card" @click="selectedSection = section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{ section }}</h6>
                            <small class="text-white-50">{{ members.length }} members</small>
                        </div>
                        <div class="section-icon">
                            <i class="bi bi-music-note-beamed"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Grid -->
        <div class="row g-4">
            <div v-for="member in filteredMembers" :key="member.id" class="col-lg-4 col-md-6">
                <div class="member-card luxury-card">
                    <div class="d-flex align-items-center mb-3">
                        <img :src="member.avatar" :alt="member.name" class="member-avatar me-3">
                        <div>
                            <h5 class="mb-1">{{ member.name }}</h5>
                            <p class="mb-0 text-white-50">{{ member.role }}</p>
                        </div>
                        <span class="badge ms-auto" :class="member.status === 'active' ? 'bg-success' : 'bg-secondary'">
                            {{ member.status }}
                        </span>
                    </div>

                    <div class="member-details">
                        <div class="detail-item">
                            <i class="bi bi-envelope"></i>
                            <span>{{ member.email }}</span>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-telephone"></i>
                            <span>{{ member.phone }}</span>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-calendar-check"></i>
                            <span>Joined {{ formatDate(member.joinDate) }}</span>
                        </div>
                    </div>

                    <div class="member-stats mt-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-white-50">Performances</small>
                                <h4 class="mb-0">{{ member.performances }}</h4>
                            </div>
                            <div>
                                <small class="text-white-50">Section</small>
                                <h4 class="mb-0">{{ member.section }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="member-actions mt-3">
                        <button class="btn btn-sm btn-outline-info me-2" @click="viewMemberDetails(member)">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="deactivateMember(member.id)">
                            <i class="bi bi-person-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Member Modal -->
        <div v-if="showAddModal" class="modal-overlay">
            <div class="modal-content luxury-card">
                <h4 class="fw-bold mb-4">Add New Member</h4>
                <form @submit.prevent="addNewMember">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" v-model="newMember.name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" v-model="newMember.role" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section</label>
                        <select class="form-select" v-model="newMember.section" required>
                            <option value="Soprano">Soprano</option>
                            <option value="Alto">Alto</option>
                            <option value="Tenor">Tenor</option>
                            <option value="Bass">Bass</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" v-model="newMember.email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" v-model="newMember.phone">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient flex-grow-1">Add Member</button>
                        <button type="button" class="btn btn-outline-light"
                            @click="showAddModal = false">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useMembersStore } from '../stores/members'

const membersStore = useMembersStore()
const showAddModal = ref(false)
const selectedSection = ref(null)

const newMember = ref({
    name: '',
    role: '',
    section: 'Soprano',
    email: '',
    phone: '',
    avatar: 'https://i.pravatar.cc/150?img=' + Math.floor(Math.random() * 70)
})

const membersBySection = computed(() => membersStore.membersBySection)

const filteredMembers = computed(() => {
    if (!selectedSection.value) return membersStore.members
    return membersStore.members.filter(m => m.section === selectedSection.value)
})

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    })
}

function addNewMember() {
    membersStore.addMember({ ...newMember.value })
    showAddModal.value = false
    newMember.value = {
        name: '',
        role: '',
        section: 'Soprano',
        email: '',
        phone: '',
        avatar: 'https://i.pravatar.cc/150?img=' + Math.floor(Math.random() * 70)
    }
}

function deactivateMember(id) {
    if (confirm('Are you sure you want to deactivate this member?')) {
        membersStore.updateMemberStatus(id, 'inactive')
    }
}

function viewMemberDetails(member) {
    alert(`Viewing details for ${member.name}`)
}
</script>

<style scoped>
.section-card {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 12px;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.section-card:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
}

.section-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-gradient);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.member-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid var(--primary-color);
}

.member-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.detail-item i {
    width: 20px;
    margin-right: 0.5rem;
    color: var(--primary-color);
}

.member-stats {
    background: rgba(102, 126, 234, 0.05);
    padding: 1rem;
    border-radius: 8px;
}

.member-actions {
    display: flex;
    gap: 0.5rem;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.modal-content {
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.form-control,
.form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 8px;
}

.form-control:focus,
.form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>