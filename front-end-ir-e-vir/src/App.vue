<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'
import { RouterView, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

const router = useRouter()
const auth = useAuthStore()
const ui = useUiStore()

function handleSessionExpired() {
  auth.clearSession()
  ui.notify('error', 'Sessão expirada. Faça login novamente.')

  if (router.currentRoute.value.path !== '/login') {
    router.push('/login')
  }
}

onMounted(() => window.addEventListener('irevir:session-expired', handleSessionExpired))
onUnmounted(() => window.removeEventListener('irevir:session-expired', handleSessionExpired))
</script>

<template><RouterView /></template>
