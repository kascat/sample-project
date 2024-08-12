<template>
  <div>
    <Bar v-if="'bar' === type" ref="chart" :data="data" :options="chartOptions"/>
    <Pie v-if="'pie' === type" ref="chart" :data="data" :options="chartOptions"/>
    <Line v-if="'line' === type" ref="chart" :data="data" :options="chartOptions"/>
    <Bubble v-if="'bubble' === type" ref="chart" :data="data" :options="chartOptions"/>
    <Radar v-if="'radar' === type" ref="chart" :data="data" :options="chartOptions"/>
    <Doughnut v-if="'doughnut' === type" ref="chart" :data="data" :options="chartOptions"/>
    <Scatter v-if="'scatter' === type" ref="chart" :data="data" :options="chartOptions"/>
    <PolarArea v-if="'polar-area' === type" ref="chart" :data="data" :options="chartOptions"/>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import {
  Bar,
  Pie,
  Line,
  Bubble,
  Radar,
  Doughnut,
  Scatter,
  PolarArea,
} from 'vue-chartjs';
import {
  Chart as ChartJS,
  registerables,
} from 'chart.js';

ChartJS.register(...registerables);

const chart = ref(null);

const props = defineProps({
  type: {
    type: String,
    default: 'bar',
  },
  data: {
    default: {
      labels: [ 'Toys', 'Movies', 'Computers', 'Games' ],
      datasets: [ {
        label: '#Departments',
        data: [ 24, 15, 17, 20 ],
        backgroundColor: [ '#E17D27B7', '#C53648BF', '#75C9B7BF', '#5AFF6A66' ],
        borderColor: [ '#f58200', '#720512', '#1f886d', '#3f9f4b' ],
        borderWidth: 1,
      } ],
    },
  },
  options: {
    default: {
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        duration: 3500,
      },
    },
  },
  predefined_animation: {
    type: Number,
    default: null,
  },
});

const previousY = (ctx) => ctx.index === 0 ? ctx.chart.scales.y.getPixelForValue(100) : ctx.chart.getDatasetMeta(ctx.datasetIndex).data[ctx.index - 1].getProps([ 'y' ], true).y;

const predefinedAnimations = {
  1: {
    duration: 3500,
  },
  2: {
    x: {
      type: 'number',
      easing: 'linear',
      duration: 100,
      from: NaN, // the point is initially skipped
      delay(ctx) {
        if (ctx.type !== 'data' || ctx.xStarted) {
          return 0;
        }
        ctx.xStarted = true;
        return ctx.index * 100;
      },
    },
    y: {
      type: 'number',
      easing: 'linear',
      duration: 100,
      from: previousY,
      delay(ctx) {
        if (ctx.type !== 'data' || ctx.yStarted) {
          return 0;
        }
        ctx.yStarted = true;
        return ctx.index * 100;
      },
    },
  },
};

const chartOptions = computed(() => {
  const options = { ...props.options };

  if (props.predefined_animation) {
    options.animation = predefinedAnimations[props.predefined_animation];
  }

  return options;
});

watch(() => props.data?.datasets, () => {
  ChartJS.getChart(chart.value.chart).data = props.data;
  ChartJS.getChart(chart.value.chart).update();
});

</script>
