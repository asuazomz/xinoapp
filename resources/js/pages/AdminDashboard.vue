<script setup>
import AppLayout from '@/layouts/AppLayout.vue'

defineProps({
  user: Object,
})
</script>

<template>
  <AppLayout>
    <div class="bg-white rounded-xl shadow-md p-6">
      <h1 class="text-2xl font-bold">Panel de Administrador</h1>

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
  </AppLayout>
</template>