<script setup>
/**
 * AdminLayout — dark-luxury dashboard shell with sidebar navigation.
 * Features: sticky sidebar, user info block, mobile top-bar + overlay drawer.
 */
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const sidebarOpen = ref(false)

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}

function closeSidebar() {
  sidebarOpen.value = false
}

function handleLogout() {
  auth.logout()
  router.push({ name: 'home' })
}

// Close sidebar on Escape
function onKeydown(e) {
  if (e.key === 'Escape' && sidebarOpen.value) closeSidebar()
}

onMounted(() => {
  if (!auth.isAdmin) router.replace({ name: 'home' })
  document.addEventListener('keydown', onKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', onKeydown)
})
</script>

<template>
  <div class="admin-shell">
    <!-- ── Mobile top-bar ─────────────────────────────────────── -->
    <header class="admin-topbar d-lg-none">
      <button
        class="admin-topbar-toggle"
        :aria-expanded="sidebarOpen"
        aria-controls="admin-sidebar"
        aria-label="Toggle navigation"
        @click="toggleSidebar"
      >
        <span class="topbar-bar" :class="{ open: sidebarOpen }"></span>
        <span class="topbar-bar" :class="{ open: sidebarOpen }"></span>
        <span class="topbar-bar" :class="{ open: sidebarOpen }"></span>
      </button>

      <RouterLink to="/admin" class="admin-topbar-brand text-decoration-none" @click="closeSidebar">
        <i class="bi bi-music-note-beamed text-gold"></i>
        <span class="text-cream fw-bold" style="letter-spacing:0.18em; font-size:0.85rem;">BATAVIA</span>
      </RouterLink>

      <div class="admin-topbar-avatar" :title="auth.user?.name">
        {{ auth.user?.name?.split(' ').map(w => w[0]).slice(0, 2).join('') || 'AD' }}
      </div>
    </header>

    <!-- ── Sidebar overlay (mobile) ──────────────────────────── -->
    <Transition name="sidebar-fade">
      <div
        v-if="sidebarOpen"
        class="admin-sidebar-backdrop d-lg-none"
        aria-hidden="true"
        @click="closeSidebar"
      ></div>
    </Transition>

    <!-- ── Sidebar ────────────────────────────────────────────── -->
    <aside
      id="admin-sidebar"
      class="admin-sidebar"
      :class="{ 'sidebar-open': sidebarOpen }"
      role="navigation"
      aria-label="Admin navigation"
    >
      <!-- Brand -->
      <RouterLink to="/" class="admin-brand text-decoration-none" @click="closeSidebar">
        <div class="admin-brand-icon">
          <i class="bi bi-music-note-beamed"></i>
        </div>
        <div>
          <div class="text-cream fw-bold" style="letter-spacing:0.15em; font-size:0.95rem; line-height:1.2;">BATAVIA</div>
          <div class="text-gold" style="letter-spacing:0.3em; font-size:0.6rem;">ADMIN CONSOLE</div>
        </div>
      </RouterLink>

      <div class="gold-divider-left my-4"></div>

      <!-- User info block -->
      <div class="admin-user-block">
        <div class="admin-user-avatar">
          {{ auth.user?.name?.split(' ').map(w => w[0]).slice(0, 2).join('') || 'AD' }}
        </div>
        <div class="admin-user-info">
          <div class="text-cream" style="font-size:0.82rem; font-weight:500; line-height:1.3;">
            {{ auth.user?.name || 'Administrator' }}
          </div>
          <div class="text-gold" style="font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase;">
            Admin
          </div>
        </div>
      </div>

      <div class="admin-nav-section-label">Navigation</div>

      <!-- Nav links -->
      <nav class="admin-nav" aria-label="Admin sections">
        <RouterLink
          class="admin-nav-link"
          to="/admin"
          exact-active-class="router-link-exact-active"
          @click="closeSidebar"
        >
          <span class="admin-nav-icon"><i class="bi bi-speedometer2"></i></span>
          <span>Overview</span>
          <span class="admin-nav-arrow"><i class="bi bi-chevron-right"></i></span>
        </RouterLink>
        <RouterLink
          class="admin-nav-link"
          to="/admin/members"
          @click="closeSidebar"
        >
          <span class="admin-nav-icon"><i class="bi bi-people"></i></span>
          <span>Members</span>
          <span class="admin-nav-arrow"><i class="bi bi-chevron-right"></i></span>
        </RouterLink>
        <RouterLink
          class="admin-nav-link"
          to="/admin/events"
          @click="closeSidebar"
        >
          <span class="admin-nav-icon"><i class="bi bi-calendar-event"></i></span>
          <span>Events</span>
          <span class="admin-nav-arrow"><i class="bi bi-chevron-right"></i></span>
        </RouterLink>
        <RouterLink
          class="admin-nav-link"
          to="/admin/gallery"
          @click="closeSidebar"
        >
          <span class="admin-nav-icon"><i class="bi bi-images"></i></span>
          <span>Gallery</span>
          <span class="admin-nav-arrow"><i class="bi bi-chevron-right"></i></span>
        </RouterLink>
      </nav>

      <!-- Footer links -->
      <div class="admin-sidebar-footer">
        <div class="admin-nav-section-label mb-2">Account</div>
        <RouterLink to="/" class="admin-nav-link small-link" @click="closeSidebar">
          <span class="admin-nav-icon"><i class="bi bi-box-arrow-up-left"></i></span>
          <span>View Site</span>
        </RouterLink>
        <button
          class="admin-nav-link small-link w-100 border-0 bg-transparent"
          @click="handleLogout"
        >
          <span class="admin-nav-icon"><i class="bi bi-box-arrow-right"></i></span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- ── Content area ───────────────────────────────────────── -->
    <div class="admin-content">
      <main class="admin-main" id="main-content" tabindex="-1">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<style scoped>
/* ── Shell ─────────────────────────────────────────────────── */
.admin-shell {
  display: flex;
  min-height: 100vh;
  background: var(--bms-darker);
}

/* ── Sidebar ───────────────────────────────────────────────── */
.admin-sidebar {
  width: 260px;
  flex-shrink: 0;
  position: sticky;
  top: 0;
  height: 100vh;
  display: flex;
  flex-direction: column;
  padding: 1.75rem 1.25rem 1.5rem;
  background: linear-gradient(180deg,
    rgba(22, 22, 40, 0.98) 0%,
    rgba(12, 12, 24, 0.99) 100%
  );
  border-right: 1px solid rgba(201, 169, 97, 0.14);
  overflow-y: auto;
  overflow-x: hidden;
  scrollbar-width: none;
}
.admin-sidebar::-webkit-scrollbar { display: none; }

/* ── Brand ─────────────────────────────────────────────────── */
.admin-brand {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}
.admin-brand-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, rgba(201,169,97,0.2), rgba(201,169,97,0.06));
  border: 1px solid rgba(201,169,97,0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--bms-gold);
  font-size: 1.25rem;
  flex-shrink: 0;
  transition: box-shadow 0.3s ease;
}
.admin-brand:hover .admin-brand-icon {
  box-shadow: 0 0 16px rgba(201,169,97,0.3);
}

/* ── User block ────────────────────────────────────────────── */
.admin-user-block {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.85rem 1rem;
  border-radius: 8px;
  background: rgba(201,169,97,0.05);
  border: 1px solid rgba(201,169,97,0.12);
  margin-bottom: 1.25rem;
}
.admin-user-avatar {
  width: 36px;
  height: 36px;
  flex-shrink: 0;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--bms-gold), var(--bms-accent));
  border: 1px solid rgba(201,169,97,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--bms-cream);
}
.admin-user-info { min-width: 0; overflow: hidden; }
.admin-user-info > div { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ── Section label ─────────────────────────────────────────── */
.admin-nav-section-label {
  font-size: 0.6rem;
  letter-spacing: 0.35em;
  text-transform: uppercase;
  color: var(--bms-muted);
  padding: 0 0.5rem;
  margin-bottom: 0.5rem;
}

/* ── Nav ───────────────────────────────────────────────────── */
.admin-nav {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  margin-bottom: 0.75rem;
}

.admin-nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.7rem 0.85rem;
  border-radius: 7px;
  color: var(--bms-muted-strong);
  text-decoration: none;
  font-size: 0.82rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  transition: background 0.25s ease, color 0.25s ease, padding-left 0.25s ease;
  position: relative;
}

.admin-nav-icon {
  width: 28px;
  height: 28px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  border-radius: 6px;
  transition: background 0.25s ease, color 0.25s ease;
}

.admin-nav-arrow {
  margin-left: auto;
  font-size: 0.65rem;
  opacity: 0;
  transform: translateX(-4px);
  transition: opacity 0.25s ease, transform 0.25s ease;
  color: var(--bms-gold);
}

.admin-nav-link:hover {
  background: rgba(201,169,97,0.07);
  color: var(--bms-cream);
  padding-left: 1.1rem;
}
.admin-nav-link:hover .admin-nav-icon { color: var(--bms-gold); }
.admin-nav-link:hover .admin-nav-arrow { opacity: 1; transform: translateX(0); }

.admin-nav-link.router-link-exact-active {
  background: linear-gradient(135deg, rgba(201,169,97,0.16), rgba(201,169,97,0.05));
  color: var(--bms-gold);
  border: 1px solid rgba(201,169,97,0.2);
}
.admin-nav-link.router-link-exact-active .admin-nav-icon {
  background: rgba(201,169,97,0.15);
  color: var(--bms-gold);
}
.admin-nav-link.router-link-exact-active .admin-nav-arrow { opacity: 1; transform: translateX(0); }

/* ── Sidebar footer ────────────────────────────────────────── */
.admin-sidebar-footer {
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid rgba(201,169,97,0.1);
}
.small-link { font-size: 0.72rem; }
button.admin-nav-link { text-align: left; cursor: pointer; }

/* ── Content ───────────────────────────────────────────────── */
.admin-content { flex: 1; min-width: 0; }
.admin-main { padding: 2.5rem 2.5rem 5rem; max-width: 1200px; }

/* ── Mobile top-bar ────────────────────────────────────────── */
.admin-topbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1100;
  height: 58px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.25rem;
  background: rgba(12,12,24,0.97);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(201,169,97,0.14);
}
.admin-topbar-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 1.2rem;
}
.admin-topbar-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--bms-gold), var(--bms-accent));
  border: 1px solid rgba(201,169,97,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--bms-cream);
}

/* Hamburger */
.admin-topbar-toggle {
  width: 34px;
  height: 34px;
  background: transparent;
  border: 1px solid rgba(201,169,97,0.25);
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 0;
  cursor: pointer;
  transition: border-color 0.25s ease, background 0.25s ease;
}
.admin-topbar-toggle:hover { border-color: var(--bms-gold); background: rgba(201,169,97,0.06); }
.topbar-bar {
  width: 18px;
  height: 2px;
  background: var(--bms-gold);
  border-radius: 2px;
  transition: all 0.3s var(--ease-lux);
  transform-origin: center;
}
.topbar-bar:nth-child(1).open { transform: translateY(7px) rotate(45deg); }
.topbar-bar:nth-child(2).open { opacity: 0; transform: scaleX(0); }
.topbar-bar:nth-child(3).open { transform: translateY(-7px) rotate(-45deg); }

/* ── Mobile sidebar overlay ────────────────────────────────── */
.admin-sidebar-backdrop {
  position: fixed;
  inset: 0;
  z-index: 1199;
  background: rgba(5,5,14,0.7);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
}
.sidebar-fade-enter-active, .sidebar-fade-leave-active { transition: opacity 0.3s ease; }
.sidebar-fade-enter-from, .sidebar-fade-leave-to { opacity: 0; }

/* ── Responsive ────────────────────────────────────────────── */
@media (max-width: 991.98px) {
  .admin-shell { flex-direction: column; }

  .admin-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 1200;
    transform: translateX(-100%);
    transition: transform 0.35s var(--ease-lux);
    height: 100dvh;
    padding-top: 1.5rem;
    box-shadow: 4px 0 40px rgba(0,0,0,0.5);
  }
  .admin-sidebar.sidebar-open { transform: translateX(0); }

  .admin-content { padding-top: 58px; }
  .admin-main { padding: 2rem 1.25rem 4rem; }
}

@media (max-width: 576px) {
  .admin-main { padding: 1.5rem 1rem 4rem; }
}
</style>
