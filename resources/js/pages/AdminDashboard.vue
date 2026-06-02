<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
  user: Object,
})

const logout = () => {
  router.post('/logout')
}
</script>

<template>
  <div class="min-h-screen bg-gray-900 p-8 text-white">
    <div class="bg-white text-gray-900 rounded-xl shadow-md p-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Panel de Administrador</h1>

        <button
          @click="logout"
          class="bg-red-600 text-white px-4 py-2 rounded"
        >
          Cerrar sesión
        </button>
      </div>

      <div class="mt-6">
        <p><strong>Nombre:</strong> {{ user.name }}</p>
        <p><strong>Email:</strong> {{ user.email }}</p>
        <p><strong>Rol:</strong> {{ user.role }}</p>
      </div>

      <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="border rounded-lg p-4">
          <h2 class="font-bold">Usuarios</h2>
          <p class="text-gray-600 mt-2">Administrar usuarios registrados.</p>
        </div>

        <div class="border rounded-lg p-4">
          <h2 class="font-bold">Reportes</h2>
          <p class="text-gray-600 mt-2">Ver información del sistema.</p>
        </div>

        <div class="border rounded-lg p-4">
          <h2 class="font-bold">Configuración</h2>
          <p class="text-gray-600 mt-2">Opciones administrativas.</p>
        </div>
      </div>
    </div>
  </div>
</template>