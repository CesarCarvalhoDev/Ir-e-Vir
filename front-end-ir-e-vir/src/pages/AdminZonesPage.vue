<script setup lang="ts">
import { useQuery } from '@tanstack/vue-query'
import { MapPin, Plus } from 'lucide-vue-next'
import { RouterLink } from 'vue-router'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import StatePanel from '@/components/StatePanel.vue'

const zones = useQuery({ queryKey: ['zones'], queryFn: apiService.getZones })
</script>

<template>
  <section class="page-head page-head-row">
    <div><p class="eyebrow">Administração</p><h1>Zonas</h1><p>Zonas de estacionamento e tempo máximo permitido.</p></div>
    <RouterLink to="/admin/zonas/nova" class="command-link"><Plus />Adicionar nova zona</RouterLink>
  </section>
  <section class="metric-grid"><article class="metric"><MapPin /><span>Zonas ativas</span><strong>{{ zones.data.value?.length ?? 0 }}</strong></article></section>
  <section class="section-block"><div class="section-title"><div><h2>Lista de zonas</h2><p>GET /api/admin/zones</p></div></div>
    <StatePanel :loading="zones.isLoading.value" :error="zones.error.value ? getApiError(zones.error.value) : null" :empty="!zones.data.value?.length">
      <div class="table-wrap"><table><thead><tr><th>ID</th><th>Nome</th><th>Tempo máximo</th></tr></thead><tbody><tr v-for="zone in zones.data.value" :key="zone.id"><td>#{{ zone.id }}</td><td>{{ zone.name }}</td><td>{{ zone.maximum_time }} min</td></tr></tbody></table></div>
    </StatePanel>
  </section>
</template>
