<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-2xl shadow p-2 flex flex-wrap gap-2">
  <Link
    href="/finanzas/resumen"
    class="px-4 py-2 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition"
  >
    📊 Resumen
  </Link>

  <Link
    href="/finanzas/ingresos"
    class="px-4 py-2 rounded-xl font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 transition"
  >
    💰 Ingresos
  </Link>

  <Link
    href="/finanzas/gastos"
    class="px-4 py-2 rounded-xl font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 transition"
  >
    🧾 Gastos
  </Link>

  <Link
    href="/finanzas/configuracion"
    class="px-4 py-2 rounded-xl font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition"
  >
    ⚙️ Configuración
  </Link>
</div>

      <slot />
    </div>
  </AppLayout>
</template>

