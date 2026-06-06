<script setup lang="ts">
definePageMeta({
  layout: 'default'
})

const stats = [
  { label: 'Total Spend', value: '$1,428,930' },
  { label: 'Active RFQs', value: '42' },
  { label: 'Pending Approvals', value: '18' },
  { label: 'Recent POs', value: '126' },
]

const approvals = [
  { id: 'RFQ-2024-001', vendor: 'Global Tech Solutions', value: '$12,400', status: 'Pending', badgeColor: 'yellow' as const },
  { id: 'RFQ-2024-042', vendor: 'NexGen Industries', value: '$45,000', status: 'Approved', badgeColor: 'green' as const },
  { id: 'PO-882-990', vendor: 'Stellar Ventures', value: '$2,850', status: 'Rejected', badgeColor: 'red' as const },
]

const newsItems = [
  {
    title: 'Supply Chain Trends',
    description: 'Global electronics lead times reduced by 14%.',
    image: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&q=80',
  },
  {
    title: 'New Vendor Spotlights',
    description: 'Welcome 5 new pre-vetted suppliers.',
    image: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=600&q=80',
  },
]

const quickActions = ['New RFQ', 'Add Vendor', 'New Order', 'Reports']

const activities = [
  { icon: 'i-lucide-check-circle', text: 'Order #9812 approved' },
  { icon: 'i-lucide-file-text', text: 'New quotation received' },
  { icon: 'i-lucide-building-2', text: 'Vendor profile updated' },
  { icon: 'i-lucide-alert-triangle', text: 'RFQ flagged for review' },
]
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-orange-900 dark:text-orange-100">
          Dashboard
        </h1>
        <p class="mt-1 text-orange-600 dark:text-orange-400">
          Welcome back Sir.
        </p>
      </div>

      <UButton
        icon="i-lucide-calendar"
        variant="outline"
        class="border-orange-300 text-orange-700 hover:bg-orange-50 dark:border-orange-700 dark:text-orange-300 dark:hover:bg-orange-950"
      >
        Last 30 Days
      </UButton>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <UCard
        v-for="stat in stats"
        :key="stat.label"
        class="border border-orange-200 bg-orange-50 dark:border-orange-800 dark:bg-orange-950"
      >
        <p class="text-xs font-semibold uppercase tracking-widest text-orange-500 dark:text-orange-400">
          {{ stat.label }}
        </p>
        <h2 class="mt-1 text-3xl font-bold text-orange-900 dark:text-orange-100">
          {{ stat.value }}
        </h2>
      </UCard>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

      <!-- Left column -->
      <div class="space-y-6 xl:col-span-2">

        <!-- Urgent Approvals -->
        <UCard class="border border-orange-200 dark:border-orange-800">
          <template #header>
            <div class="flex items-center justify-between px-1">
              <h3 class="font-semibold text-orange-900 dark:text-orange-100">
                Urgent Approvals
              </h3>
              <UButton
                variant="ghost"
                size="xs"
                class="text-orange-500 hover:bg-orange-50 hover:text-orange-700 dark:hover:bg-orange-950"
              >
                View All
              </UButton>
            </div>
          </template>

          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-orange-100 dark:border-orange-800">
                <th class="py-3 text-left text-xs font-semibold uppercase tracking-wider text-orange-500">
                  Request ID
                </th>
                <th class="py-3 text-left text-xs font-semibold uppercase tracking-wider text-orange-500">
                  Vendor
                </th>
                <th class="py-3 text-left text-xs font-semibold uppercase tracking-wider text-orange-500">
                  Value
                </th>
                <th class="py-3 text-left text-xs font-semibold uppercase tracking-wider text-orange-500">
                  Status
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in approvals"
                :key="row.id"
                class="border-b border-orange-50 transition-colors last:border-0 hover:bg-orange-50 dark:border-orange-900 dark:hover:bg-orange-950"
              >
                <td class="py-3 font-mono text-xs text-orange-600 dark:text-orange-400">
                  {{ row.id }}
                </td>
                <td class="py-3 text-gray-800 dark:text-gray-200">
                  {{ row.vendor }}
                </td>
                <td class="py-3 font-medium text-gray-900 dark:text-gray-100">
                  {{ row.value }}
                </td>
                <td class="py-3">
                  <UBadge :colors="row.badgeColor" variant="subtle" size="xs">
                    {{ row.status }}
                  </UBadge>
                </td>
              </tr>
            </tbody>
          </table>
        </UCard>

        <!-- News Cards -->
        <div class="grid gap-4 md:grid-cols-2">
          <UCard
            v-for="news in newsItems"
            :key="news.title"
            class="overflow-hidden border border-orange-200 dark:border-orange-800"
            :ui="{ body: 'p-0' }"
          >
            <img
              :src="news.image"
              :alt="news.title"
              class="h-48 w-full object-cover"
            >
            <div class="p-4">
              <h3 class="font-bold text-orange-900 dark:text-orange-100">
                {{ news.title }}
              </h3>
              <p class="mt-1 text-sm text-orange-600 dark:text-orange-400">
                {{ news.description }}
              </p>
            </div>
          </UCard>
        </div>

      </div>

      <!-- Right column -->
      <div class="space-y-6">

        <!-- Quick Actions -->
        <UCard class="border border-orange-200 dark:border-orange-800">
          <template #header>
            <h3 class="px-1 font-semibold text-orange-900 dark:text-orange-100">
              Quick Actions
            </h3>
          </template>
          <div class="grid grid-cols-2 gap-3">
            <UButton
              v-for="action in quickActions"
              :key="action"
              block
              variant="outline"
              class="border-orange-300 text-orange-700 hover:bg-orange-50 dark:border-orange-700 dark:text-orange-300 dark:hover:bg-orange-950"
            >
              {{ action }}
            </UButton>
          </div>
        </UCard>

        <!-- Recent Activity -->
        <UCard class="border border-orange-200 dark:border-orange-800">
          <template #header>
            <h3 class="px-1 font-semibold text-orange-900 dark:text-orange-100">
              Recent Activity
            </h3>
          </template>
          <div class="space-y-3">
            <div
              v-for="item in activities"
              :key="item.text"
              class="flex items-center gap-3 text-sm"
            >
              <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-orange-200 bg-orange-100 dark:border-orange-700 dark:bg-orange-900">
                <UIcon :name="item.icon" class="h-4 w-4 text-orange-500" />
              </div>
              <span class="text-gray-700 dark:text-gray-300">{{ item.text }}</span>
            </div>
          </div>
        </UCard>

        <!-- System Performance -->
        <div class="rounded-xl bg-orange-500 p-4 dark:bg-orange-600">
          <p class="text-xs font-semibold uppercase tracking-widest text-orange-100">
            System Performance
          </p>
          <h2 class="mt-2 text-3xl font-bold text-white">
            99.9% Uptime
          </h2>
          <p class="mt-1 text-sm text-orange-100">
            Automated processing active.
          </p>
          <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-orange-400 dark:bg-orange-500">
            <div class="h-full w-[99.9%] rounded-full bg-white" />
          </div>
        </div>

      </div>
    </div>
  </div>
</template>