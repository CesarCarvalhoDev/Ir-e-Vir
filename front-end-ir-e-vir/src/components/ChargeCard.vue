<template>
  <div class="bg-slate-700 rounded-lg p-6 border border-slate-600 hover:border-slate-500 transition-colors">
    <!-- Plate -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-xl font-bold text-white">{{ charge.stay?.vehicle?.plate || 'Placa' }}</h3>
      <span
        :class="{
          'bg-green-500/20 text-green-300': charge.status === 'paid',
          'bg-yellow-500/20 text-yellow-300': charge.status === 'pending',
          'bg-red-500/20 text-red-300': charge.status === 'overdue',
        }"
        class="px-3 py-1 rounded-full text-sm font-semibold"
      >
        {{ formatStatus(charge.status) }}
      </span>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-2 gap-4 mb-4">
      <!-- Value -->
      <div>
        <p class="text-slate-400 text-sm mb-1">Valor</p>
        <p class="text-2xl font-bold text-white">R$ {{ formatValue(charge.value) }}</p>
      </div>

      <!-- Due Date -->
      <div>
        <p class="text-slate-400 text-sm mb-1">Data de Vencimento</p>
        <p class="text-white font-semibold">{{ formatDate(charge.due_date) }}</p>
      </div>

      <!-- Entry -->
      <div>
        <p class="text-slate-400 text-sm mb-1">Entrada</p>
        <p class="text-white text-sm">{{ formatDateTime(charge.stay?.entry) }}</p>
      </div>

      <!-- Exit -->
      <div>
        <p class="text-slate-400 text-sm mb-1">Saída</p>
        <p class="text-white text-sm">{{ formatDateTime(charge.stay?.exit) }}</p>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-2 pt-4 border-t border-slate-600">
      <button
        @click="downloadReceipt"
        class="flex-1 py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-colors"
      >
        📥 Comprovante
      </button>
      <button
        v-if="charge.status === 'pending'"
        @click="payCharge"
        class="flex-1 py-2 px-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-colors"
      >
        💳 Pagar
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ChargeCard',
  props: {
    charge: {
      type: Object,
      required: true,
    },
  },
  methods: {
    formatStatus(status) {
      const statusMap = {
        paid: 'Pago',
        pending: 'Pendente',
        overdue: 'Vencido',
      };
      return statusMap[status] || status;
    },

    formatValue(value) {
      return parseFloat(value).toFixed(2).replace('.', ',');
    },

    formatDate(date) {
      if (!date) return '-';
      return new Date(date).toLocaleDateString('pt-BR');
    },

    formatDateTime(dateTime) {
      if (!dateTime) return '-';
      const date = new Date(dateTime);
      return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    },

    downloadReceipt() {
      alert('Funcionalidade de download de comprovante em breve!');
    },

    payCharge() {
      alert('Funcionalidade de pagamento em breve!');
    },
  },
};
</script>
