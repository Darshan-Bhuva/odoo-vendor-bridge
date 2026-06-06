<script setup lang="ts">
import AppHeader from '~/components/layout/AppHeader.vue';
import AppSidebar from '~/components/layout/AppSidebar.vue';

</script>

<template>
  <div class="h-screen flex flex-col">
    <AppHeader />

    <div class="flex flex-1 overflow-hidden">
      <AppSidebar />

      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>