<script setup>
import { computed, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
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

const editingExpenseId = ref(null)

const editExpenseForm = useForm({
  name: '',
  amount: '',
  category: '',
  month: '',
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

const cambiarMes = (direccion) => {
  const [year, month] = props.month.split('-')
  const fecha = new Date(Number(year), Number(month) - 1, 1)

  fecha.setMonth(fecha.getMonth() + direccion)

  const nuevoYear = fecha.getFullYear()
  const nuevoMonth = String(fecha.getMonth() + 1).padStart(2, '0')

  router.get('/finanzas/gastos', {
    month: `${nuevoYear}-${nuevoMonth}`,
  }, {
    preserveScroll: true,
    preserveState: false,
  })
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

const editarGasto = (expense) => {
  editingExpenseId.value = expense.id
  editExpenseForm.name = expense.name
  editExpenseForm.amount = expense.amount
  editExpenseForm.category = expense.category
  editExpenseForm.month = expense.month
  editExpenseForm.description = expense.description || ''
}

const cancelarEdicionGasto = () => {
  editingExpenseId.value = null
  editExpenseForm.reset()
}

const actualizarGasto = (expense) => {
  editExpenseForm.put(`/finanzas/gastos/${expense.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingExpenseId.value = null
      editExpenseForm.reset()
    },
  })
}

const eliminarGasto = (expense) => {
  if (confirm('¿Eliminar este gasto?')) {
    router.delete(`/finanzas/gastos/${expense.id}`, {
      preserveScroll: true,
    })
  }
}
</script>

<template>
  <FinanceLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold">Gastos comunes del hogar</h1>
            <p class="text-gray-600 mt-2">
              Registra gastos mensuales como arriendo, luz, agua, gas, internet y comida del hogar.
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
          class="border-b py-4"
        >
          <div v-if="editingExpenseId === expense.id" class="space-y-3">
            <input
              v-model="editExpenseForm.name"
              class="w-full border rounded p-2"
            />

            <input
              v-model="editExpenseForm.amount"
              type="number"
              class="w-full border rounded p-2"
            />

            <select
              v-model="editExpenseForm.category"
              class="w-full border rounded p-2"
            >
              <option value="">Selecciona una categoría</option>

              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.name"
              >
                {{ category.name }}
              </option>
            </select>

            <input
              v-model="editExpenseForm.month"
              type="month"
              class="w-full border rounded p-2"
            />

            <textarea
              v-model="editExpenseForm.description"
              class="w-full border rounded p-2"
            ></textarea>

            <div class="flex gap-2">
              <button
                type="button"
                @click="actualizarGasto(expense)"
                class="bg-black text-white px-3 py-1 rounded"
              >
                Guardar
              </button>

              <button
                type="button"
                @click="cancelarEdicionGasto"
                class="border px-3 py-1 rounded"
              >
                Cancelar
              </button>
            </div>
          </div>

          <div v-else class="flex justify-between items-start gap-4">
            <div>
              <p class="font-bold">{{ expense.name }}</p>

              <p class="text-sm text-gray-500">
                {{ expense.category || 'Sin categoría' }}
              </p>

              <p v-if="expense.description" class="text-sm text-gray-500">
                {{ expense.description }}
              </p>
            </div>

            <div class="text-right">
              <p class="font-bold text-red-700">
                {{ formatoDinero(expense.amount) }}
              </p>

              <div class="flex gap-2 mt-2">
                <button
                  type="button"
                  @click="editarGasto(expense)"
                  class="text-sm border px-2 py-1 rounded"
                >
                  Editar
                </button>

                <button
                  type="button"
                  @click="eliminarGasto(expense)"
                  class="text-sm bg-red-600 text-white px-2 py-1 rounded"
                >
                  Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FinanceLayout>
</template>