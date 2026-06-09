<script setup>
import { useForm } from '@inertiajs/vue3'
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
})

const form = useForm({
  household_member_id: '',
  amount: '',
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

const guardarIngreso = () => {
  form.post('/finanzas/ingresos', {
    preserveScroll: true,
    onSuccess: () => {
  form.reset('household_member_id', 'amount', 'description')
  form.month = props.month
},
  })
}
</script>

<template>
  <FinanceLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold">Ingresos del hogar</h1>
        <p class="text-gray-600 mt-2">
          Registra los ingresos mensuales de las personas que participan en el hogar.
        </p>
      </div>

      <form @submit.prevent="guardarIngreso" class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-bold">Registrar ingreso</h2>

        <div>
  <label class="block text-sm font-medium">Participante</label>

  <select
    v-model="form.household_member_id"
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

  <p v-if="form.errors.household_member_id" class="text-red-600 text-sm">
    {{ form.errors.household_member_id }}
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
            placeholder="Ej: Sueldo principal, bono, ingreso extra"
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
          class="flex justify-between border-b py-3"
        >
          <div>
            <p class="font-bold">{{ income.member?.name || income.person_name  }}</p>
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