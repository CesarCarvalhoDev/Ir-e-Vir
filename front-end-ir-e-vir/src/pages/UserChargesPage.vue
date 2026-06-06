<script setup lang="ts">
import { computed } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { CircleDollarSign } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { dateTime, money } from '@/utils/format'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import StatePanel from '@/components/StatePanel.vue'
import StatusBadge from '@/components/StatusBadge.vue'

const ui = useUiStore()
const auth = useAuthStore()
const client = useQueryClient()
const charges = useQuery({ queryKey: ['me', 'charges'], queryFn: apiService.getMyCharges })
const pendingTotal = computed(() => charges.data.value?.filter((charge) => charge.status === 'PENDING').reduce((sum, charge) => sum + Number(charge.value), 0) ?? 0)
const payMutation = useMutation({
  mutationFn: (chargeId: number) => apiService.payMyCharge(chargeId),
  onSuccess: async (response) => {
    ui.notify('success', `${response.message} Saldo restante: ${money(response.remaining_balance)}`)
    await Promise.all([client.invalidateQueries({ queryKey: ['me', 'charges'] }), auth.refreshMe()])
  },
  onError: (error) => ui.notify('error', getApiError(error)),
})
</script>

<template>
  <section class="page-head"><div><p class="eyebrow">Área do usuário</p><h1>Minhas cobranças</h1><p>Cobranças pendentes e pagas dos seus veículos.</p></div></section>
  <section class="metric-grid"><article class="metric"><CircleDollarSign /><span>Total pendente</span><strong>{{ money(pendingTotal) }}</strong></article></section>
  <section class="section-block"><div class="section-title"><div><h2>Extrato</h2><p>GET /api/me/charges</p></div></div>
    <StatePanel :loading="charges.isLoading.value" :error="charges.error.value ? getApiError(charges.error.value) : null" :empty="!charges.data.value?.length">
      <div class="charge-grid">
        <article v-for="charge in charges.data.value" :key="charge.id" class="charge-card">
          <div><small>Cobrança #{{ charge.id }}</small><StatusBadge :status="charge.status" /></div>
          <strong>{{ money(charge.value) }}</strong>
          <p>Permanência #{{ charge.stay_id }} · vence em {{ dateTime(charge.due_date) }}</p>
          <span v-if="charge.payment">Pago em {{ dateTime(charge.payment.payment_date) }} · {{ money(charge.payment.paid_value) }}</span>
          <button v-if="charge.status === 'PENDING'" class="danger compact-button" :disabled="payMutation.isPending.value" @click="payMutation.mutate(charge.id)">Pagar</button>
        </article>
      </div>
    </StatePanel>
  </section>
</template>
