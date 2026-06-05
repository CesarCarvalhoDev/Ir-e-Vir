import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const menuOpen = ref(false)
  const toast = ref<{ type: 'success' | 'error'; message: string } | null>(null)
  let timer: ReturnType<typeof setTimeout> | undefined
  const notify = (type: 'success' | 'error', message: string) => {
    toast.value = { type, message }
    clearTimeout(timer)
    timer = setTimeout(() => { toast.value = null }, 5000)
  }
  return { menuOpen, toast, notify }
})
