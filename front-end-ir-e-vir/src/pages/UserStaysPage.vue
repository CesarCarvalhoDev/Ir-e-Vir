<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { Clock3 } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { dateTime } from '@/utils/format'
import StatePanel from '@/components/StatePanel.vue'
import StatusBadge from '@/components/StatusBadge.vue'

const stays = useQuery({ queryKey: ['me', 'stays'], queryFn: apiService.getMyStays })
const active = computed(() => stays.data.value?.filter((stay) => stay.status === 'ACTIVE').length ?? 0)
</script>

<template>
  <section class="page-head"><div><p class="eyebrow">Área do usuário</p><h1>Minhas permanências</h1><p>Histórico dos veículos vinculados à sua conta.</p></div></section>
  <section class="metric-grid"><article class="metric"><Clock3 /><span>Permanências ativas</span><strong>{{ active }}</strong></article></section>
  <section class="section-block"><div class="section-title"><div><h2>Histórico</h2><p>GET /api/me/stays</p></div></div>
    <StatePanel :loading="stays.isLoading.value" :error="stays.error.value ? getApiError(stays.error.value) : null" :empty="!stays.data.value?.length">
      <div class="table-wrap"><table><thead><tr><th>ID</th><th>Veículo</th><th>Zona</th><th>Entrada</th><th>Saída</th><th>Tempo</th><th>Status</th></tr></thead><tbody><tr v-for="stay in stays.data.value" :key="stay.id"><td>#{{ stay.id }}</td><td>#{{ stay.vehicle_id }}</td><td>#{{ stay.zone_id }}</td><td>{{ dateTime(stay.entry) }}</td><td>{{ dateTime(stay.exit) }}</td><td>{{ stay.total_time ?? '—' }} min</td><td><StatusBadge :status="stay.status" /></td></tr></tbody></table></div>
    </StatePanel>
  </section>
</template>
