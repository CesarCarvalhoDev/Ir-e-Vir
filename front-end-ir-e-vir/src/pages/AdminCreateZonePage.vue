<script setup lang="ts">
import { reactive } from 'vue'
import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { useRouter } from 'vue-router'
import { ArrowLeft, MapPin, Save } from 'lucide-vue-next'
import { apiService } from '@/services/apiService'
import { getApiError } from '@/api/http'
import { useUiStore } from '@/stores/ui'

const router = useRouter()
const queryClient = useQueryClient()
const ui = useUiStore()
const form = reactive({ name: '', maximum_time: 60 })

const createMutation = useMutation({
  mutationFn: apiService.createZone,
  onSuccess: async (zone) => {
    await queryClient.invalidateQueries({ queryKey: ['zones'] })
    ui.notify('success', `Zona ${zone.name} adicionada com sucesso.`)
    router.push('/admin/zonas')
  },
  onError: (error) => ui.notify('error', getApiError(error)),
})

function submit() {
  createMutation.mutate({
    name: form.name.trim(),
    maximum_time: Number(form.maximum_time),
  })
}
</script>

<template>
  <section class="page-head page-head-row">
    <div><p class="eyebrow">Administração</p><h1>Adicionar zona</h1><p>Defina o nome da zona e o tempo máximo permitido.</p></div>
    <button class="secondary-command" type="button" @click="router.push('/admin/zonas')"><ArrowLeft />Voltar</button>
  </section>

  <section class="form-page">
    <form class="panel zone-form" @submit.prevent="submit">
      <div class="panel-title"><MapPin /><h2>Dados da zona</h2></div>
      <label>Nome da zona<input v-model.trim="form.name" required maxlength="255" placeholder="Ex.: Centro - Setor A" /></label>
      <label>Tempo máximo em minutos<input v-model.number="form.maximum_time" type="number" min="1" step="1" required /></label>
      <div class="form-actions">
        <button class="secondary-command" type="button" @click="router.push('/admin/zonas')"><ArrowLeft />Cancelar</button>
        <button class="primary command-button" type="submit" :disabled="createMutation.isPending.value"><Save />{{ createMutation.isPending.value ? 'Salvando...' : 'Salvar zona' }}</button>
      </div>
    </form>
  </section>
</template>
