<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { CreditCard, Plus, Search } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { dateTime, money } from '@/utils/format'
import { useUiStore } from '@/stores/ui'
import type { Charge } from '@/types/api'
import StatePanel from '@/components/StatePanel.vue'
import StatusBadge from '@/components/StatusBadge.vue'
const ui = useUiStore(); const client = useQueryClient()
const charges = useQuery({ queryKey: ['charges'], queryFn: apiService.getCharges })
const stays = useQuery({ queryKey: ['stays'], queryFn: apiService.getStays })
const createStayId = ref(0); const searchPlate = ref(''); const found = ref<Charge | null>(null)
const payment = reactive({ chargeId: 0, userId: 0 })
const refresh = () => client.invalidateQueries({ queryKey: ['charges'] })
const createMutation = useMutation({ mutationFn: apiService.createCharge, onSuccess: () => { ui.notify('success', 'Cobrança gerada.'); refresh() }, onError: e => ui.notify('error', getApiError(e)) })
const searchMutation = useMutation({ mutationFn: apiService.getChargeByPlate, onSuccess: v => { found.value = v }, onError: e => { found.value = null; ui.notify('error', getApiError(e)) } })
const payMutation = useMutation({ mutationFn: () => apiService.payCharge(payment.chargeId, payment.userId), onSuccess: r => { ui.notify('success', `${r.message} Saldo restante: ${money(r.remaining_balance)}`); refresh() }, onError: e => ui.notify('error', getApiError(e)) })
</script>
<template>
  <section class="page-head"><div><p class="eyebrow">Financeiro</p><h1>Cobranças</h1><p>Consulte, gere e pague cobranças pelos endpoints existentes.</p></div></section>
  <section class="form-grid three">
    <form class="panel" @submit.prevent="createMutation.mutate(createStayId)"><div class="panel-title"><Plus /><h2>Gerar cobrança</h2></div><label>Permanência<select v-model.number="createStayId" required><option :value="0" disabled>Selecione</option><option v-for="s in stays.data.value" :key="s.id" :value="s.id">#{{ s.id }} · veículo #{{ s.vehicle_id }}</option></select></label><button class="primary">Gerar</button></form>
    <form class="panel" @submit.prevent="searchMutation.mutate(searchPlate)"><div class="panel-title"><Search /><h2>Buscar por placa</h2></div><label>Placa<input v-model.trim="searchPlate" required placeholder="ABC-1234" /></label><button class="primary">Buscar</button><div v-if="found" class="compact-result"><strong>{{ money(found.value) }}</strong><StatusBadge :status="found.status" /></div></form>
    <form class="panel" @submit.prevent="payMutation.mutate()"><div class="panel-title"><CreditCard /><h2>Pagar cobrança</h2></div><label>ID da cobrança<input v-model.number="payment.chargeId" type="number" min="1" required /></label><label>ID do usuário<input v-model.number="payment.userId" type="number" min="1" required /></label><button class="danger">Pagar com carteira</button></form>
  </section>
  <section class="section-block"><div class="section-title"><div><h2>Extrato de cobranças</h2><p>GET /api/admin/charges</p></div></div>
    <StatePanel :loading="charges.isLoading.value" :error="charges.error.value ? getApiError(charges.error.value) : null" :empty="!charges.data.value?.length">
      <div class="charge-grid"><article v-for="charge in charges.data.value" :key="charge.id" class="charge-card"><div><small>Cobrança #{{ charge.id }}</small><StatusBadge :status="charge.status" /></div><strong>{{ money(charge.value) }}</strong><p>Permanência #{{ charge.stay_id }}</p><span>Vence em {{ dateTime(charge.due_date) }}</span><span v-if="charge.payment">Pago em {{ dateTime(charge.payment.payment_date) }} · {{ money(charge.payment.paid_value) }}</span></article></div>
    </StatePanel>
  </section>
</template>
