<script setup lang="ts">
import { useQuery } from '@tanstack/vue-query'
import { CarFront } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import StatePanel from '@/components/StatePanel.vue'

const vehicles = useQuery({ queryKey: ['vehicles'], queryFn: apiService.getVehicles })
</script>

<template>
  <section class="page-head"><div><p class="eyebrow">Administração</p><h1>Veículos</h1><p>Frota reconhecida pelo sistema Free Flow.</p></div></section>
  <section class="metric-grid"><article class="metric"><CarFront /><span>Veículos</span><strong>{{ vehicles.data.value?.length ?? 0 }}</strong></article></section>
  <section class="section-block"><div class="section-title"><div><h2>Cadastro de veículos</h2><p>GET /api/admin/vehicles</p></div></div>
    <StatePanel :loading="vehicles.isLoading.value" :error="vehicles.error.value ? getApiError(vehicles.error.value) : null" :empty="!vehicles.data.value?.length">
      <div class="table-wrap"><table><thead><tr><th>ID</th><th>Placa</th><th>Tipo</th><th>Registrado</th></tr></thead><tbody><tr v-for="vehicle in vehicles.data.value" :key="vehicle.id"><td>#{{ vehicle.id }}</td><td>{{ vehicle.plate }}</td><td>{{ vehicle.type ?? '—' }}</td><td>{{ vehicle.has_registration ? 'Sim' : 'Não' }}</td></tr></tbody></table></div>
    </StatePanel>
  </section>
</template>
