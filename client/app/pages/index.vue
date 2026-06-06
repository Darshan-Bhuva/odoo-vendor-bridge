<script setup lang="ts">
useSeoMeta({
  title: 'Vendor Bridge',
  description: 'Vendor Management and Procurement Platform'
})

import dashboard from './dashboard/dashboard.vue';
</script>

<template>
  <div class="h-screen flex flex-col">
    <dashboard/>
  </div>
</template>