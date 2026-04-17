<template>
  <div class="space-y-5">
    <div class="flex gap-3">
      <input
        v-model="placa"
        type="text"
        placeholder="Digite a placa do veículo (ex: ABC1234)"
        class="flex-1 px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-white placeholder-slate-400 uppercase transition-all"
        @keyup.enter="buscar"
      />
      <button
        @click="buscar"
        :disabled="carregando"
        class="bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 disabled:from-slate-600 disabled:to-slate-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 active:scale-95 min-w-32"
      >
        <span v-if="!carregando">🔍</span>
        <span v-else class="animate-spin">⏳</span>
        {{ carregando ? 'Buscando' : 'Buscar' }}
      </button>
    </div>

    <div v-if="resultado" class="mt-6 animate-in fade-in">
      <div v-if="resultado.erro" class="bg-red-900 border border-red-700 text-red-200 px-5 py-4 rounded-lg flex items-start gap-3">
        <span class="text-xl">⚠️</span>
        <div>
          <p class="font-semibold">Nenhuma cobrança encontrada</p>
          <p class="text-sm mt-1">{{ resultado.erro }}</p>
        </div>
      </div>

      <div v-else class="bg-gradient-to-br from-emerald-900 to-emerald-800 border border-emerald-700 rounded-lg p-6">
        <div class="flex items-center gap-3 mb-6">
          <span class="text-3xl">✅</span>
          <h3 class="text-xl font-bold text-emerald-100">Cobrança Encontrada</h3>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="bg-emerald-800 rounded-lg p-4 border border-emerald-700">
            <p class="text-xs text-emerald-300 uppercase tracking-wide font-semibold">ID da Cobrança</p>
            <p class="text-2xl font-bold text-emerald-100 mt-2">{{ resultado.data?.id || resultado.id }}</p>
          </div>

          <div class="bg-emerald-800 rounded-lg p-4 border border-emerald-700">
            <p class="text-xs text-emerald-300 uppercase tracking-wide font-semibold">Valor</p>
            <p class="text-2xl font-bold text-emerald-300 mt-2">{{ formatarValor(resultado.data?.value || resultado.value) }}</p>
          </div>

          <div class="bg-emerald-800 rounded-lg p-4 border border-emerald-700">
            <p class="text-xs text-emerald-300 uppercase tracking-wide font-semibold">Status</p>
            <span :class="[
              'inline-block px-3 py-2 rounded-full text-xs font-bold mt-2',
              (resultado.data?.status || resultado.status) === 'paid'
                ? 'bg-emerald-600 text-emerald-100'
                : 'bg-amber-600 text-amber-100'
            ]">
              {{ (resultado.data?.status || resultado.status) === 'paid' ? '✓ Pago' : '⏳ Pendente' }}
            </span>
          </div>

          <div class="bg-emerald-800 rounded-lg p-4 border border-emerald-700">
            <p class="text-xs text-emerald-300 uppercase tracking-wide font-semibold">Data de Criação</p>
            <p class="text-sm text-emerald-100 mt-2">{{ formatarData(resultado.data?.created_at || resultado.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  resultado: Object,
  carregando: Boolean
})

const emit = defineEmits(['buscar'])

const placa = ref('')

const buscar = () => {
  if (!placa.value.trim()) {
    alert('Digite uma placa!')
    return
  }
  emit('buscar', placa.value.toUpperCase())
}

const formatarValor = (valor) => {
  if (!valor || isNaN(valor)) return 'R$ 0,00'
  const num = parseFloat(valor)
  return 'R$ ' + num.toFixed(2).replace('.', ',')
}

const formatarData = (data) => {
  if (!data) return ''
  const d = new Date(data)
  return d.toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
