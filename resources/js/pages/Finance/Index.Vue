<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
  month: String,
  incomes: Array,
  expenses: Array,
  totalIncome: Number,
  totalExpense: Number,
})

const incomeForm = useForm({
  person_name: '',
  amount: '',
  month: props.month,
  description: '',
})

const expenseForm = useForm({
  name: '',
  amount: '',
  category: '',
  month: props.month,
  description: '',
})

const guardarIngreso = () => {
  incomeForm.post('/finanzas/ingresos', {
    preserveScroll: true,
    onSuccess: () => {
      incomeForm.reset('person_name', 'amount', 'description')
      incomeForm.month = props.month
    },
  })
}

const guardarGasto = () => {
  expenseForm.post('/finanzas/gastos', {
    preserveScroll: true,
    onSuccess: () => {
      expenseForm.reset('name', 'amount', 'category', 'description')
      expenseForm.month = props.month
    },
  })
}

const formatoDinero = (valor) => {
  return new Intl.NumberFormat('es-CL', {
    style: 'currency',
    currency: 'CLP',
    maximumFractionDigits: 0,
  }).format(valor || 0)
}
</script>

<template>
  <AppLayout>
    <div class="space-y-8">
      <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold">Finanzas del Hogar</h1>
        <p class="text-gray-600 mt-2">
          Registro inicial de ingresos y gastos mensuales.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="font-bold text-gray-700">Ingresos</h2>
          <p class="text-2xl font-bold mt-2">{{ formatoDinero(totalIncome) }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="font-bold text-gray-700">Gastos</h2>
          <p class="text-2xl font-bold mt-2">{{ formatoDinero(totalExpense) }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="font-bold text-gray-700">Diferencia</h2>
          <p class="text-2xl font-bold mt-2">
            {{ formatoDinero(totalIncome - totalExpense) }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <form @submit.prevent="guardarIngreso" class="bg-white rounded-xl shadow p-6 space-y-4">
          <h2 class="text-xl font-bold">Registrar ingreso</h2>

          <div>
            <label class="block text-sm font-medium">Persona</label>
            <input v-model="incomeForm.person_name" class="w-full border rounded p-2" placeholder="Ej: Ricardo" />
            <p v-if="incomeForm.errors.person_name" class="text-red-600 text-sm">
              {{ incomeForm.errors.person_name }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium">Monto</label>
            <input v-model="incomeForm.amount" type="number" class="w-full border rounded p-2" placeholder="Ej: 1000000" />
            <p v-if="incomeForm.errors.amount" class="text-red-600 text-sm">
              {{ incomeForm.errors.amount }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium">Mes</label>
            <input v-model="incomeForm.month" type="month" class="w-full border rounded p-2" />
            <p v-if="incomeForm.errors.month" class="text-red-600 text-sm">
              {{ incomeForm.errors.month }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium">Descripción</label>
            <textarea v-model="incomeForm.description" class="w-full border rounded p-2"></textarea>
          </div>

          <button class="bg-black text-white px-4 py-2 rounded" :disabled="incomeForm.processing">
            Guardar ingreso
          </button>
        </form>

        <form @submit.prevent="guardarGasto" class="bg-white rounded-xl shadow p-6 space-y-4">
          <h2 class="text-xl font-bold">Registrar gasto</h2>

          <div>
            <label class="block text-sm font-medium">Nombre del gasto</label>
            <input v-model="expenseForm.name" class="w-full border rounded p-2" placeholder="Ej: Arriendo" />
            <p v-if="expenseForm.errors.name" class="text-red-600 text-sm">
              {{ expenseForm.errors.name }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium">Monto</label>
            <input v-model="expenseForm.amount" type="number" class="w-full border rounded p-2" placeholder="Ej: 450000" />
            <p v-if="expenseForm.errors.amount" class="text-red-600 text-sm">
              {{ expenseForm.errors.amount }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium">Categoría</label>
            <input v-model="expenseForm.category" class="w-full border rounded p-2" placeholder="Ej: Hogar" />
          </div>

          <div>
            <label class="block text-sm font-medium">Mes</label>
            <input v-model="expenseForm.month" type="month" class="w-full border rounded p-2" />
            <p v-if="expenseForm.errors.month" class="text-red-600 text-sm">
              {{ expenseForm.errors.month }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium">Descripción</label>
            <textarea v-model="expenseForm.description" class="w-full border rounded p-2"></textarea>
          </div>

          <button class="bg-black text-white px-4 py-2 rounded" :disabled="expenseForm.processing">
            Guardar gasto
          </button>
        </form>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-bold mb-4">Ingresos registrados</h2>

          <div v-if="incomes.length === 0" class="text-gray-500">
            No hay ingresos registrados.
          </div>

          <div v-for="income in incomes" :key="income.id" class="border-b py-3">
            <p class="font-bold">{{ income.person_name }}</p>
            <p>{{ formatoDinero(income.amount) }}</p>
            <p class="text-sm text-gray-500">{{ income.description }}</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-bold mb-4">Gastos registrados</h2>

          <div v-if="expenses.length === 0" class="text-gray-500">
            No hay gastos registrados.
          </div>

          <div v-for="expense in expenses" :key="expense.id" class="border-b py-3">
            <p class="font-bold">{{ expense.name }}</p>
            <p>{{ formatoDinero(expense.amount) }}</p>
            <p class="text-sm text-gray-500">
              {{ expense.category }} - {{ expense.description }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>