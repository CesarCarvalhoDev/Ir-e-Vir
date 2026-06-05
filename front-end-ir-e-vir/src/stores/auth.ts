import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { setAuthToken } from '@/api/http'
import { apiService } from '@/services/apiService'
import type { User } from '@/types/api'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(sessionStorage.getItem('irevir_token'))
  const user = ref<User | null>(null)
  const initialized = ref(false)

  const isAuthenticated = computed(() => Boolean(token.value && user.value))
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function login(email: string, password: string) {
    const response = await apiService.login({ email, password })
    token.value = response.token
    user.value = response.user
    setAuthToken(response.token)
    initialized.value = true
    return response.user
  }

  async function loadSession() {
    if (initialized.value) return user.value
    if (!token.value) {
      initialized.value = true
      return null
    }

    try {
      setAuthToken(token.value)
      user.value = await apiService.me()
      return user.value
    } catch {
      clearSession()
      return null
    } finally {
      initialized.value = true
    }
  }

  async function refreshMe() {
    if (!token.value) return null
    user.value = await apiService.me()
    return user.value
  }

  async function logout() {
    try {
      if (token.value) await apiService.logout()
    } finally {
      clearSession()
    }
  }

  function clearSession() {
    token.value = null
    user.value = null
    initialized.value = true
    setAuthToken(null)
  }

  return { token, user, initialized, isAuthenticated, isAdmin, login, loadSession, refreshMe, logout, clearSession }
})
