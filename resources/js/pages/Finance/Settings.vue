<script setup>
import { useForm, router } from '@inertiajs/vue3'
import FinanceLayout from '@/layouts/FinanceLayout.vue'

defineProps({
  members: {
    type: Array,
    default: () => [],
  },
  categories: {
    type: Array,
    default: () => [],
  },
  incomeCategories: {
    type: Array,
    default: () => [],
  },
})

const memberForm = useForm({
  name: '',
})

const expenseCategoryForm = useForm({
  name: '',
})

const incomeCategoryForm = useForm({
  name: '',
  allow_multiple_per_month: false,
})

const guardarParticipante = () => {
  memberForm.post('/finanzas/participantes', {
    preserveScroll: true,
    onSuccess: () => memberForm.reset('name'),
  })
}

const eliminarParticipante = (member) => {
  if (confirm(`¿Eliminar participante "${member.name}"?`)) {
    router.delete(`/finanzas/participantes/${member.id}`, {
      preserveScroll: true,
    })
  }
}

const guardarCategoriaGasto = () => {
  expenseCategoryForm.post('/finanzas/configuracion/categorias', {
    preserveScroll: true,
    onSuccess: () => expenseCategoryForm.reset('name'),
  })
}

const eliminarCategoriaGasto = (category) => {
  if (confirm(`¿Eliminar categoría de gasto "${category.name}"?`)) {
    router.delete(`/finanzas/configuracion/categorias/${category.id}`, {
      preserveScroll: true,
    })
  }
}

const guardarCategoriaIngreso = () => {
  incomeCategoryForm.post('/finanzas/configuracion/categorias-ingreso', {
    preserveScroll: true,
    onSuccess: () => {
      incomeCategoryForm.reset('name', 'allow_multiple_per_month')
      incomeCategoryForm.allow_multiple_per_month = false
    },
  })
}

const eliminarCategoriaIngreso = (category) => {
  if (confirm(`¿Eliminar categoría de ingreso "${category.name}"?`)) {
    router.delete(`/finanzas/configuracion/categorias-ingreso/${category.id}`, {
      preserveScroll: true,
    })
  }
}
</script>

<template>
  <FinanceLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">
          Configuración del hogar
        </h1>
        <p class="text-gray-600 mt-2">
          Administra participantes, categorías de gastos y categorías de ingresos.
        </p>
      </div>

      <!-- Participantes -->
      <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-bold text-gray-800">
          Participantes del hogar
        </h2>

        <form @submit.prevent="guardarParticipante" class="space-y-3">
          <input
            v-model="memberForm.name"
            class="w-full border rounded p-2"
            placeholder="Ej: Ricardo"
          />

          <p v-if="memberForm.errors.name" class="text-red-600 text-sm">
            {{ memberForm.errors.name }}
          </p>

          <button
            class="bg-black text-white px-4 py-2 rounded"
            :disabled="memberForm.processing"
          >
            Guardar participante
          </button>
        </form>

        <div class="pt-4">
          <div v-if="members.length === 0" class="text-gray-500">
            No hay participantes registrados.
          </div>

          <div
            v-for="member in members"
            :key="member.id"
            class="flex justify-between items-center border-b py-3"
          >
            <span class="font-medium">{{ member.name }}</span>

            <button
              type="button"
              @click="eliminarParticipante(member)"
              class="bg-red-600 text-white px-3 py-1 rounded text-sm"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>

      <!-- Categorías de gastos -->
      <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-bold text-gray-800">
          Categorías de gastos
        </h2>

        <form @submit.prevent="guardarCategoriaGasto" class="space-y-3">
          <input
            v-model="expenseCategoryForm.name"
            class="w-full border rounded p-2"
            placeholder="Ej: Arriendo, Luz, Agua"
          />

          <p v-if="expenseCategoryForm.errors.name" class="text-red-600 text-sm">
            {{ expenseCategoryForm.errors.name }}
          </p>

          <button
            class="bg-black text-white px-4 py-2 rounded"
            :disabled="expenseCategoryForm.processing"
          >
            Guardar categoría de gasto
          </button>
        </form>

        <div class="pt-4">
          <div v-if="categories.length === 0" class="text-gray-500">
            No hay categorías de gastos.
          </div>

          <div
            v-for="category in categories"
            :key="category.id"
            class="flex justify-between items-center border-b py-3"
          >
            <span class="font-medium">{{ category.name }}</span>

            <button
              type="button"
              @click="eliminarCategoriaGasto(category)"
              class="bg-red-600 text-white px-3 py-1 rounded text-sm"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>

      <!-- Categorías de ingresos -->
      <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-bold text-gray-800">
          Categorías de ingresos
        </h2>

        <form @submit.prevent="guardarCategoriaIngreso" class="space-y-3">
          <input
            v-model="incomeCategoryForm.name"
            class="w-full border rounded p-2"
            placeholder="Ej: Sueldo principal, Bono, Venta de miel"
          />

          <p v-if="incomeCategoryForm.errors.name" class="text-red-600 text-sm">
            {{ incomeCategoryForm.errors.name }}
          </p>

          <label class="flex items-center gap-2 text-sm text-gray-700">
            <input
              type="checkbox"
              v-model="incomeCategoryForm.allow_multiple_per_month"
            />
            Permitir múltiples ingresos de esta categoría por mes
          </label>

          <button
            class="bg-black text-white px-4 py-2 rounded"
            :disabled="incomeCategoryForm.processing"
          >
            Guardar categoría de ingreso
          </button>
        </form>

        <div class="pt-4">
          <div v-if="incomeCategories.length === 0" class="text-gray-500">
            No hay categorías de ingresos.
          </div>

          <div
            v-for="category in incomeCategories"
            :key="category.id"
            class="flex justify-between items-center border-b py-3"
          >
            <div>
              <p class="font-medium">{{ category.name }}</p>
              <p class="text-sm text-gray-500">
                {{ category.allow_multiple_per_month ? 'Permite múltiples por mes' : 'Solo una vez por mes' }}
              </p>
            </div>

            <button
              type="button"
              @click="eliminarCategoriaIngreso(category)"
              class="bg-red-600 text-white px-3 py-1 rounded text-sm"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
  </FinanceLayout>
</template>