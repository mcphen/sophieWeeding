<script setup lang="ts">
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps<{
  chartData: {
    labels: string[];
    data: number[];
  };
  chartTitle: string;
  chartId: string;
}>();

const chartData = computed(() => ({
  labels: props.chartData.labels,
  datasets: [
    {
      label: props.chartTitle,
      backgroundColor: [
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 99, 132, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(255, 206, 86, 0.6)',
        'rgba(153, 102, 255, 0.6)',
        'rgba(255, 159, 64, 0.6)',
        'rgba(199, 199, 199, 0.6)',
        'rgba(83, 102, 255, 0.6)',
        'rgba(40, 159, 64, 0.6)',
        'rgba(210, 199, 199, 0.6)',
      ],
      data: props.chartData.data,
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'right',
    },
    title: {
      display: true,
      text: props.chartTitle,
    },
  },
};
</script>

<template>
  <div class="h-full">
    <Pie :id="chartId" :data="chartData" :options="chartOptions" />
  </div>
</template>
