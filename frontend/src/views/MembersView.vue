<template>
    <div class="members-page">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2 luxury-heading">
                            <i class="bi bi-people-fill me-2 text-gold"></i>
                            Choir Members
                        </h2>
                        <p class="luxury-subtitle">Manage active members and sections</p>
                    </div>
                    <button v-if="isMusicDirector" class="btn btn-gradient luxury-btn" @click="showAddModal = true">
                        <i class="bi bi-person-plus me-2"></i>
                        Add Member
                    </button>
                </div>
            </div>
        </div>

        <!-- Search + Section Overview -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search text-white-50"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" v-model="memberSearchQuery"
                        placeholder="Search by name or nickname..." style="border-radius: 0 8px 8px 0;">
                </div>
            </div>
        </div>

        <!-- Section Overview -->
        <div class="row g-3 mb-4">
            <div v-for="(members, section) in membersBySection" :key="section" class="col-md-3 col-sm-6">
                <div class="section-card luxury-section-card" @click="selectedSection = section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 luxury-section-title">{{ section }}</h6>
                            <small class="luxury-text-muted">{{ members.length }} members</small>
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
                            <h5 class="mb-1 luxury-event-title">{{ member.name }}</h5>
                            <p class="mb-0 luxury-text-muted">{{ member.role }}</p>
                        </div>
                        <span class="badge ms-auto badge-section"
                            :class="member.status === 'active' ? 'badge-active' : 'badge-inactive'">
                            {{ member.status }}
                        </span>
                    </div>

                    <div class="member-details luxury-details">
                        <div class="detail-item">
                            <i class="bi bi-envelope text-gold"></i>
                            <span>{{ member.email }}</span>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-telephone text-gold"></i>
                            <span>{{ displayPhone(member.phone) }}</span>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-calendar-check text-gold"></i>
                            <span>Joined {{ formatDate(member.joinDate) }}</span>
                        </div>
                    </div>

                    <div class="member-stats mt-3 luxury-stats">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="luxury-text-muted">Performances</small>
                                <h4 class="mb-0 luxury-event-title">{{ member.performances }}</h4>
                            </div>
                            <div>
                                <small class="luxury-text-muted">Section</small>
                                <h4 class="mb-0 luxury-event-title">{{ member.section }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="member-actions mt-3">
                        <button class="btn btn-sm btn-earth-outline me-2" @click="viewMemberDetails(member)">
                            <i class="bi bi-eye me-1"></i>View
                        </button>
                        <button v-if="isMusicDirector" class="btn btn-sm btn-gold-outline me-2"
                            @click="editMember(member)">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </button>
                        <button v-if="isMusicDirector" class="btn btn-sm btn-danger-earth"
                            @click="deactivateMember(member.id)">
                            <i class="bi bi-person-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Member Modal -->
        <div v-if="showAddModal" class="modal-overlay">
            <div class="modal-content luxury-card">
                <h4 class="fw-bold mb-4 luxury-subheading">Add New Member</h4>
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
                        <button type="button" class="btn btn-earth-outline"
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
                            <h4 class="fw-bold mb-1 luxury-subheading">{{ viewingMember.name }}</h4>
                            <p class="luxury-text-muted mb-0">{{ viewingMember.role }}</p>
                            <span class="badge mt-2 badge-section"
                                :class="viewingMember.status === 'active' ? 'badge-active' : 'badge-inactive'">
                                {{ viewingMember.status }}
                            </span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-earth-outline" @click="showViewModal = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Nickname</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ viewingMember.nickname || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Stage Name</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ viewingMember.stage_name || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Email</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ viewingMember.email }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Phone</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ displayPhone(viewingMember.phone) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Birth Place</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ viewingMember.birth_place || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Birth Date</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ formatDate(viewingMember.birth_date) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Domicile</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ viewingMember.domicile || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Year Join</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ formatDate(viewingMember.year_join) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Field of Work</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ viewingMember.field_of_work || '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Section</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ viewingMember.section }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Join Date</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ formatDate(viewingMember.joinDate) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card luxury-detail-card">
                            <small class="luxury-text-muted">Performances</small>
                            <p class="mb-0 fw-semibold luxury-event-title">{{ viewingMember.performances }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button v-if="isMusicDirector" class="btn btn-gradient flex-grow-1"
                        @click="editMember(viewingMember)">
                        <i class="bi bi-pencil me-2"></i>Edit Member
                    </button>
                    <button class="btn btn-earth-outline" @click="showViewModal = false">Close</button>
                </div>
            </div>
        </div>

        <!-- Edit Member Modal -->
        <div v-if="showEditModal" class="modal-overlay" @click.self="showEditModal = false">
            <div class="modal-content luxury-card" style="max-width: 700px;">
                <h4 class="fw-bold mb-4 luxury-subheading">Edit Member</h4>
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
                        <button type="button" class="btn btn-earth-outline"
                            @click="showEditModal = false">Cancel</button>
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
import { useAuthStore } from '../stores/auth'

const membersStore = useMembersStore()
const authStore = useAuthStore()
const showAddModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedSection = ref(null)
const viewingMember = ref({})
const editingMember = ref({})
const memberSearchQuery = ref('')
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
    let members = membersStore.members || []
    if (selectedSection.value) {
        members = members.filter(m => m.section === selectedSection.value)
    }
    if (memberSearchQuery.value) {
        const q = memberSearchQuery.value.toLowerCase()
        members = members.filter(m => (m.name && m.name.toLowerCase().includes(q)) || (m.nickname && m.nickname.toLowerCase().includes(q)))
    }
    return members
})

onMounted(() => {
    membersStore.fetchMembers()
})

const userRole = computed(() => authStore.user?.role || '')
const isMusicDirector = computed(() => userRole.value === 'Music Director')
const isSectionLeader = computed(() => userRole.value === 'Section Leader')

function displayPhone(phone) {
    if (!phone) return '-'
    if (isMusicDirector.value || isSectionLeader.value) return phone
    const digits = String(phone).replace(/\D/g, '')
    const last4 = digits.slice(-4)
    return `***${last4}`
}

async function addNewMember() {
    if (!isMusicDirector.value) {
        alert('Only the Music Director can add members.')
        return
    }
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
    if (!isMusicDirector.value) {
        alert('Only the Music Director can remove or deactivate members.')
        return
    }
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
    if (!isMusicDirector.value) {
        alert('Only the Music Director can edit members.')
        return
    }
    showViewModal.value = false
    editingMember.value = { ...member }
    showEditModal.value = true
}

async function saveMember() {
    if (!isMusicDirector.value) {
        alert('Only the Music Director can save member changes.')
        return
    }
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
    background: linear-gradient(135deg, rgba(166, 123, 91, 0.12), rgba(193, 154, 107, 0.08));
    border: 1px solid rgba(166, 123, 91, 0.25);
    border-radius: 16px;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.luxury-section-card:hover {
    background: linear-gradient(135deg, rgba(166, 123, 91, 0.2), rgba(193, 154, 107, 0.15));
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(166, 123, 91, 0.2);
}

.section-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-gradient);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 4px 12px rgba(166, 123, 91, 0.3);
}

.luxury-section-title {
    color: var(--text-primary);
    font-weight: 600;
}

.member-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid var(--primary-color);
    box-shadow: 0 4px 12px rgba(166, 123, 91, 0.3);
}

.member-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(166, 123, 91, 0.15);
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
}

.member-stats {
    background: rgba(166, 123, 91, 0.05);
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid rgba(166, 123, 91, 0.1);
}

.luxury-stats {
    background: rgba(166, 123, 91, 0.05);
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid rgba(166, 123, 91, 0.1);
}

.member-actions {
    display: flex;
    gap: 0.5rem;
}

.luxury-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(166, 123, 91, 0.15);
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(26, 18, 10, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(8px);
}

.modal-content {
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.form-control,
.form-select {
    background: rgba(166, 123, 91, 0.08);
    border: 1px solid rgba(166, 123, 91, 0.3);
    color: var(--text-primary);
    border-radius: 12px;
    padding: 0.875rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    background: rgba(166, 123, 91, 0.12);
    border-color: var(--primary-color);
    color: var(--text-primary);
    box-shadow: 0 0 0 0.25rem rgba(166, 123, 91, 0.25);
}

.member-avatar-lg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid var(--primary-color);
    object-fit: cover;
    box-shadow: 0 6px 16px rgba(166, 123, 91, 0.3);
}

.luxury-detail-card {
    background: rgba(166, 123, 91, 0.05);
    border: 1px solid rgba(166, 123, 91, 0.1);
    border-radius: 12px;
    padding: 0.75rem 1rem;
}

.luxury-detail-card p {
    color: var(--text-primary);
    margin-top: 0.25rem;
}

.badge-active {
    background: var(--success-color);
    color: #F5F0E6;
    border-radius: 16px;
}

.badge-inactive {
    background: rgba(139, 115, 85, 0.5);
    color: var(--text-secondary);
    border-radius: 16px;
}

.luxury-text-muted {
    color: var(--text-secondary);
}

.luxury-event-title {
    color: var(--text-primary);
}
</style>