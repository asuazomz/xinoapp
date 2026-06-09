<script setup>
import { computed } from 'vue'
import FinanceLayout from '@/layouts/FinanceLayout.vue'
import { Pie, Doughnut} from 'vue-chartjs'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
  month: String,
  incomes: {
    type: Array,
    default: () => [],
  },
  expenses: {
    type: Array,
    default: () => [],
  },
  totalIncome: {
    type: [Number, String],
    default: 0,
  },
  totalExpense: {
    type: [Number, String],
    default: 0,
  },
})

const coloresGrafico = [
  '#ef4444',
  '#3b82f6',
  '#22c55e',
  '#f59e0b',
  '#8b5cf6',
  '#06b6d4',
  '#ec4899',
  '#84cc16',
  '#f97316',
  '#64748b',
]

const coloresReparto = [
  '#2563eb',
  '#16a34a',
  '#f59e0b',
  '#dc2626',
  '#7c3aed',
]

const formatoDinero = (valor) => {
  return new Intl.NumberFormat('es-CL', {
    style: 'currency',
    currency: 'CLP',
    maximumFractionDigits: 0,
  }).format(Number(valor || 0))
}

const saldo = computed(() => {
  return Number(props.totalIncome || 0) - Number(props.totalExpense || 0)
})

const gastosPorCategoria = computed(() => {
  const resumen = {}

  props.expenses.forEach((expense) => {
    const categoria = expense.category || 'Sin categoría'
    const monto = Number(expense.amount || 0)

    resumen[categoria] = (resumen[categoria] || 0) + monto
  })

  return Object.entries(resumen)
    .map(([category, amount]) => ({
      category,
      amount,
    }))
    .sort((a, b) => b.amount - a.amount)
})

const chartData = computed(() => ({
  labels: gastosPorCategoria.value.map(item => item.category),
  datasets: [
    {
      label: 'Gastos por categoría',
      data: gastosPorCategoria.value.map(item => item.amount),
      backgroundColor: coloresGrafico,
      borderColor: '#ffffff',
      borderWidth: 2,
    },
  ],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
  },
}

const porcentajeCategoria = (amount) => {
  const total = Number(props.totalExpense || 0)

  if (total === 0) {
    return 0
  }

  return ((Number(amount || 0) / total) * 100).toFixed(1)
}

const nombreMes = computed(() => {
  if (!props.month) return ''

  const [year, month] = props.month.split('-')
  const fecha = new Date(Number(year), Number(month) - 1, 1)

  return new Intl.DateTimeFormat('es-CL', {
    month: 'long',
    year: 'numeric',
  }).format(fecha)
})

const repartoGastos = computed(() => {
  const totalIngresos = Number(props.totalIncome || 0)
  const totalGastos = Number(props.totalExpense || 0)

  if (totalIngresos === 0 || totalGastos === 0) {
    return []
  }

  return props.incomes.map((income) => {
    const ingreso = Number(income.amount || 0)
    const porcentaje = ingreso / totalIngresos
    const aporte = totalGastos * porcentaje

    return {
      name: income.person_name,
      income: ingreso,
      percentage: porcentaje * 100,
      contribution: aporte,
    }
  })
})
const repartoChartData = computed(() => ({
  labels: repartoGastos.value.map(item => item.name),
  datasets: [
    {
      label: 'Aporte sugerido',
      data: repartoGastos.value.map(item => item.contribution),
      backgroundColor: coloresReparto,
      borderColor: '#ffffff',
      borderWidth: 2,
    },
  ],
}))

const repartoChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  rotation: -90,
  circumference: 180,
  cutout: '65%',
  plugins: {
    legend: {
      display: false,
    },
  },
}
</script>

<template>
  <FinanceLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">
          Resumen mensual
        </h1>

        <p class="text-gray-600 mt-2">
          Gastos comunes del hogar para {{ nombreMes }}.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-6">
          <p class="text-sm text-gray-500">Ingresos del hogar</p>
          <p class="text-2xl font-bold mt-2 text-green-700">
            {{ formatoDinero(totalIncome) }}
          </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
          <p class="text-sm text-gray-500">Gastos del Hogar</p>
          <p class="text-2xl font-bold mt-2 text-red-700">
            {{ formatoDinero(totalExpense) }}
          </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
          <p class="text-sm text-gray-500">Saldo estimado</p>
          <p
            class="text-2xl font-bold mt-2"
            :class="saldo >= 0 ? 'text-blue-700' : 'text-red-700'"
          >
            {{ formatoDinero(saldo) }}
          </p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">
          Distribución de gastos por categoría
        </h2>

        <div v-if="gastosPorCategoria.length === 0" class="text-gray-500">
          No hay gastos registrados para este mes.
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
          <div class="h-72 w-72 mx-auto">
            <Pie :data="chartData" :options="chartOptions" />
          </div>

          <div class="space-y-3">
            <div
              v-for="(item, index) in gastosPorCategoria"
              :key="item.category"
              class="flex items-center justify-between border-b pb-3"
            >
              <div class="flex items-center gap-3">
                <span
                  class="w-4 h-4 rounded-full"
                  :style="{ backgroundColor: coloresGrafico[index % coloresGrafico.length] }"
                ></span>

                <div>
                  <p class="font-medium text-gray-800">
                    {{ item.category }}
                  </p>

                  <p class="text-sm text-gray-500">
                    {{ porcentajeCategoria(item.amount) }}% del total
                  </p>
                </div>
              </div>

              <p class="font-bold text-gray-800">
                {{ formatoDinero(item.amount) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
  <h2 class="text-xl font-bold text-gray-800 mb-2">
    División del Gasto Mensual
  </h2>

  <p class="text-gray-600 mb-6">
    Calculado según el porcentaje de ingresos de cada persona.
  </p>

  <div v-if="repartoGastos.length === 0" class="text-gray-500">
    Registra ingresos y gastos para calcular el reparto.
  </div>

  <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
    <div class="h-56 w-96 max-w-full mx-auto">
      <Doughnut :data="repartoChartData" :options="repartoChartOptions" />
    </div>

    <div class="space-y-3">
      <div
        v-for="(item, index) in repartoGastos"
        :key="item.name"
        class="flex items-center justify-between border-b pb-3"
      >
        <div class="flex items-center gap-3">
          <span
            class="w-4 h-4 rounded-full"
            :style="{ backgroundColor: coloresReparto[index % coloresReparto.length] }"
          ></span>

          <div>
            <p class="font-medium text-gray-800">
              {{ item.name }}
            </p>

            <p class="text-sm text-gray-500">
              {{ item.percentage.toFixed(1) }}% de los ingresos
            </p>
          </div>
        </div>

        <p class="font-bold text-gray-800">
          {{ formatoDinero(item.contribution) }}
        </p>
      </div>
    </div>
  </div>
</div>
    </div>
  </FinanceLayout>
</template>