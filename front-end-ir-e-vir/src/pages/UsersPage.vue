<script setup lang="ts">
import { ref } from 'vue'
import { useMutation, useQuery } from '@tanstack/vue-query'
import { Search, Users } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { dateTime, money } from '@/utils/format'
import { useUiStore } from '@/stores/ui'
import type { Stay } from '@/types/api'
import StatePanel from '@/components/StatePanel.vue'
import StatusBadge from '@/components/StatusBadge.vue'
const ui = useUiStore()
const userId = ref(0); const userStays = ref<Stay[]>([])
const users = useQuery({ queryKey: ['admin', 'users'], queryFn: apiService.getUsers })
const searchMutation = useMutation({ mutationFn: apiService.getUserStays, onSuccess: v => { userStays.value = v }, onError: e => ui.notify('error', getApiError(e)) })
function selectUser(id: number) {
  userId.value = id
}
</script>
<template>
  <section class="page-head"><div><p class="eyebrow">Administração</p><h1>Usuários</h1><p>Consulte os usuários e seus históricos de permanência.</p></div></section>
  <section class="section-block">
    <div class="section-title"><div><h2>Usuários cadastrados</h2><p>GET /api/admin/users</p></div></div>
    <StatePanel :loading="users.isLoading.value" :error="users.error.value ? getApiError(users.error.value) : null" :empty="!users.data.value?.length">
      <div class="table-wrap"><table><thead><tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Saldo</th><th></th></tr></thead><tbody><tr v-for="user in users.data.value" :key="user.id"><td>#{{ user.id }}</td><td>{{ user.name }}</td><td>{{ user.email }}</td><td>{{ user.role }}</td><td>{{ money(user.wallet?.balance ?? user.available_balance) }}</td><td><button class="primary compact-button" @click="selectUser(user.id)"><Users />Usar ID</button></td></tr></tbody></table></div>
    </StatePanel>
  </section>
  <section>
    <form class="panel" @submit.prevent="searchMutation.mutate(userId)"><div class="panel-title"><Search /><h2>Permanências do usuário</h2></div><label>ID do usuário<input v-model.number="userId" type="number" min="1" required /></label><button class="primary">Consultar</button></form>
  </section>
  <section class="section-block"><div class="section-title"><div><h2>Resultado da consulta</h2><p>GET /api/admin/user/{{ userId || '{user}' }}/stays</p></div></div><div v-if="!userStays.length" class="state-panel">Faça uma consulta para visualizar as permanências.</div><div v-else class="list"><article v-for="stay in userStays" :key="stay.id" class="list-row"><div class="grow"><strong>Permanência #{{ stay.id }}</strong><span>{{ dateTime(stay.entry) }} · veículo #{{ stay.vehicle_id }}</span></div><StatusBadge :status="stay.status" /></article></div></section>
</template>
