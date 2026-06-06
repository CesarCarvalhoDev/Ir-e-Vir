<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { AlertTriangle, CarFront, CircleDollarSign, Clock3, MapPin } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { money } from '@/utils/format'
import StatePanel from '@/components/StatePanel.vue'
import StatusBadge from '@/components/StatusBadge.vue'

const zones = useQuery({ queryKey: ['zones'], queryFn: apiService.getZones })
const vehicles = useQuery({ queryKey: ['vehicles'], queryFn: apiService.getVehicles })
const stays = useQuery({ queryKey: ['stays'], queryFn: apiService.getStays })
const charges = useQuery({ queryKey: ['charges'], queryFn: apiService.getCharges })
const fines = useQuery({ queryKey: ['fines'], queryFn: apiService.getFines })
const active = computed(() => stays.data.value?.filter(s => s.status === 'ACTIVE').length ?? 0)
const pending = computed(() => charges.data.value?.filter(c => c.status === 'PENDING').reduce((sum, c) => sum + Number(c.value), 0) ?? 0)
const activeFines = computed(() => fines.data.value?.filter(f => f.status === 'active').length ?? 0)
const loading = computed(() => zones.isLoading.value || vehicles.isLoading.value || stays.isLoading.value || charges.isLoading.value || fines.isLoading.value)
const error = computed(() => [zones.error.value, vehicles.error.value, stays.error.value, charges.error.value, fines.error.value].find(Boolean))
</script>

<template>
  <section class="page-head"><div><p class="eyebrow">Área administrativa</p><h1>Olá, seja bem-vindo!</h1><p>Acompanhe os dados operacionais disponíveis para administradores.</p></div></section>
  <StatePanel :loading="loading" :error="error ? getApiError(error) : null">
    <section class="metric-grid">
      <article class="metric"><CarFront /><span>Veículos</span><strong>{{ vehicles.data.value?.length ?? 0 }}</strong></article>
      <article class="metric"><Clock3 /><span>Permanências ativas</span><strong>{{ active }}</strong></article>
      <article class="metric"><CircleDollarSign /><span>Total pendente</span><strong>{{ money(pending) }}</strong></article>
      <article class="metric"><AlertTriangle /><span>Multas ativas</span><strong>{{ activeFines }}</strong></article>
      <article class="metric"><MapPin /><span>Zonas</span><strong>{{ zones.data.value?.length ?? 0 }}</strong></article>
    </section>
    <section class="section-block"><div class="section-title"><div><h2>Últimas cobranças</h2><p>Dados retornados por GET /api/admin/charges</p></div></div>
      <div class="list">
        <article v-for="charge in charges.data.value?.slice(-5).reverse()" :key="charge.id" class="list-row">
          <div class="row-icon"><CircleDollarSign /></div><div class="grow"><strong>Cobrança #{{ charge.id }}</strong><span>Permanência #{{ charge.stay_id }}</span></div><strong>{{ money(charge.value) }}</strong><StatusBadge :status="charge.status" />
        </article>
        <div v-if="!charges.data.value?.length" class="state-panel">Nenhuma cobrança encontrada.</div>
      </div>
    </section>
    <aside class="notice"><AlertTriangle /><div><strong>Recursos respeitam a API atual</strong><p>Cadastro público e depósito direto não aparecem porque não possuem endpoints ativos.</p></div></aside>
  </StatePanel>
</template>
