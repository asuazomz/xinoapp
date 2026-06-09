<script setup>
import { useForm, router } from '@inertiajs/vue3'
import FinanceLayout from '@/layouts/FinanceLayout.vue'

defineProps({
  members: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  name: '',
})

const guardarParticipante = () => {
  form.post('/finanzas/participantes', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('name')
    },
  })
}

const eliminarParticipante = (member) => {
  if (confirm(`¿Eliminar al participante "${member.name}"?`)) {
    router.delete(`/finanzas/participantes/${member.id}`, {
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
          Participantes del hogar
        </h1>

        <p class="text-gray-600 mt-2">
          Registra las personas que participan en los gastos del hogar.
        </p>
      </div>

      <form
        @submit.prevent="guardarParticipante"
        class="bg-white rounded-xl shadow p-6 space-y-4"
      >
        <h2 class="text-xl font-bold text-gray-800">
          Nuevo participante
        </h2>

        <div>
          <label class="block text-sm font-medium text-gray-700">
            Nombre
          </label>

          <input
            v-model="form.name"
            class="w-full border rounded p-2"
            placeholder="Ej: Ricardo"
          />

          <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">
            {{ form.errors.name }}
          </p>
        </div>

        <button
          class="bg-black text-white px-4 py-2 rounded"
          :disabled="form.processing"
        >
          Guardar participante
        </button>
      </form>

      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
          Participantes registrados
        </h2>

        <div v-if="members.length === 0" class="text-gray-500">
          Aún no existen participantes.
        </div>

        <div
          v-for="member in members"
          :key="member.id"
          class="flex justify-between items-center border-b py-3"
        >
          <div>
            <p class="font-medium text-gray-800">
              {{ member.name }}
            </p>

            <p class="text-sm text-green-700">
              Activo
            </p>
          </div>

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
  </FinanceLayout>
</template>