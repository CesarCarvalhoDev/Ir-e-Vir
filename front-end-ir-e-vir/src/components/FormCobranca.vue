<template>
  <form @submit.prevent="submeter" class="space-y-5">
    <div>
      <label for="stay_id" class="block text-sm font-semibold text-slate-200 mb-2">
        🎫 ID da Permanência (Stay)
      </label>
      <input
        id="stay_id"
        v-model.number="formulario.stay_id"
        type="number"
        required
        class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-slate-400 transition-all"
        placeholder="Ex: 1"
      />
      <p class="text-xs text-slate-400 mt-2">💡 O valor será calculado automaticamente pela tarifa</p>
    </div>

    <button
      type="submit"
      class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 active:scale-95"
    >
      <span>⚡</span> Gerar Cobrança
    </button>
  </form>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['submit'])

const formulario = ref({
  stay_id: null
})

const submeter = () => {
  if (!formulario.value.stay_id) {
    alert('Por favor preencha o ID da Permanência!')
    return
  }
  emit('submit', formulario.value)
  formulario.value = { stay_id: null }
}
</script>
