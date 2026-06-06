import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import LoginPage from '@/pages/LoginPage.vue'
import UserHomePage from '@/pages/UserHomePage.vue'
import UserVehiclesPage from '@/pages/UserVehiclesPage.vue'
import UserStaysPage from '@/pages/UserStaysPage.vue'
import UserChargesPage from '@/pages/UserChargesPage.vue'
import UserFinesPage from '@/pages/UserFinesPage.vue'
import DashboardPage from '@/pages/DashboardPage.vue'
import StaysPage from '@/pages/StaysPage.vue'
import ChargesPage from '@/pages/ChargesPage.vue'
import UsersPage from '@/pages/UsersPage.vue'
import AdminVehiclesPage from '@/pages/AdminVehiclesPage.vue'
import AdminZonesPage from '@/pages/AdminZonesPage.vue'
import AdminFinesPage from '@/pages/AdminFinesPage.vue'
import ApiNotesPage from '@/pages/ApiNotesPage.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', component: LoginPage, meta: { guest: true } },
    { path: '/', redirect: '/user' },
    {
      path: '/user',
      component: AppLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '', component: UserHomePage },
        { path: 'veiculos', component: UserVehiclesPage },
        { path: 'permanencias', component: UserStaysPage },
        { path: 'cobrancas', component: UserChargesPage },
        { path: 'multas', component: UserFinesPage },
        { path: 'limites-da-api', component: ApiNotesPage },
      ],
    },
    {
      path: '/admin',
      component: AppLayout,
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        { path: '', component: DashboardPage },
        { path: 'permanencias', component: StaysPage },
        { path: 'veiculos', component: AdminVehiclesPage },
        { path: 'zonas', component: AdminZonesPage },
        { path: 'cobrancas', component: ChargesPage },
        { path: 'multas', component: AdminFinesPage },
        { path: 'usuarios', component: UsersPage },
        { path: 'limites-da-api', component: ApiNotesPage },
      ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/user' },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  await auth.loadSession()

  if (to.meta.guest && auth.isAuthenticated) {
    return auth.isAdmin ? '/admin' : '/user'
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return '/login'
  }

  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return '/user'
  }

  return true
})

export default router
