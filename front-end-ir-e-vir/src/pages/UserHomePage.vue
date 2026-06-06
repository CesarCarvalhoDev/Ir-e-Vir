<script setup lang="ts">
import { computed, reactive } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { CarFront, CircleDollarSign, Clock3, FileWarning, Plus, Wallet } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import { dateTime, money } from '@/utils/format'
import StatePanel from '@/components/StatePanel.vue'
import StatusBadge from '@/components/StatusBadge.vue'

const auth = useAuthStore()
const ui = useUiStore()
const client = useQueryClient()
const vehicles = useQuery({ queryKey: ['me', 'vehicles'], queryFn: apiService.getMyVehicles })
const stays = useQuery({ queryKey: ['me', 'stays'], queryFn: apiService.getMyStays })
const charges = useQuery({ queryKey: ['me', 'charges'], queryFn: apiService.getMyCharges })
const fines = useQuery({ queryKey: ['me', 'fines'], queryFn: apiService.getMyFines })
const form = reactive({ plate: '', type: 'CAR' })

const activeStays = computed(() => stays.data.value?.filter((stay) => stay.status === 'ACTIVE') ?? [])
const pendingCharges = computed(() => charges.data.value?.filter((charge) => charge.status === 'PENDING') ?? [])
const activeFines = computed(() => fines.data.value?.filter((fine) => fine.status === 'active') ?? [])
const shouldOnboard = computed(() => !vehicles.isLoading.value && !stays.isLoading.value && (!vehicles.data.value?.length || !stays.data.value?.length))

const linkMutation = useMutation({
  mutationFn: () => apiService.linkMyVehicle({ plate: form.plate, type: form.type }),
  onSuccess: async () => {
    ui.notify('success', 'Veículo vinculado com sucesso.')
    form.plate = ''
    await Promise.all([
      client.invalidateQueries({ queryKey: ['me', 'vehicles'] }),
      client.invalidateQueries({ queryKey: ['me', 'stays'] }),
      auth.refreshMe(),
    ])
  },
  onError: (err) => ui.notify('error', getApiError(err)),
})

const payMutation = useMutation({
  mutationFn: (chargeId: number) => apiService.payMyCharge(chargeId),
  onSuccess: async (response) => {
    ui.notify('success', `${response.message} Saldo restante: ${money(response.remaining_balance)}`)
    await Promise.all([
      client.invalidateQueries({ queryKey: ['me', 'charges'] }),
      auth.refreshMe(),
    ])
  },
  onError: (err) => ui.notify('error', getApiError(err)),
})
</script>

<template>
  <section class="page-head">
    <div>
      <p class="eyebrow">Área do usuário</p>
      <h1>Olá, {{ auth.user?.name }}</h1>
      <p>Consulte seus veículos, permanências e cobranças vinculadas à sua conta.</p>
    </div>
  </section>

  <section class="metric-grid">
    <article class="metric"><CarFront /><span>Veículos vinculados</span><strong>{{ vehicles.data.value?.length ?? 0 }}</strong></article>
    <article class="metric"><Clock3 /><span>Permanências ativas</span><strong>{{ activeStays.length }}</strong></article>
    <article class="metric"><CircleDollarSign /><span>Cobranças pendentes</span><strong>{{ pendingCharges.length }}</strong></article>
    <article class="metric"><FileWarning /><span>Multas ativas</span><strong>{{ activeFines.length }}</strong></article>
    <article class="metric"><Wallet /><span>Saldo da carteira</span><strong>{{ money(auth.user?.wallet?.balance ?? auth.user?.available_balance) }}</strong></article>
  </section>

  <section v-if="shouldOnboard" class="section-block">
    <form class="panel onboarding" @submit.prevent="linkMutation.mutate()">
      <div class="panel-title"><Plus /><h2>Vincule seu primeiro veículo</h2></div>
      <p>Nenhuma permanência foi encontrada para sua conta. Cadastre um veículo usando os campos exigidos pela API.</p>
      <label>Placa<input v-model.trim="form.plate" required placeholder="ABC-1234" /></label>
      <label>Tipo do veículo<select v-model="form.type"><option>CAR</option><option>MOTORCYCLE</option></select></label>
      <button class="primary" :disabled="linkMutation.isPending.value">Vincular veículo</button>
    </form>
  </section>

  <section class="section-block">
    <div class="section-title"><div><h2>Meus veículos</h2><p>GET /api/me/vehicles</p></div></div>
    <StatePanel :loading="vehicles.isLoading.value" :error="vehicles.error.value ? getApiError(vehicles.error.value) : null" :empty="!vehicles.data.value?.length">
      <div class="charge-grid"><article v-for="vehicle in vehicles.data.value" :key="vehicle.id" class="charge-card"><div><small>Veículo #{{ vehicle.id }}</small></div><strong>{{ vehicle.plate }}</strong><p>{{ vehicle.type ?? 'Tipo não informado' }}</p></article></div>
    </StatePanel>
  </section>

  <section class="section-block">
    <div class="section-title"><div><h2>Histórico de permanências</h2><p>GET /api/me/stays</p></div></div>
    <StatePanel :loading="stays.isLoading.value" :error="stays.error.value ? getApiError(stays.error.value) : null" :empty="!stays.data.value?.length">
      <div class="list"><article v-for="stay in stays.data.value" :key="stay.id" class="list-row"><div class="grow"><strong>Permanência #{{ stay.id }}</strong><span>{{ dateTime(stay.entry) }} até {{ dateTime(stay.exit) }}</span></div><StatusBadge :status="stay.status" /></article></div>
    </StatePanel>
  </section>

  <section class="section-block">
    <div class="section-title"><div><h2>Minhas cobranças</h2><p>GET /api/me/charges</p></div></div>
    <StatePanel :loading="charges.isLoading.value" :error="charges.error.value ? getApiError(charges.error.value) : null" :empty="!charges.data.value?.length">
      <div class="charge-grid">
        <article v-for="charge in charges.data.value" :key="charge.id" class="charge-card">
          <div><small>Cobrança #{{ charge.id }}</small><StatusBadge :status="charge.status" /></div>
          <strong>{{ money(charge.value) }}</strong>
          <p>Vence em {{ dateTime(charge.due_date) }}</p>
          <button v-if="charge.status === 'PENDING'" class="danger compact-button" @click="payMutation.mutate(charge.id)">Pagar</button>
        </article>
      </div>
    </StatePanel>
  </section>

  <section class="section-block">
    <div class="section-title"><div><h2>Minhas multas</h2><p>GET /api/me/fines</p></div></div>
    <StatePanel :loading="fines.isLoading.value" :error="fines.error.value ? getApiError(fines.error.value) : null" :empty="!fines.data.value?.length">
      <div class="list"><article v-for="fine in fines.data.value?.slice(0, 5)" :key="fine.id" class="list-row"><div class="grow"><strong>Multa #{{ fine.id }}</strong><span>Permanência #{{ fine.stay_id }} · {{ money(fine.amount) }}</span></div><StatusBadge :status="fine.status" /></article></div>
    </StatePanel>
  </section>
</template>
