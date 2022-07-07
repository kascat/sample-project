<template>
  <card-expand-a
    icon="receipt"
    title="Sales by department"
    headerColor="purple-6"
    textColor="purple-1"
  >
    <template v-slot:content>
      <chart-js ref="chartJs"/>
    </template>
  </card-expand-a>
</template>

<script>
import { defineComponent } from 'vue';
import CardExpandA from '../cards/CardExpandA.vue';
import ChartJs from './ChartJs.vue';

import { labels, byCategory } from 'src/_data/chartjs'

export default defineComponent({
  components: { ChartJs, CardExpandA },
  name: 'ChartJsPolarArea',
  data() {
    return {
      expanded: true,
      data: {
        labels,
        datasets: [byCategory]
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    }
  },
  mounted() {
    this.$refs.chartJs.renderChart('polarArea', this.data, this.options)
  }
})
</script>
