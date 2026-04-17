<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
    <!-- Loading State -->
    <div v-if="!user" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
        <p class="text-slate-400">Carregando...</p>
      </div>
    </div>

    <!-- Header -->
    <header v-else class="bg-gradient-to-r from-slate-800 to-slate-700 shadow-2xl border-b border-slate-700">
      <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-4xl font-bold text-white">
              🅿️ Ir e Vir - Meus Estacionamentos
            </h1>
            <p class="text-slate-400 mt-2">Visualize todas as suas cobranças</p>
          </div>
          <div class="text-right">
            <p class="text-sm text-slate-400 mb-2">Olá, {{ user.name }}</p>
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
      <!-- Search by Plate -->
      <div class="bg-slate-800 rounded-xl shadow-2xl p-8 border border-slate-700 hover:border-slate-600 transition-colors mb-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center">
            <span class="text-lg">🔍</span>
          </div>
          <h2 class="text-2xl font-bold text-white">Buscar por Placa</h2>
        </div>

        <div class="flex gap-4">
          <input
            v-model="searchPlate"
            @keyup.enter="searchChargesByPlate"
            type="text"
            placeholder="Digite a placa do veículo (ex: ABC-1234)"
            class="flex-1 px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors uppercase"
          />
          <button
            @click="searchChargesByPlate"
            :disabled="searchLoading || !searchPlate"
            class="px-6 py-3 bg-amber-600 hover:bg-amber-700 disabled:bg-slate-600 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors"
          >
            <span v-if="!searchLoading">Buscar</span>
            <span v-else class="flex items-center gap-2">
              <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              Buscando...
            </span>
          </button>
        </div>

        <p v-if="searchError" class="text-red-500 text-sm mt-4">{{ searchError }}</p>
      </div>

      <!-- Charge Search Result -->
      <div v-if="selectedCharge" class="bg-slate-800 rounded-xl shadow-2xl p-8 border border-slate-700 mb-8">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-bold text-white">Resultado da Busca</h3>
          <button
            @click="selectedCharge = null; searchPlate = ''"
            class="text-slate-400 hover:text-white text-2xl"
          >
            ✕
          </button>
        </div>
        <ChargeCard :charge="selectedCharge" />
      </div>

      <!-- Charges List -->
      <div class="bg-slate-800 rounded-xl shadow-2xl p-8 border border-slate-700">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
            <span class="text-lg">📋</span>
          </div>
          <h2 class="text-2xl font-bold text-white">Todas as Cobranças</h2>
          <span class="ml-auto text-slate-400 text-sm">Total: {{ charges.length }}</span>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="charges.length === 0" class="text-center py-12">
          <p class="text-slate-400 text-lg">Nenhuma cobrança encontrada</p>
          <p class="text-slate-500 text-sm mt-2">Suas cobranças aparecerão aqui</p>
        </div>

        <!-- Charges Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <ChargeCard
            v-for="charge in charges"
            :key="charge.id"
            :charge="charge"
          />
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import authService from '../services/authService.js';
import ChargeCard from './ChargeCard.vue';

export default {
  name: 'UserDashboard',
  components: {
    ChargeCard,
  },
  emits: ['logout'],
  data() {
    return {
      user: null,
      charges: [],
      loading: true,
      searchPlate: '',
      searchLoading: false,
      searchError: '',
      selectedCharge: null,
    };
  },
  async mounted() {
    this.user = authService.getUser();
    await this.loadCharges();
  },
  methods: {
    async loadCharges() {
      this.loading = true;
      try {
        const response = await authService.fetchCharges();
        this.charges = response.data || response;
      } catch (err) {
        console.error('Error loading charges:', err);
      } finally {
        this.loading = false;
      }
    },

    async searchChargesByPlate() {
      if (!this.searchPlate) return;

      this.searchLoading = true;
      this.searchError = '';
      this.selectedCharge = null;

      try {
        const response = await authService.fetchChargesByPlate(this.searchPlate);
        this.selectedCharge = response.data || response;
      } catch (err) {
        this.searchError = 'Nenhuma cobrança encontrada para esta placa';
        console.error('Search error:', err);
      } finally {
        this.searchLoading = false;
      }
    },

    async handleLogout() {
      try {
        await authService.logout();
        this.$emit('logout');
      } catch (err) {
        console.error('Logout error:', err);
        // Even if logout fails on server, clear local data
        authService.logout();
        this.$emit('logout');
      }
    },
  },
};
</script>
