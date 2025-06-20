<script setup lang="ts">
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

const props = defineProps<{
  chartData: {
    labels: string[];
    datasets: {
      label: string;
      data: number[];
      borderColor?: string;
      backgroundColor?: string;
    }[];
  };
  chartTitle: string;
  chartId: string;
}>();

const chartData = computed(() => ({
  labels: props.chartData.labels,
  datasets: props.chartData.datasets.map((dataset, index) => ({
    ...dataset,
    borderColor: dataset.borderColor || getColor(index, false),
    backgroundColor: dataset.backgroundColor || getColor(index, true),
    tension: 0.1,
    fill: false,
  })),
}));

function getColor(index: number, isBackground: boolean): string {
  const colors = [
    'rgb(54, 162, 235)',
    'rgb(255, 99, 132)',
    'rgb(75, 192, 192)',
    'rgb(255, 206, 86)',
    'rgb(153, 102, 255)',
    'rgb(255, 159, 64)',
  ];

  const color = colors[index % colors.length];

  return isBackground ? color.replace('rgb', 'rgba').replace(')', ', 0.2)') : color;
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'top',
    },
    title: {
      display: true,
      text: props.chartTitle,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
    },
  },
};
</script>

<template>
  <div class="h-full">
    <Line :id="chartId" :data="chartData" :options="chartOptions" />
  </div>
</template>
