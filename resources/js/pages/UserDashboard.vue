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
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="bg-white rounded-xl shadow-md p-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Dashboard de Usuario</h1>

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
          <h2 class="font-bold">Mi perfil</h2>
          <p class="text-gray-600 mt-2">Revisa tus datos personales.</p>
        </div>

        <div class="border rounded-lg p-4">
          <h2 class="font-bold">Mis actividades</h2>
          <p class="text-gray-600 mt-2">Aquí podrás ver tus acciones.</p>
        </div>

        <div class="border rounded-lg p-4">
          <h2 class="font-bold">Configuración</h2>
          <p class="text-gray-600 mt-2">Ajustes de tu cuenta.</p>
        </div>
      </div>
    </div>
  </div>
</template>