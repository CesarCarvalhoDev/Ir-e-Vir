<template>
  <!-- Login Page -->
  <LoginPage v-if="!isAuthenticated" @login="handleLogin" />

  <!-- Authenticated User -->
  <div v-else-if="user">
    <!-- Admin Dashboard -->
    <div v-if="isAdmin" class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
      <!-- Header -->
      <header class="bg-gradient-to-r from-slate-800 to-slate-700 shadow-2xl border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-4xl font-bold text-white">
                🅿️ Ir e Vir - Painel do Administrador
              </h1>
              <p class="text-slate-400 mt-2">Gerenciamento de cobranças</p>
            </div>
            <div class="text-right">
              <p class="text-sm text-slate-400 mb-2">Olá, {{ user && user.name }}</p>
              <button
                @click="handleLogout"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors"
              >
                Sair
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Formulário de Nova Cobrança -->
          <div class="bg-slate-800 rounded-xl shadow-2xl p-8 border border-slate-700 hover:border-slate-600 transition-colors">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-lg">➕</span>
              </div>
              <h2 class="text-2xl font-bold text-white">Nova Cobrança</h2>
            </div>
            <FormCobranca @submit="criarCobranca" />
          </div>

          <!-- Lista de Cobranças -->
          <div class="bg-slate-800 rounded-xl shadow-2xl p-8 border border-slate-700 hover:border-slate-600 transition-colors">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                <span class="text-lg">📋</span>
              </div>
              <h2 class="text-2xl font-bold text-white">Cobranças</h2>
            </div>
            <ListaCobrancas :cobracas="cobracas" :carregando="carregando" @atualizar="buscarCobracas" />
          </div>
        </div>

        <!-- Buscar por Placa -->
        <div class="bg-slate-800 rounded-xl shadow-2xl p-8 mt-10 border border-slate-700 hover:border-slate-600 transition-colors">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center">
              <span class="text-lg">🔍</span>
            </div>
            <h2 class="text-2xl font-bold text-white">Buscar por Placa</h2>
          </div>
          <BuscaPlaca @buscar="buscarPorPlaca" :resultado="resultadoBusca" :carregando="carregandoBusca" />
        </div>
      </main>
    </div>

    <!-- User Dashboard -->
    <UserDashboard v-else @logout="handleLogout" />
  </div>

  <!-- Loading State -->
  <div v-else class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
      <p class="text-slate-400">Carregando...</p>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import LoginPage from './components/LoginPage.vue'
import UserDashboard from './components/UserDashboard.vue'
import FormCobranca from './components/FormCobranca.vue'
import ListaCobrancas from './components/ListaCobrancas.vue'
import BuscaPlaca from './components/BuscaPlaca.vue'
import authService from './services/authService.js'

export default {
  name: 'App',
  components: {
    LoginPage,
    UserDashboard,
    FormCobranca,
    ListaCobrancas,
    BuscaPlaca,
  },
  setup() {
    const user = ref(null)
    const isAuthenticated = computed(() => authService.isAuthenticated())
    const isAdmin = computed(() => user.value && user.value.role === 'admin')

    const cobracas = ref([])
    const carregando = ref(false)
    const carregandoBusca = ref(false)
    const resultadoBusca = ref(null)

    const apiURL = 'http://localhost:8000/api/v1'

    const handleLogin = (data) => {
      user.value = data.user
      buscarCobracas()
    }

    const handleLogout = async () => {
      await authService.logout()
      user.value = null
      cobracas.value = []
    }

    const buscarCobracas = async () => {
      carregando.value = true
      try {
        const response = await axios.get(`${apiURL}/charges`, {
          headers: {
            Authorization: `Bearer ${authService.getToken()}`,
          },
        })
        cobracas.value = response.data.data || response.data || []
      } catch (error) {
        console.error('Erro ao buscar cobranças:', error)
        alert('Erro ao buscar cobranças: ' + (error.response?.data?.message || error.message))
      } finally {
        carregando.value = false
      }
    }

    const criarCobranca = async (dadosCobranca) => {
      try {
        const response = await axios.post(`${apiURL}/charges/${dadosCobranca.stay_id}`, {}, {
          headers: {
            Authorization: `Bearer ${authService.getToken()}`,
          },
        })
        alert('Cobrança criada com sucesso!')
        buscarCobracas()
      } catch (error) {
        console.error('Erro ao criar cobrança:', error)
        alert(`Erro: ${error.response?.data?.message || error.response?.data?.errors || error.message}`)
      }
    }

    const buscarPorPlaca = async (placa) => {
      carregandoBusca.value = true
      try {
        const response = await axios.get(`${apiURL}/charges/plate/${placa}`, {
          headers: {
            Authorization: `Bearer ${authService.getToken()}`,
          },
        })
        resultadoBusca.value = response.data
      } catch (error) {
        console.error('Erro ao buscar placa:', error)
        resultadoBusca.value = { erro: 'Nenhuma cobrança encontrada para esta placa' }
      } finally {
        carregandoBusca.value = false
      }
    }

    onMounted(async () => {
      // Tenta carregar dados do localStorage e valida com o backend
      await authService.loadFromStorage();
      
      const savedUser = authService.getUser();
      if (savedUser) {
        user.value = savedUser;
        if (savedUser.role === 'admin') {
          buscarCobracas();
        }
      }
    })

    return {
      user,
      isAuthenticated,
      isAdmin,
      cobracas,
      carregando,
      carregandoBusca,
      resultadoBusca,
      handleLogin,
      handleLogout,
      buscarCobracas,
      criarCobranca,
      buscarPorPlaca,
    }
  },
}
</script>

