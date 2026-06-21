<script setup>
import { computed, ref, watch } from 'vue'
import FinanceLayout from '@/layouts/FinanceLayout.vue'
import { Pie, Doughnut, Line} from 'vue-chartjs'
import { router } from '@inertiajs/vue3'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  ArcElement,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
)

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
  yearExpenses: {
  type: Array,
  default: () => [],
  },
  year: {
    type: [String, Number],
    default: '',
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
      name: income.member?.name || income.person_name,
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

const cambiarMes = (direccion) => {
  const [year, month] = props.month.split('-')

  const fecha = new Date(Number(year), Number(month) - 1, 1)

  fecha.setMonth(fecha.getMonth() + direccion)

  const nuevoYear = fecha.getFullYear()
  const nuevoMonth = String(fecha.getMonth() + 1).padStart(2, '0')

  router.get('/finanzas/resumen', {
    month: `${nuevoYear}-${nuevoMonth}`,
  }, {
    preserveState: false,
    preserveScroll: true,
  })
}

const mesesAnio = [
  { key: '01', label: 'Ene' },
  { key: '02', label: 'Feb' },
  { key: '03', label: 'Mar' },
  { key: '04', label: 'Abr' },
  { key: '05', label: 'May' },
  { key: '06', label: 'Jun' },
  { key: '07', label: 'Jul' },
  { key: '08', label: 'Ago' },
  { key: '09', label: 'Sep' },
  { key: '10', label: 'Oct' },
  { key: '11', label: 'Nov' },
  { key: '12', label: 'Dic' },
]

const categoriasAnuales = computed(() => {
  return [...new Set(props.yearExpenses.map(expense => expense.category || 'Sin categoría'))]
})

const lineChartData = computed(() => {
  return {
    labels: mesesAnio.map(mes => mes.label),
    datasets: categoriasAnuales.value
      .filter(category => selectedCategories.value.includes(category))
      .map((category, index) => {
        return {
          label: category,
          data: mesesAnio.map((mes) => {
            const monthKey = `${props.year}-${mes.key}`

            const total = props.yearExpenses
              .filter(expense => expense.month === monthKey && (expense.category || 'Sin categoría') === category)
              .reduce((sum, expense) => sum + Number(expense.amount || 0), 0)

            return total > 0 ? total : null
          }),
          borderColor: coloresGrafico[index % coloresGrafico.length],
          backgroundColor: coloresGrafico[index % coloresGrafico.length],
          tension: 0,
          spanGaps: true,
        }
      }),
  }
})

const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: 'index',
    intersect: false,
  },
  plugins: {
    legend: {
      position: 'bottom',
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        callback: function (value) {
          return new Intl.NumberFormat('es-CL', {
            style: 'currency',
            currency: 'CLP',
            maximumFractionDigits: 0,
          }).format(value)
        },
      },
    },
  },
}

const selectedCategories = ref([])

watch(categoriasAnuales, (categories) => {
  selectedCategories.value = categories
}, { immediate: true })

const toggleCategory = (category) => {
  if (selectedCategories.value.includes(category)) {
    selectedCategories.value = selectedCategories.value.filter(item => item !== category)
  } else {
    selectedCategories.value.push(category)
  }
}

const seleccionarTodas = () => {
  selectedCategories.value = [...categoriasAnuales.value]
}

const limpiarCategorias = () => {
  selectedCategories.value = []
}

</script>

<template>
  <FinanceLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow p-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">
        Resumen mensual
      </h1>

      <p class="text-gray-600 mt-2">
        Gastos comunes del hogar para {{ nombreMes }}.
      </p>
    </div>

    <div class="flex items-center gap-3">
      <button
        type="button"
        @click="cambiarMes(-1)"
        class="px-4 py-2 rounded border bg-white hover:bg-gray-100"
      >
        ← Mes anterior
      </button>

      <div class="font-bold text-gray-800 min-w-36 text-center">
        {{ nombreMes }}
      </div>

      <button
        type="button"
        @click="cambiarMes(1)"
        class="px-4 py-2 rounded border bg-white hover:bg-gray-100"
      >
        Mes siguiente →
      </button>
    </div>
  </div>
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
    Evolución anual de gastos
  </h2>

  <p class="text-gray-600 mb-6">
    Gastos mensuales por categoría durante {{ year }}.
  </p>

  <div v-if="yearExpenses.length === 0" class="text-gray-500">
    No hay gastos registrados para este año.
  </div>

  <div v-else class="space-y-6">
    <div class="border rounded-xl p-4 bg-gray-50">
      <div class="flex justify-between items-center mb-4">
        <p class="font-semibold text-gray-700">
          Categorías a mostrar
        </p>

        <div class="flex gap-2">
          <button
            type="button"
            @click="seleccionarTodas"
            class="text-sm border px-3 py-1 rounded bg-white"
          >
            Todas
          </button>

          <button
            type="button"
            @click="limpiarCategorias"
            class="text-sm border px-3 py-1 rounded bg-white"
          >
            Limpiar
          </button>
        </div>
      </div>

      <div class="flex flex-wrap gap-3">
        <label
          v-for="category in categoriasAnuales"
          :key="category"
          class="flex items-center gap-2 border rounded px-3 py-2 bg-white cursor-pointer"
        >
          <input
            type="checkbox"
            :checked="selectedCategories.includes(category)"
            @change="toggleCategory(category)"
          />

          <span>{{ category }}</span>
        </label>
      </div>
    </div>

    <div v-if="selectedCategories.length === 0" class="text-gray-500">
      Selecciona al menos una categoría para ver el gráfico.
    </div>

    <div v-else class="h-96">
      <Line :data="lineChartData" :options="lineChartOptions" />
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