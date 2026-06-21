<script setup>
import { computed } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'

const page = usePage()

const user = computed(() => page.props.auth.user)

const logout = () => {
  router.post('/logout')
}

const irDashboard = () => {
  if (user.value?.role === 'admin') {
    router.visit('/admin/dashboard')
  } else {
    router.visit('/user/dashboard')
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
        <div>
          <button @click="irDashboard" class="text-xl font-bold text-gray-800">
            XinoApp
          </button>
          <p class="text-sm text-gray-500">Portal de aprendizaje Laravel + Vue</p>
        </div>

        <nav class="flex items-center gap-4">
         
          <Link href="/finanzas" class="text-gray-700 hover:underline">
            Finanzas
          </Link>

          <button
            v-if="user"
            @click="irDashboard"
            class="text-gray-700 hover:underline"
          >
            Dashboard
          </button>

          <span v-if="user" class="text-sm text-gray-500">
            {{ user.name }} / {{ user.role }}
          </span>

          <button
            v-if="user"
            @click="logout"
            class="bg-red-600 text-white px-4 py-2 rounded"
          >
            Cerrar sesión
          </button>

          <template v-else>
            <Link href="/login" class="text-gray-700 hover:underline">
              Login
            </Link>

            <Link href="/register" class="bg-black text-white px-4 py-2 rounded">
              Registro
            </Link>
          </template>
        </nav>
      </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8">
      <slot />
    </main>
  </div>
</template>