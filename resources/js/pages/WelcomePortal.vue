<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()

const user = computed(() => page.props.auth.user)

const irDashboard = () => {
  if (user.value.role === 'admin') {
    router.visit('/admin/dashboard')
  } else {
    router.visit('/user/dashboard')
  }
}

const logout = () => {
  router.post('/logout')
}
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-md max-w-xl text-center">
      <h1 class="text-3xl font-bold text-gray-800">
        Bienvenido a XinoApp
      </h1>

      <p class="mt-4 text-gray-600">
        Portal de aprendizaje con Laravel, Vue, Inertia y Breeze.
      </p>

      <div v-if="user" class="mt-6">
        <p class="mb-4 text-gray-700">
          Sesión iniciada como <strong>{{ user.name }}</strong>
        </p>

        <button
          @click="irDashboard"
          class="bg-black text-white px-4 py-2 rounded mr-2"
        >
          Ir a mi dashboard
        </button>

        <button
          @click="logout"
          class="border border-black px-4 py-2 rounded"
        >
          Cerrar sesión
        </button>
      </div>

      <div v-else class="mt-6 flex justify-center gap-4">
        <a href="/login" class="bg-black text-white px-4 py-2 rounded">
          Iniciar sesión
        </a>

        <a href="/register" class="border border-black px-4 py-2 rounded">
          Registrarse
        </a>
      </div>
    </div>
  </div>
</template>