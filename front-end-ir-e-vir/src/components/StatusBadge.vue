<script setup lang="ts">
import { statusLabel } from '@/utils/format'
defineProps<{ status: string }>()
</script>
<template><span :class="status.toLowerCase()" class="badge">{{ statusLabel(status) }}</span></template>
