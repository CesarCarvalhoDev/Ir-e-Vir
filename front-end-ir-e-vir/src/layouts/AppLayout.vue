<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { AlertTriangle, CarFront, CircleDollarSign, FileWarning, House, LogOut, MapPin, Menu, Route, UserCircle, X } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
const ui = useUiStore()
const auth = useAuthStore()
const router = useRouter()
const links = computed(() => auth.isAdmin ? [
  { to: '/admin', label: 'Admin geral', icon: House },
  { to: '/admin/permanencias', label: 'Permanências', icon: Route },
  { to: '/admin/veiculos', label: 'Veículos', icon: CarFront },
  { to: '/admin/zonas', label: 'Zonas', icon: MapPin },
  { to: '/admin/cobrancas', label: 'Cobranças', icon: CircleDollarSign },
  { to: '/admin/multas', label: 'Multas', icon: AlertTriangle },
  { to: '/admin/usuarios', label: 'Usuários e veículos', icon: CarFront },
  { to: '/admin/limites-da-api', label: 'Limites da API', icon: FileWarning },
] : [
  { to: '/user', label: 'Minha home', icon: House },
  { to: '/user/veiculos', label: 'Meus veículos', icon: CarFront },
  { to: '/user/permanencias', label: 'Permanências', icon: Route },
  { to: '/user/cobrancas', label: 'Cobranças', icon: CircleDollarSign },
  { to: '/user/multas', label: 'Multas', icon: AlertTriangle },
  { to: '/user/limites-da-api', label: 'Limites da API', icon: FileWarning },
])
async function logout() {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <div class="app-shell">
    <header class="topbar">
      <RouterLink to="/" class="brand"><span class="brand-mark">P</span><span>Ir <b>e Vir</b></span></RouterLink>
      <div class="top-user"><UserCircle /><span>{{ auth.user?.name }}</span><small>{{ auth.user?.role }}</small></div>
      <button class="icon-button menu-button" title="Abrir menu" @click="ui.menuOpen = true"><Menu /></button>
    </header>
    <div v-if="ui.menuOpen" class="backdrop" @click="ui.menuOpen = false" />
    <aside :class="{ open: ui.menuOpen }" class="sidebar">
      <div class="sidebar-head"><strong>Ir e Vir</strong><button class="icon-button mobile-only" title="Fechar menu" @click="ui.menuOpen = false"><X /></button></div>
      <nav>
        <RouterLink v-for="link in links" :key="link.to" :to="link.to" @click="ui.menuOpen = false"><component :is="link.icon" /><span>{{ link.label }}</span></RouterLink>
      </nav>
      <button class="logout-button" @click="logout"><LogOut />Sair</button>
      <p class="api-base">Sessão<br><strong>{{ auth.user?.role === 'admin' ? 'Administrador' : 'Usuário' }}</strong></p>
    </aside>
    <main class="main-content"><RouterView /></main>
    <Transition name="toast"><div v-if="ui.toast" :class="ui.toast.type" class="toast">{{ ui.toast.message }}</div></Transition>
  </div>
</template>
