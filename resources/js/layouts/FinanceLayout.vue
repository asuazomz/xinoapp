<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow p-4 flex gap-4">
        <Link href="/finanzas" class="font-semibold hover:underline">
          Inicio
        </Link>

        <Link href="/finanzas/ingresos" class="font-semibold hover:underline">
          Ingresos
        </Link>

        <Link href="/finanzas/gastos" class="font-semibold hover:underline">
          Gastos
        </Link>

        <Link href="/finanzas/resumen" class="font-semibold hover:underline">
          Resumen
        </Link>

        <Link href="/finanzas/configuracion" class="font-semibold hover:underline">
  Configuración
</Link>
      </div>

      <slot />
    </div>
  </AppLayout>
</template>