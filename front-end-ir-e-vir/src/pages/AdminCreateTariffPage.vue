<script setup lang="ts">
import { reactive } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { useRouter } from 'vue-router'
import { ArrowLeft, CircleDollarSign, Save } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { useUiStore } from '@/stores/ui'
import StatePanel from '@/components/StatePanel.vue'

const toLocalValue = (date: Date) => {
  const value = new Date(date)
  value.setMinutes(value.getMinutes() - value.getTimezoneOffset())
  return value.toISOString().slice(0, 16)
}

const start = new Date()
const end = new Date()
end.setMonth(end.getMonth() + 1)

const router = useRouter()
const queryClient = useQueryClient()
const ui = useUiStore()
const zones = useQuery({ queryKey: ['zones'], queryFn: apiService.getZones })
const form = reactive({
  hourly_rate: 10,
  start_date: toLocalValue(start),
  end_date: toLocalValue(end),
  active: true,
  zone_id: 0,
})

const createMutation = useMutation({
  mutationFn: apiService.createTariff,
  onSuccess: async () => {
    await queryClient.invalidateQueries({ queryKey: ['tariffs'] })
    ui.notify('success', 'Tarifa adicionada com sucesso.')
    router.push('/admin/tarifas')
  },
  onError: (error) => ui.notify('error', getApiError(error)),
})

function submit() {
  createMutation.mutate({
    hourly_rate: Number(form.hourly_rate),
    start_date: form.start_date,
    end_date: form.end_date,
    active: Boolean(form.active),
    zone_id: Number(form.zone_id),
  })
}
</script>

<template>
  <section class="page-head page-head-row">
    <div><p class="eyebrow">Administração</p><h1>Adicionar tarifa</h1><p>Configure o valor por hora e o período de vigência.</p></div>
    <button class="secondary-command" type="button" @click="router.push('/admin/tarifas')"><ArrowLeft />Voltar</button>
  </section>

  <section class="form-page">
    <StatePanel :loading="zones.isLoading.value" :error="zones.error.value ? getApiError(zones.error.value) : null" :empty="!zones.data.value?.length" empty-text="Cadastre uma zona antes de criar a tarifa.">
      <form class="panel entity-form" @submit.prevent="submit">
        <div class="panel-title"><CircleDollarSign /><h2>Dados da tarifa</h2></div>
        <label>Zona<select v-model.number="form.zone_id" required><option :value="0" disabled>Selecione uma zona</option><option v-for="zone in zones.data.value" :key="zone.id" :value="zone.id">{{ zone.name }}</option></select></label>
        <label>Valor por hora<input v-model.number="form.hourly_rate" type="number" min="0.01" step="0.01" required /></label>
        <label>Início da vigência<input v-model="form.start_date" type="datetime-local" required /></label>
        <label>Fim da vigência<input v-model="form.end_date" type="datetime-local" :min="form.start_date" required /></label>
        <label class="toggle-field"><input v-model="form.active" type="checkbox" /><span>Tarifa ativa</span></label>
        <div class="form-actions">
          <button class="secondary-command" type="button" @click="router.push('/admin/tarifas')"><ArrowLeft />Cancelar</button>
          <button class="primary command-button" type="submit" :disabled="createMutation.isPending.value"><Save />{{ createMutation.isPending.value ? 'Salvando...' : 'Salvar tarifa' }}</button>
        </div>
      </form>
    </StatePanel>
  </section>
</template>
