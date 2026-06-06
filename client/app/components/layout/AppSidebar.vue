<template>
  <aside class="w-72 shrink-0 h-full flex flex-col border-r border-(--ui-border) bg-(--ui-bg) p-4">

    <!-- Logo -->
    <!-- <div class="flex items-center gap-2.5 px-2 py-3 mb-4">
      <div class="size-7 rounded-lg bg-primary-600 flex items-center justify-center shrink-0">
        <UIcon name="i-lucide-zap" class="size-4 text-white" />
      </div>
      <h2 class="font-bold text-base text-(--ui-text-highlighted)">
        ProcureCorp
      </h2>
    </div> -->

    <!-- Navigation -->
    <nav class="flex flex-col gap-0.5 flex-1">
     <NuxtLink
  v-for="item in navItems"
  :key="item.to"
  :to="item.to"
  class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150"
  :class="isActive(item.to)
    ? 'bg-orange-100 text-orange-600'
    : 'text-gray-500 hover:bg-orange-50 hover:text-orange-600 transition duration-90'"
>
  <UIcon
    :name="item.icon"
    class="size-4 shrink-0"
    :class="isActive(item.to)
      ? 'text-orange-600'
      : 'text-gray-400 group-hover:text-orange-600 transition duration-90'"
  />

  <span>{{ item.label }}</span>
</NuxtLink> 
    </nav>

    <!-- Footer -->
   <template>
  <UUser
    name="John Doe"
    description="Software Engineer"
    :avatar="{
      src: 'https://i.pravatar.cc/150?u=john-doe'
    }"
    :chip="{
      color: 'primary',
      position: 'top-right'
    }"
  />
</template>


  </aside>
</template>

<script setup lang="ts">
const route = useRoute()

const navItems = [
  { label: 'Dashboard', to: '/', icon: 'i-lucide-home' },
  { label: 'Vendors', to: '/dashboard/vendor', icon: 'i-lucide-users' },
  { label: 'RFQs', to: '/dashboard/rfqs', icon: 'i-lucide-file-text' },
  { label: 'Orders', to: '/dashboard/order', icon: 'i-lucide-shopping-cart' },
  { label: 'Reports', to: '/dashboard/report', icon: 'i-lucide-bar-chart-3' }
]

function isActive(path: string) {
  return route.path === path || route.path.startsWith(path + '/')
}
</script>