<script setup lang="ts">
defineProps<{ loading?: boolean; error?: string | null; empty?: boolean; emptyText?: string }>()
</script>
<template>
  <div v-if="loading" class="state-panel"><span class="spinner" />Carregando dados...</div>
  <div v-else-if="error" class="state-panel error">{{ error }}</div>
  <div v-else-if="empty" class="state-panel">{{ emptyText ?? 'Nenhum registro encontrado.' }}</div>
  <slot v-else />
</template>
