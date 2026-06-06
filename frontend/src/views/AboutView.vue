<template>
    <div class="about-page">
        <!-- Hero Section -->
        <div class="hero-section luxury-card mb-4">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">BMS Choir</h1>
                    <p class="lead text-white-50 mb-4">
                        Elevating musical excellence through harmony, passion, and dedication since 2005.
                        Our choir brings together talented voices to create unforgettable musical experiences.
                    </p>
                    <div class="stats-row d-flex gap-4">
                        <div class="stat">
                            <h3 class="fw-bold mb-0">50+</h3>
                            <small class="text-white-50">Active Members</small>
                        </div>
                        <div class="stat">
                            <h3 class="fw-bold mb-0">100+</h3>
                            <small class="text-white-50">Performances</small>
                        </div>
                        <div class="stat">
                            <h3 class="fw-bold mb-0">15+</h3>
                            <small class="text-white-50">Years Experience</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image">
                        <i class="bi bi-music-note-beamed display-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="luxury-card h-100">
                    <div class="card-icon mb-3">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Our Mission</h4>
                    <p class="text-white-50">
                        To inspire and enrich our community through exceptional choral performances,
                        fostering musical excellence and creating transformative experiences that
                        celebrate the power of the human voice.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="luxury-card h-100">
                    <div class="card-icon mb-3">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Our Vision</h4>
                    <p class="text-white-50">
                        To be a leading voice in the choral community, recognized internationally
                        for artistic excellence, innovative programming, and commitment to
                        musical education and cultural enrichment.
                    </p>
                </div>
            </div>
        </div>

        <!-- Leadership Team -->
        <div class="luxury-card mb-4">
            <h3 class="fw-bold mb-4">
                <i class="bi bi-people me-2"></i>
                Leadership Team
            </h3>
            <div class="row g-4">
                <div v-for="leader in leadershipTeam" :key="leader.id" class="col-lg-3 col-md-6">
                    <div class="leader-card text-center">
                        <img :src="leader.avatar" :alt="leader.name" class="leader-avatar mb-3">
                        <h5 class="fw-bold mb-1">{{ leader.name }}</h5>
                        <p class="text-primary mb-2">{{ leader.role }}</p>
                        <p class="text-white-50 small">{{ leader.bio }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievements -->
        <div class="luxury-card">
            <h3 class="fw-bold mb-4">
                <i class="bi bi-trophy me-2"></i>
                Our Achievements
            </h3>
            <div class="row g-4">
                <div v-for="achievement in achievements" :key="achievement.year" class="col-md-4">
                    <div class="achievement-card">
                        <div class="year-badge">{{ achievement.year }}</div>
                        <h5 class="fw-bold mb-2">{{ achievement.title }}</h5>
                        <p class="text-white-50 mb-0">{{ achievement.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const leadershipTeam = [
    {
        id: 1,
        name: 'Alexandra Williams',
        role: 'Music Director',
        bio: '15+ years of choral conducting experience',
        avatar: 'https://i.pravatar.cc/150?img=5'
    },
    {
        id: 2,
        name: 'Marcus Thompson',
        role: 'Assistant Director',
        bio: 'Award-winning composer and arranger',
        avatar: 'https://i.pravatar.cc/150?img=12'
    },
    {
        id: 3,
        name: 'Isabella Martinez',
        role: 'Vocal Coach',
        bio: 'Opera singer and voice specialist',
        avatar: 'https://i.pravatar.cc/150?img=9'
    },
    {
        id: 4,
        name: 'James Anderson',
        role: 'Accompanist',
        bio: 'Concert pianist with international acclaim',
        avatar: 'https://i.pravatar.cc/150?img=15'
    }
]

const achievements = [
    {
        year: 2025,
        title: 'International Choir Competition',
        description: 'Gold Medal at the International Choral Festival in Vienna'
    },
    {
        year: 2024,
        title: 'Best Conductor Award',
        description: 'Our director received the prestigious National Conducting Award'
    },
    {
        year: 2023,
        title: 'Community Impact Award',
        description: 'Recognized for outstanding contribution to local arts education'
    }
]
</script>

<style scoped>
.hero-section {
    background: linear-gradient(135deg, rgba(166, 123, 91, 0.08), rgba(193, 154, 107, 0.05));
    border: 1px solid rgba(166, 123, 91, 0.2);
}

.hero-image {
    width: 200px;
    height: 200px;
    margin: 0 auto;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 10px 40px rgba(166, 123, 91, 0.3);
}

.card-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-gradient);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 4px 12px rgba(166, 123, 91, 0.3);
}

.leader-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 3px solid var(--primary-color);
    box-shadow: 0 4px 12px rgba(166, 123, 91, 0.2);
}

.leader-card {
    padding: 1.5rem;
    border-radius: 16px;
    background: rgba(166, 123, 91, 0.05);
    transition: all 0.4s ease;
}

.leader-card:hover {
    background: rgba(166, 123, 91, 0.12);
    transform: translateY(-5px);
}

.achievement-card {
    padding: 1.5rem;
    border-radius: 16px;
    background: rgba(166, 123, 91, 0.05);
    border-left: 4px solid var(--primary-color);
}

.year-badge {
    background: var(--primary-gradient);
    color: white;
    padding: 0.25rem 1rem;
    border-radius: 20px;
    display: inline-block;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.luxury-text-muted {
    color: var(--text-secondary);
}

.luxury-event-title {
    color: var(--text-primary);
}

.luxury-heading {
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .hero-image {
        width: 150px;
        height: 150px;
        margin-top: 2rem;
    }
    
    .stats-row {
        flex-wrap: wrap;
    }
    
    .stat {
        flex: 1 1 30%;
    }
}
</style>