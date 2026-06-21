<script setup>
import { computed, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import FinanceLayout from '@/layouts/FinanceLayout.vue'

const props = defineProps({
  month: String,
  incomes: {
    type: Array,
    default: () => [],
  },
  members: {
    type: Array,
    default: () => [],
  },
  incomeCategories: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  household_member_id: '',
  income_category_id: '',
  amount: '',
  month: props.month,
  description: '',
})

const editingIncomeId = ref(null)

const editIncomeForm = useForm({
  household_member_id: '',
  income_category_id: '',
  amount: '',
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

const guardarIngreso = () => {
  form.post('/finanzas/ingresos', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('household_member_id', 'income_category_id', 'amount', 'description')
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

  router.get('/finanzas/ingresos', {
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

const editarIngreso = (income) => {
  editingIncomeId.value = income.id
  editIncomeForm.household_member_id = income.household_member_id
  editIncomeForm.income_category_id = income.income_category_id
  editIncomeForm.amount = income.amount
  editIncomeForm.month = income.month
  editIncomeForm.description = income.description || ''
}

const cancelarEdicionIngreso = () => {
  editingIncomeId.value = null
  editIncomeForm.reset()
}

const actualizarIngreso = (income) => {
  editIncomeForm.put(`/finanzas/ingresos/${income.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingIncomeId.value = null
      editIncomeForm.reset()
    },
  })
}

const eliminarIngreso = (income) => {
  if (confirm('¿Eliminar este ingreso?')) {
    router.delete(`/finanzas/ingresos/${income.id}`, {
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
            <h1 class="text-2xl font-bold text-gray-800">
              Ingresos del hogar
            </h1>
            <p class="text-gray-600 mt-2">
              Registra los ingresos mensuales asociados a cada participante.
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

      <form @submit.prevent="guardarIngreso" class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-bold">Registrar ingreso</h2>

        <div>
          <label class="block text-sm font-medium">Participante</label>
          <select v-model="form.household_member_id" class="w-full border rounded p-2">
            <option value="">Selecciona un participante</option>
            <option
              v-for="member in members"
              :key="member.id"
              :value="member.id"
            >
              {{ member.name }}
            </option>
          </select>
          <p v-if="form.errors.household_member_id" class="text-red-600 text-sm">
            {{ form.errors.household_member_id }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium">Categoría de ingreso</label>
          <select v-model="form.income_category_id" class="w-full border rounded p-2">
            <option value="">Selecciona una categoría</option>
            <option
              v-for="category in incomeCategories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </select>
          <p v-if="form.errors.income_category_id" class="text-red-600 text-sm">
            {{ form.errors.income_category_id }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium">Monto</label>
          <input
            v-model="form.amount"
            type="number"
            class="w-full border rounded p-2"
            placeholder="Ej: 1000000"
          />
          <p v-if="form.errors.amount" class="text-red-600 text-sm">
            {{ form.errors.amount }}
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
            placeholder="Ej: sueldo líquido, bono, venta, ingreso extra"
          ></textarea>
        </div>

        <button
          class="bg-black text-white px-4 py-2 rounded"
          :disabled="form.processing"
        >
          Guardar ingreso
        </button>
      </form>

      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-4">Ingresos registrados</h2>

        <div v-if="incomes.length === 0" class="text-gray-500">
          No hay ingresos registrados para este mes.
        </div>

        <div
          v-for="income in incomes"
          :key="income.id"
          class="border-b py-4"
        >
          <div v-if="editingIncomeId === income.id" class="space-y-3">
            <select
              v-model="editIncomeForm.household_member_id"
              class="w-full border rounded p-2"
            >
              <option value="">Selecciona un participante</option>
              <option
                v-for="member in members"
                :key="member.id"
                :value="member.id"
              >
                {{ member.name }}
              </option>
            </select>

            <select
              v-model="editIncomeForm.income_category_id"
              class="w-full border rounded p-2"
            >
              <option value="">Selecciona una categoría</option>
              <option
                v-for="category in incomeCategories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>

            <input
              v-model="editIncomeForm.amount"
              type="number"
              class="w-full border rounded p-2"
            />

            <input
              v-model="editIncomeForm.month"
              type="month"
              class="w-full border rounded p-2"
            />

            <textarea
              v-model="editIncomeForm.description"
              class="w-full border rounded p-2"
            ></textarea>

            <div class="flex gap-2">
              <button
                type="button"
                @click="actualizarIngreso(income)"
                class="bg-black text-white px-3 py-1 rounded"
              >
                Guardar
              </button>

              <button
                type="button"
                @click="cancelarEdicionIngreso"
                class="border px-3 py-1 rounded"
              >
                Cancelar
              </button>
            </div>
          </div>

          <div v-else class="flex justify-between items-start gap-4">
            <div>
              <p class="font-bold">
                {{ income.member?.name || income.person_name }}
              </p>

              <p class="text-sm text-gray-500">
                {{ income.category?.name || 'Sin categoría' }}
              </p>

              <p v-if="income.description" class="text-sm text-gray-500">
                {{ income.description }}
              </p>
            </div>

            <div class="text-right">
              <p class="font-bold text-green-700">
                {{ formatoDinero(income.amount) }}
              </p>

              <div class="flex gap-2 mt-2">
                <button
                  type="button"
                  @click="editarIngreso(income)"
                  class="text-sm border px-2 py-1 rounded"
                >
                  Editar
                </button>

                <button
                  type="button"
                  @click="eliminarIngreso(income)"
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