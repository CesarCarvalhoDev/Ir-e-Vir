<script setup lang="ts">
import { reactive } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { LogIn, LogOut } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { dateTime, localDateTimeValue } from '@/utils/format'
import { useUiStore } from '@/stores/ui'
import StatePanel from '@/components/StatePanel.vue'
import StatusBadge from '@/components/StatusBadge.vue'
const ui = useUiStore(); const client = useQueryClient()
const stays = useQuery({ queryKey: ['stays'], queryFn: apiService.getStays })
const vehicles = useQuery({ queryKey: ['vehicles'], queryFn: apiService.getVehicles })
const zones = useQuery({ queryKey: ['zones'], queryFn: apiService.getZones })
const entry = reactive({ entry: localDateTimeValue(), plate: '', zone_id: 0 })
const exit = reactive({ exit: localDateTimeValue(), plate: '' })
const refresh = () => client.invalidateQueries({ queryKey: ['stays'] })
const entryMutation = useMutation({ mutationFn: apiService.createEntry, onSuccess: () => { ui.notify('success', 'Entrada registrada.'); refresh() }, onError: e => ui.notify('error', getApiError(e)) })
const exitMutation = useMutation({ mutationFn: apiService.createExit, onSuccess: () => { ui.notify('success', 'Saída processada.'); refresh(); client.invalidateQueries({ queryKey: ['charges'] }); client.invalidateQueries({ queryKey: ['fines'] }) }, onError: e => { ui.notify('error', getApiError(e)); refresh(); client.invalidateQueries({ queryKey: ['charges'] }); client.invalidateQueries({ queryKey: ['fines'] }) } })
</script>
<template>
  <section class="page-head"><div><p class="eyebrow">Controle de estacionamento</p><h1>Permanências</h1><p>Registre entradas e saídas com os campos exigidos pelo back-end.</p></div></section>
  <section class="form-grid">
    <form class="panel" @submit.prevent="entryMutation.mutate({ ...entry })"><div class="panel-title"><LogIn /><h2>Registrar entrada</h2></div>
      <label>Veículo<select v-model="entry.plate" required><option value="" disabled>Selecione a placa</option><option v-for="v in vehicles.data.value" :key="v.id" :value="v.plate">{{ v.plate }} · {{ v.type ?? 'Tipo não informado' }}</option></select></label>
      <label>Zona<select v-model.number="entry.zone_id" required><option :value="0" disabled>Selecione a zona</option><option v-for="z in zones.data.value" :key="z.id" :value="z.id">{{ z.name }} · máximo {{ z.maximum_time }} min</option></select></label>
      <label>Data e hora<input v-model="entry.entry" type="datetime-local" required /></label><button :disabled="entryMutation.isPending.value" class="primary">Registrar entrada</button>
    </form>
    <form class="panel" @submit.prevent="exitMutation.mutate({ ...exit })"><div class="panel-title"><LogOut /><h2>Registrar saída</h2></div>
      <label>Veículo<select v-model="exit.plate" required><option value="" disabled>Selecione a placa</option><option v-for="v in vehicles.data.value" :key="v.id" :value="v.plate">{{ v.plate }}</option></select></label>
      <label>Data e hora<input v-model="exit.exit" type="datetime-local" required /></label><button :disabled="exitMutation.isPending.value" class="danger">Registrar saída</button>
    </form>
  </section>
  <section class="section-block"><div class="section-title"><div><h2>Histórico de permanências</h2><p>GET /api/admin/stays</p></div></div>
    <StatePanel :loading="stays.isLoading.value" :error="stays.error.value ? getApiError(stays.error.value) : null" :empty="!stays.data.value?.length">
      <div class="table-wrap"><table><thead><tr><th>ID</th><th>Veículo</th><th>Zona</th><th>Entrada</th><th>Saída</th><th>Tempo</th><th>Status</th></tr></thead><tbody><tr v-for="stay in stays.data.value" :key="stay.id"><td>#{{ stay.id }}</td><td>#{{ stay.vehicle_id }}</td><td>#{{ stay.zone_id }}</td><td>{{ dateTime(stay.entry) }}</td><td>{{ dateTime(stay.exit) }}</td><td>{{ stay.total_time ?? '—' }} min</td><td><StatusBadge :status="stay.status" /></td></tr></tbody></table></div>
    </StatePanel>
  </section>
</template>
