<script setup>
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'
import { computed } from 'vue'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps({
    data: {
        type: Array,
        required: true
    },
    label: {
        type: String,
        default: 'Data'
    },
    color: {
        type: String,
        default: '#4F46E5' // Indigo 600
    }
})

const chartData = computed(() => {
    return {
        labels: props.data.map(item => item.date),
        datasets: [
            {
                label: props.label,
                backgroundColor: props.color,
                borderColor: props.color,
                data: props.data.map(item => item.value),
                tension: 0.4, // Smooth curve
                fill: false
            }
        ]
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                display: true,
                borderDash: [5, 5]
            }
        },
        x: {
            grid: {
                display: false
            },
            ticks: {
                maxTicksLimit: 10 // Limit x-axis labels
            }
        }
    }
}
</script>

<template>
    <div class="h-full w-full">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>
