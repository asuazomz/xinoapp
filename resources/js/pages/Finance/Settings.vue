<script setup>
import { useForm } from '@inertiajs/vue3'
import FinanceLayout from '@/layouts/FinanceLayout.vue'

defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  name: '',
})

const guardarCategoria = () => {
  form.post('/finanzas/configuracion/categorias', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('name')
    },
  })
}

const eliminarCategoria = (category) => {
  if (confirm(`¿Eliminar la categoría "${category.name}"?`)) {
    router.delete(`/finanzas/configuracion/categorias/${category.id}`, {
      preserveScroll: true,
    })
  }
}

</script>

<template>
  <FinanceLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold">Configuración del hogar</h1>
        <p class="text-gray-600 mt-2">
          Administra configuraciones generales del módulo de finanzas.
        </p>
      </div>

      <form @submit.prevent="guardarCategoria" class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-bold">Categorías de gasto</h2>

        <div>
          <label class="block text-sm font-medium">Nueva categoría</label>
          <input
            v-model="form.name"
            class="w-full border rounded p-2"
            placeholder="Ej: Arriendo, Luz, Supermercado"
          />

          <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">
            {{ form.errors.name }}
          </p>
        </div>

        <button
          class="bg-black text-white px-4 py-2 rounded"
          :disabled="form.processing"
        >
          Guardar categoría
        </button>
      </form>

      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-4">Categorías registradas</h2>

        <div v-if="categories.length === 0" class="text-gray-500">
          Aún no existen categorías.
        </div>

        <div
            v-for="category in categories"
            :key="category.id"
            class="flex justify-between items-center border-b py-3"
        >
        <div>
            <span class="font-medium">{{ category.name }}</span>
            <span class="ml-2 text-sm text-green-700">Activa</span>
        </div>

        <button
            @click="eliminarCategoria(category)"
            class="bg-red-600 text-white px-3 py-1 rounded text-sm"
        >
            Eliminar
        </button>
        </div>
      </div>
    </div>
  </FinanceLayout>
</template>