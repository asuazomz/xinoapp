<script setup>
import { computed } from 'vue'
import FinanceLayout from '@/layouts/FinanceLayout.vue'
import { Pie } from 'vue-chartjs'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const chartData = computed(() => ({
  labels: gastosPorCategoria.value.map(item => item.category),
  datasets: [
{
    label: 'Gastos por categoría',
    data: gastosPorCategoria.value.map(item => item.amount),

    backgroundColor: [
        '#ef4444', // rojo
        '#3b82f6', // azul
        '#22c55e', // verde
        '#f59e0b', // amarillo
        '#8b5cf6', // violeta
        '#06b6d4', // cyan
        '#ec4899', // rosado
        '#84cc16', // lima
        '#f97316', // naranja
        '#64748b', // gris
    ],

    borderWidth: 2,
},
],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
}

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

    if (!resumen[categoria]) {
      resumen[categoria] = 0
    }

    resumen[categoria] += monto
  })

  return Object.entries(resumen).map(([category, amount]) => ({
    category,
    amount,
  }))
})

const porcentajeCategoria = (amount) => {
  const total = Number(props.totalExpense || 0)

  if (total === 0) {
    return 0
  }

  return Math.round((Number(amount || 0) / total) * 100)
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
          <p class="text-sm text-gray-500">Gastos comunes</p>
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

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
  <h2 class="text-xl font-bold text-gray-800 mb-4">
    Distribución de gastos por categoría
  </h2>

  <div class="bg-white rounded-xl shadow p-6">
  <h2 class="text-xl font-bold text-gray-800 mb-4">
    Distribución de gastos
  </h2>

  <div v-if="gastosPorCategoria.length === 0" class="text-gray-500">
    No hay gastos registrados para este mes.
  </div>

  <div class="h-72 w-72 mx-auto">
    <Pie :data="chartData" :options="chartOptions" />
  </div>
  </div>

  <div v-if="gastosPorCategoria.length === 0" class="text-gray-500">
    No hay gastos registrados para este mes.
  </div>


  <div v-else class="space-y-4">
    <div
      v-for="item in gastosPorCategoria"
      :key="item.category"
    >
      <div class="flex justify-between mb-1">
        <span class="font-medium text-gray-700">
          {{ item.category }}
        </span>

        <span class="font-bold text-gray-800">
          {{ formatoDinero(item.amount) }}
        </span>
      </div>

      <div class="w-full bg-gray-200 rounded-full h-4">
        <div
          class="bg-black h-4 rounded-full"
          :style="{ width: porcentajeCategoria(item.amount) + '%' }"
        ></div>
      </div>

      <p class="text-sm text-gray-500 mt-1">
        {{ porcentajeCategoria(item.amount) }}% del total de gastos
      </p>
    </div>
  </div>
</div>

        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">
            Gastos registrados
          </h2>

          <div v-if="expenses.length === 0" class="text-gray-500">
            No hay gastos registrados para este mes.
          </div>

          <div
            v-for="expense in expenses"
            :key="expense.id"
            class="border-b py-4"
          >
            <div class="flex justify-between gap-4">
              <div>
                <p class="font-bold text-gray-800">
                  {{ expense.name }}
                </p>

                <p class="text-sm text-gray-500">
                  {{ expense.category || 'Sin categoría' }}
                </p>

                <p v-if="expense.description" class="text-sm text-gray-500 mt-1">
                  {{ expense.description }}
                </p>
              </div>

              <div class="font-bold text-gray-800">
                {{ formatoDinero(expense.amount) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
          Ingresos registrados
        </h2>

        <div v-if="incomes.length === 0" class="text-gray-500">
          No hay ingresos registrados para este mes.
        </div>

        <div
          v-for="income in incomes"
          :key="income.id"
          class="flex justify-between border-b py-3"
        >
          <div>
            <p class="font-bold">{{ income.person_name }}</p>
            <p v-if="income.description" class="text-sm text-gray-500">
              {{ income.description }}
            </p>
          </div>

          <p class="font-bold text-green-700">
            {{ formatoDinero(income.amount) }}
          </p>
        </div>
      </div>
    </div>
  </FinanceLayout>
</template>