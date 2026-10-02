import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue')
    },
    {
      path: '/members',
      name: 'members',
      component: () => import('../views/MembersView.vue')
    },
    {
      path: '/gallery',
      name: 'gallery',
      component: () => import('../views/GalleryView.vue')
    },
    {
      path: '/events',
      name: 'events',
      component: () => import('../views/EventsView.vue')
    },
    {
      path: '/contact',
      name: 'contact',
      component: () => import('../views/ContactView.vue')
    },
    // ── Admin (nested under AdminLayout, admin-only) ───────────────
    {
      path: '/admin',
      component: () => import('../views/admin/AdminLayout.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        {
          path: '',
          name: 'admin-overview',
          component: () => import('../views/admin/AdminOverview.vue'),
        },
        {
          path: 'members',
          name: 'admin-members',
          component: () => import('../views/admin/AdminMembers.vue'),
        },
        {
          path: 'events',
          name: 'admin-events',
          component: () => import('../views/admin/AdminEvents.vue'),
        },
        {
          path: 'gallery',
          name: 'admin-gallery',
          component: () => import('../views/admin/AdminGallery.vue'),
        },
        {
          path: 'settings',
          name: 'admin-settings',
          component: () => import('../views/admin/AdminSettings.vue'),
        },
      ],
    },
    // ── Auth routes ────────────────────────────────────────────────
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { guestOnly: true }   // redirect to home if already logged in
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/RegisterView.vue'),
      meta: { guestOnly: true }
    },
  ],
  scrollBehavior() {
    return { top: 0 }
  }
})

// ── Navigation guards ──────────────────────────────────────────────
router.beforeEach((to) => {
  const auth = useAuthStore()

  // Guests cannot access auth-required pages
  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // Logged-in users are bounced away from login/register
  if (to.meta.guestOnly && auth.isLoggedIn) {
    return { name: 'home' }
  }

  // Only admins may access admin pages
  if (to.matched.some(record => record.meta.requiresAdmin) && !auth.isAdmin) {
    return auth.isLoggedIn ? { name: 'home' } : { name: 'login', query: { redirect: to.fullPath } }
  }
})

export default router
