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
                        <button class="btn btn-sm btn-outline-warning me-2" @click="editMember(member)">
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

        <!-- View Member Modal -->
        <div v-if="showViewModal" class="modal-overlay" @click.self="showViewModal = false">
            <div class="modal-content luxury-card" style="max-width: 600px;">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <img :src="viewingMember.avatar" :alt="viewingMember.name" class="member-avatar-lg">
                        <div>
                            <h4 class="fw-bold mb-1">{{ viewingMember.name }}</h4>
                            <p class="text-white-50 mb-0">{{ viewingMember.role }}</p>
                            <span class="badge mt-2" :class="viewingMember.status === 'active' ? 'bg-success' : 'bg-secondary'">
                                {{ viewingMember.status }}
                            </span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-light" @click="showViewModal = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Nickname</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.nickname || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Stage Name</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.stage_name || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Email</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.email }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Phone</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.phone || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Birth Place</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.birth_place || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Birth Date</small>
                            <p class="mb-0 fw-semibold">{{ formatDate(viewingMember.birth_date) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Domicile</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.domicile || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Year Join</small>
                            <p class="mb-0 fw-semibold">{{ formatDate(viewingMember.year_join) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Field of Work</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.field_of_work || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Section</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.section }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Join Date</small>
                            <p class="mb-0 fw-semibold">{{ formatDate(viewingMember.joinDate) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <small class="text-white-50">Performances</small>
                            <p class="mb-0 fw-semibold">{{ viewingMember.performances }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-gradient flex-grow-1" @click="editMember(viewingMember)">
                        <i class="bi bi-pencil me-2"></i>Edit Member
                    </button>
                    <button class="btn btn-outline-light" @click="showViewModal = false">Close</button>
                </div>
            </div>
        </div>

        <!-- Edit Member Modal -->
        <div v-if="showEditModal" class="modal-overlay" @click.self="showEditModal = false">
            <div class="modal-content luxury-card" style="max-width: 700px;">
                <h4 class="fw-bold mb-4">Edit Member</h4>
                <form @submit.prevent="saveMember">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" v-model="editingMember.name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nickname</label>
                            <input type="text" class="form-control" v-model="editingMember.nickname">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" v-model="editingMember.email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stage Name</label>
                            <input type="text" class="form-control" v-model="editingMember.stage_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Birth Place</label>
                            <input type="text" class="form-control" v-model="editingMember.birth_place">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Birth Date</label>
                            <input type="date" class="form-control" v-model="editingMember.birth_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Domicile</label>
                            <input type="text" class="form-control" v-model="editingMember.domicile">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year Join</label>
                            <input type="date" class="form-control" v-model="editingMember.year_join">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Field of Work</label>
                            <input type="text" class="form-control" v-model="editingMember.field_of_work">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role *</label>
                            <input type="text" class="form-control" v-model="editingMember.role" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section *</label>
                            <select class="form-select" v-model="editingMember.section" required>
                                <option value="Soprano">Soprano</option>
                                <option value="Alto">Alto</option>
                                <option value="Tenor">Tenor</option>
                                <option value="Bass">Bass</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Join Date</label>
                            <input type="date" class="form-control" v-model="editingMember.joinDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" v-model="editingMember.status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Performances</label>
                            <input type="number" class="form-control" v-model="editingMember.performances" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" v-model="editingMember.phone">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Avatar URL</label>
                            <input type="url" class="form-control" v-model="editingMember.avatar">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-gradient flex-grow-1">
                            <i class="bi bi-check-lg me-2"></i>Save Changes
                        </button>
                        <button type="button" class="btn btn-outline-light" @click="showEditModal = false">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useMembersStore } from '../stores/members'
import { api } from '../services/api'

const membersStore = useMembersStore()
const showAddModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedSection = ref(null)
const viewingMember = ref({})
const editingMember = ref({})
const newMember = ref({
    name: '',
    nickname: '',
    email: '',
    fullname: '',
    stage_name: '',
    birth_place: '',
    birth_date: '',
    domicile: '',
    phone: '',
    year_join: '',
    field_of_work: '',
    role: '',
    section: 'Soprano',
    joinDate: new Date().toISOString().split('T')[0],
    status: 'active',
    performances: 0,
    avatar: 'https://i.pravatar.cc/150?img=' + Math.floor(Math.random() * 70)
})

const membersBySection = computed(() => membersStore.membersBySection)

const filteredMembers = computed(() => {
    if (!selectedSection.value) return membersStore.members
    return membersStore.members.filter(m => m.section === selectedSection.value)
})

onMounted(() => {
    membersStore.fetchMembers()
})

async function addNewMember() {
    try {
        await membersStore.addMember({ ...newMember.value })
        showAddModal.value = false
        newMember.value = {
            name: '',
            nickname: '',
            email: '',
            fullname: '',
            stage_name: '',
            birth_place: '',
            birth_date: '',
            domicile: '',
            phone: '',
            year_join: '',
            field_of_work: '',
            role: '',
            section: 'Soprano',
            joinDate: new Date().toISOString().split('T')[0],
            status: 'active',
            performances: 0,
            avatar: 'https://i.pravatar.cc/150?img=' + Math.floor(Math.random() * 70)
        }
    } catch (error) {
        alert('Failed to add member: ' + error.message)
    }
}

async function deactivateMember(id) {
    if (confirm('Are you sure you want to deactivate this member?')) {
        try {
            await membersStore.updateMemberStatus(id, 'inactive')
        } catch (error) {
            alert('Failed to update member: ' + error.message)
        }
    }
}

function viewMemberDetails(member) {
    viewingMember.value = { ...member }
    showViewModal.value = true
}

function editMember(member) {
    showViewModal.value = false
    editingMember.value = { ...member }
    showEditModal.value = true
}

async function saveMember() {
    try {
        const updateData = { ...editingMember.value }
        if ('joinDate' in updateData) {
            updateData.join_date = updateData.joinDate
            delete updateData.joinDate
        }
        await api.updateMember(updateData.id, updateData)
        showEditModal.value = false
        await membersStore.fetchMembers()
    } catch (error) {
        alert('Failed to update member: ' + error.message)
    }
}

function formatDate(date) {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    })
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

.member-avatar-lg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid var(--primary-color);
    object-fit: cover;
}

.detail-card {
    background: rgba(102, 126, 234, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 0.75rem 1rem;
}

.detail-card p {
    color: var(--text-primary);
    margin-top: 0.25rem;
}
</style>