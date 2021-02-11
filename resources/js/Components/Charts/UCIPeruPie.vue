<template>
  <div class="border border-gray-300 rounded px-6 py-4">
    <h3 class="font-semibold text-lg text-gray-800 leading-tight">
      Camas UCI a nivel Nacional
    </h3>
    <canvas ref="chart" class="max-w-xs mx-auto" width="100" height="100"></canvas>
  </div>
</template>

<script>
import Chart from 'chart.js'

export default {
  props: {
    uciInfo: Object,
  },
  data() {
    return {
      myChart: null,
    }
  },
  mounted() {
    this.myChart = new Chart(this.$refs['chart'], {
      type: 'doughnut',
      data: {
        labels: ['Disponibles', 'Ocupados'],
        datasets: [
          {
            label: 'Camas UCI',
            data: [
              this.uciInfo.uci_available_nationwide,
              this.uciInfo.uci_total_nationwide - this.uciInfo.uci_available_nationwide,
            ],
            backgroundColor: ['#36a2eb', '#ff6384'],
          },
        ]
      },
      options: {
        animation: { animateScale: true },
      }
    })
  }
}
</script>
