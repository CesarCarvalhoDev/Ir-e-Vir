<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { CircleDollarSign, Plus } from 'lucide-vue-next'
import { RouterLink } from 'vue-router'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { dateTime, money } from '@/utils/format'
import StatePanel from '@/components/StatePanel.vue'

const tariffs = useQuery({ queryKey: ['tariffs'], queryFn: apiService.getTariffs })
const activeCount = computed(() => tariffs.data.value?.filter((tariff) => tariff.active).length ?? 0)
</script>

<template>
  <section class="page-head page-head-row">
    <div><p class="eyebrow">Administração</p><h1>Tarifas</h1><p>Valores por hora e períodos de vigência das zonas.</p></div>
    <RouterLink to="/admin/tarifas/nova" class="command-link"><Plus />Adicionar nova tarifa</RouterLink>
  </section>

  <section class="metric-grid">
    <article class="metric"><CircleDollarSign /><span>Tarifas ativas</span><strong>{{ activeCount }}</strong></article>
  </section>

  <section class="section-block"><div class="section-title"><div><h2>Lista de tarifas</h2><p>GET /api/admin/tariffs</p></div></div>
    <StatePanel :loading="tariffs.isLoading.value" :error="tariffs.error.value ? getApiError(tariffs.error.value) : null" :empty="!tariffs.data.value?.length">
      <div class="table-wrap"><table><thead><tr><th>ID</th><th>Zona</th><th>Valor por hora</th><th>Início</th><th>Fim</th><th>Status</th></tr></thead><tbody><tr v-for="tariff in tariffs.data.value" :key="tariff.id"><td>#{{ tariff.id }}</td><td>{{ tariff.zone?.name ?? `Zona #${tariff.zone_id}` }}</td><td>{{ money(tariff.hourly_rate) }}</td><td>{{ dateTime(tariff.start_date) }}</td><td>{{ dateTime(tariff.end_date) }}</td><td><span :class="tariff.active ? 'active' : 'canceled'" class="badge">{{ tariff.active ? 'Ativa' : 'Inativa' }}</span></td></tr></tbody></table></div>
    </StatePanel>
  </section>
</template>
