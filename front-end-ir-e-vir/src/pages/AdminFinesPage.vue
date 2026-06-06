<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { FileWarning } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { dateTime, money, reasonLabel } from '@/utils/format'
import StatePanel from '@/components/StatePanel.vue'
import StatusBadge from '@/components/StatusBadge.vue'

const fines = useQuery({ queryKey: ['fines'], queryFn: apiService.getFines })
const activeTotal = computed(() => fines.data.value?.filter((fine) => fine.status === 'active').reduce((sum, fine) => sum + Number(fine.amount), 0) ?? 0)
</script>

<template>
  <section class="page-head"><div><p class="eyebrow">Administração</p><h1>Multas</h1><p>Multas por saldo insuficiente e tempo máximo excedido.</p></div></section>
  <section class="metric-grid"><article class="metric"><FileWarning /><span>Total ativo</span><strong>{{ money(activeTotal) }}</strong></article></section>
  <section class="section-block"><div class="section-title"><div><h2>Histórico de multas</h2><p>GET /api/admin/fines</p></div></div>
    <StatePanel :loading="fines.isLoading.value" :error="fines.error.value ? getApiError(fines.error.value) : null" :empty="!fines.data.value?.length">
      <div class="table-wrap"><table><thead><tr><th>ID</th><th>Usuário</th><th>Motivo</th><th>Valor</th><th>Permanência</th><th>Veículo</th><th>Início</th><th>Status</th></tr></thead><tbody><tr v-for="fine in fines.data.value" :key="fine.id"><td>#{{ fine.id }}</td><td>{{ fine.user?.name ?? `#${fine.user_id}` }}</td><td>{{ reasonLabel(fine.reason) }}</td><td>{{ money(fine.amount) }}</td><td>#{{ fine.stay_id }}</td><td>{{ fine.vehicle?.plate ?? '—' }}</td><td>{{ dateTime(fine.started_at) }}</td><td><StatusBadge :status="fine.status" /></td></tr></tbody></table></div>
    </StatePanel>
  </section>
</template>
