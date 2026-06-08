<script setup>
import { useForm } from '@inertiajs/vue3'
import FinanceLayout from '@/layouts/FinanceLayout.vue'

const props = defineProps({
  month: String,
  expenses: {
    type: Array,
    default: () => [],
  },
  categories: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  name: '',
  amount: '',
  category: '',
  month: props.month,
  description: '',
})

const formatoDinero = (valor) => {
  return new Intl.NumberFormat('es-CL', {
    style: 'currency',
    currency: 'CLP',
    maximumFractionDigits: 0,
  }).format(Number(valor || 0))
}

const guardarGasto = () => {
  form.post('/finanzas/gastos', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('name', 'amount', 'category', 'description')
      form.month = props.month
    },
  })
}
</script>

<template>
  <FinanceLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold">Gastos comunes del hogar</h1>
        <p class="text-gray-600 mt-2">
          Registra gastos mensuales como arriendo, luz, agua, gas, internet y comida del hogar.
        </p>
      </div>

      <form @submit.prevent="guardarGasto" class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-bold">Registrar gasto</h2>

        <div>
          <label class="block text-sm font-medium">Nombre del gasto</label>
          <input
            v-model="form.name"
            class="w-full border rounded p-2"
            placeholder="Ej: Cuenta de luz"
          />
          <p v-if="form.errors.name" class="text-red-600 text-sm">
            {{ form.errors.name }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium">Monto</label>
          <input
            v-model="form.amount"
            type="number"
            class="w-full border rounded p-2"
            placeholder="Ej: 45000"
          />
          <p v-if="form.errors.amount" class="text-red-600 text-sm">
            {{ form.errors.amount }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium">Categoría</label>
          <select v-model="form.category" class="w-full border rounded p-2">
            <option value="">Selecciona una categoría</option>
            <option
  v-for="category in categories"
  :key="category.id"
  :value="category.name"
>
  {{ category.name }}
</option>
          </select>
          <p v-if="form.errors.category" class="text-red-600 text-sm">
            {{ form.errors.category }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium">Mes</label>
          <input
            v-model="form.month"
            type="month"
            class="w-full border rounded p-2"
          />
          <p v-if="form.errors.month" class="text-red-600 text-sm">
            {{ form.errors.month }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium">Descripción</label>
          <textarea
            v-model="form.description"
            class="w-full border rounded p-2"
            placeholder="Ej: Boleta de Enel, consumo mensual, etc."
          ></textarea>
        </div>

        <button
          class="bg-black text-white px-4 py-2 rounded"
          :disabled="form.processing"
        >
          Guardar gasto
        </button>
      </form>

      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-4">Gastos registrados</h2>

        <div v-if="expenses.length === 0" class="text-gray-500">
          No hay gastos registrados para este mes.
        </div>

        <div
          v-for="expense in expenses"
          :key="expense.id"
          class="flex justify-between border-b py-3"
        >
          <div>
            <p class="font-bold">{{ expense.name }}</p>
            <p class="text-sm text-gray-500">
              {{ expense.category || 'Sin categoría' }}
            </p>
            <p v-if="expense.description" class="text-sm text-gray-500">
              {{ expense.description }}
            </p>
          </div>

          <p class="font-bold text-red-700">
            {{ formatoDinero(expense.amount) }}
          </p>
        </div>
      </div>
    </div>
  </FinanceLayout>
</template>