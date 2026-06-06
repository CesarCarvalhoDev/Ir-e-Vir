<script setup lang="ts">
import { reactive } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { CarFront, Plus } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import StatePanel from '@/components/StatePanel.vue'

const ui = useUiStore()
const auth = useAuthStore()
const client = useQueryClient()
const vehicles = useQuery({ queryKey: ['me', 'vehicles'], queryFn: apiService.getMyVehicles })
const form = reactive({ plate: '', type: 'CAR' })
const linkMutation = useMutation({
  mutationFn: () => apiService.linkMyVehicle({ plate: form.plate, type: form.type }),
  onSuccess: async () => {
    ui.notify('success', 'Veículo vinculado.')
    form.plate = ''
    await Promise.all([client.invalidateQueries({ queryKey: ['me', 'vehicles'] }), auth.refreshMe()])
  },
  onError: (error) => ui.notify('error', getApiError(error)),
})
</script>

<template>
  <section class="page-head"><div><p class="eyebrow">Área do usuário</p><h1>Meus veículos</h1><p>Veículos vinculados à sua conta.</p></div></section>
  <section class="form-grid">
    <form class="panel" @submit.prevent="linkMutation.mutate()"><div class="panel-title"><Plus /><h2>Vincular veículo</h2></div><label>Placa<input v-model.trim="form.plate" required placeholder="ABC-1234" /></label><label>Tipo<select v-model="form.type"><option>CAR</option><option>MOTORCYCLE</option></select></label><button class="primary" :disabled="linkMutation.isPending.value">Vincular</button></form>
    <article class="panel"><div class="panel-title"><CarFront /><h2>Resumo</h2></div><div class="metric-inline"><strong>{{ vehicles.data.value?.length ?? 0 }}</strong><span>veículos vinculados</span></div></article>
  </section>
  <section class="section-block"><div class="section-title"><div><h2>Lista de veículos</h2><p>GET /api/me/vehicles</p></div></div>
    <StatePanel :loading="vehicles.isLoading.value" :error="vehicles.error.value ? getApiError(vehicles.error.value) : null" :empty="!vehicles.data.value?.length">
      <div class="charge-grid"><article v-for="vehicle in vehicles.data.value" :key="vehicle.id" class="charge-card"><div><small>Veículo #{{ vehicle.id }}</small></div><strong>{{ vehicle.plate }}</strong><p>{{ vehicle.type ?? 'Tipo não informado' }}</p></article></div>
    </StatePanel>
  </section>
</template>
