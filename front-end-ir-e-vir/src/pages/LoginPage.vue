<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { LogIn } from 'lucide-vue-next'
import { getApiError } from '@/api/http'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
const form = reactive({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function submit() {
  error.value = ''
  if (!form.email || !form.password) {
    error.value = 'Informe e-mail e senha.'
    return
  }

  loading.value = true
  try {
    const user = await auth.login(form.email, form.password)
    router.push(user.role === 'admin' ? '/admin' : '/user')
  } catch (err) {
    error.value = getApiError(err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="auth-screen">
    <section class="login-container">
      <div class="text-login">
        <h1>Olá, seja bem-vindo!</h1>
        <h2>Acesse sua conta Ir e Vir para continuar.</h2>
      </div>
      <form @submit.prevent="submit">
        <label class="input-container">E-mail<input v-model.trim="form.email" type="email" required placeholder="Digite seu e-mail" /></label>
        <label class="input-container">Senha<input v-model="form.password" type="password" required placeholder="Digite sua senha" /></label>
        <p v-if="error" class="form-error">{{ error }}</p>
        <button class="btn-login" :disabled="loading"><LogIn />{{ loading ? 'Entrando...' : 'Entrar' }}</button>
      </form>
      <p class="login-hint">Admin teste: admin@irevir.test / password<br>Usuário teste: user@irevir.test / password</p>
    </section>
  </main>
</template>
