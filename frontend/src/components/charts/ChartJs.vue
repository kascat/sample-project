<template>
  <div>
    <canvas
      class="chartjs-canvas"
      :id="randomId"
      :key="randomId"
    />
  </div>
</template>

<script>
import { defineComponent, getCurrentInstance, ref } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

export default defineComponent({
  name: 'ChartJs',
  setup () {
    const chart = ref(null)
    const context = ref(null)
    const randomId = `chartjs_${Math.random().toString().replace('.', '1')}`

    const checkContext = () => {
      if (!context.value) {
        const canvas = document.getElementById(randomId)
        context.value = canvas.getContext('2d')
      }
    }

    const renderChart = (type, data, options) => {
      checkContext()
      if (chart.value) {
        chart.value.destroy()
      }
      chart.value = new Chart(context.value, { type, data, options })
    }

    return {
      randomId,
      renderChart
    }
  }
})
</script>
