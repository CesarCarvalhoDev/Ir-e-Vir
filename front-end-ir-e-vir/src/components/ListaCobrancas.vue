<template>
  <div>
    <div v-if="carregando" class="flex flex-col items-center justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4"></div>
      <p class="text-slate-400">Carregando cobranças...</p>
    </div>

    <div v-else-if="!cobracas || cobracas.length === 0" class="text-center py-12">
      <p class="text-2xl text-slate-400">📭</p>
      <p class="text-slate-400 mt-2">Nenhuma cobrança encontrada</p>
    </div>

    <div v-else class="space-y-3 max-h-96 overflow-y-auto pr-2">
      <div
        v-for="cobranca in cobracas"
        :key="cobranca.id"
        class="border border-slate-600 rounded-lg p-4 hover:border-blue-500 hover:bg-slate-700 transition-all duration-200 bg-slate-700"
      >
        <div class="flex justify-between items-start mb-3">
          <div>
            <h3 class="font-bold text-white text-lg">Cobrança #{{ cobranca.id }}</h3>
            <p class="text-sm text-slate-400">Stay ID: <span class="text-slate-300 font-semibold">{{ cobranca.stay_id }}</span></p>
          </div>
          <div class="text-right">
            <p class="text-2xl font-bold text-emerald-400">
              {{ formatarValor(cobranca.value) }}
            </p>
            <span
              :class="[
                'text-xs px-3 py-1 rounded-full font-semibold inline-block mt-1',
                cobranca.status === 'paid'
                  ? 'bg-emerald-900 text-emerald-300'
                  : 'bg-amber-900 text-amber-300'
              ]"
            >
              {{ cobranca.status === 'paid' ? '✓ Pago' : '⏳ Pendente' }}
            </span>
          </div>
        </div>

        <p v-if="cobranca.description" class="text-sm text-slate-300 mb-3 pl-4 border-l-2 border-slate-500">
          {{ cobranca.description }}
        </p>

        <p class="text-xs text-slate-500">
          📅 {{ formatarData(cobranca.created_at) }}
        </p>
      </div>
    </div>

    <button
      @click="$emit('atualizar')"
      class="mt-6 w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
    >
      <span>🔄</span> Atualizar
    </button>
  </div>
</template>

<script setup>
defineProps({
  cobracas: Array,
  carregando: Boolean
})

defineEmits(['atualizar'])

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
