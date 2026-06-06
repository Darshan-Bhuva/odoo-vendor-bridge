<script setup lang="ts">
definePageMeta({
  layout: 'default'
})

const vendors = [
  {
    name: 'NorthStar Electronics',
    price: '$42,500',
    delivery: '14-16 Days',
    badge: 'Lowest Price',
    color: 'success'
  },
  {
    name: 'Quantum Components',
    price: '$48,250',
    delivery: '5-7 Days',
    badge: 'Shortest Delivery',
    color: 'primary'
  },
  {
    name: 'Sapex Valve Co.',
    price: '$45,900',
    delivery: '10-12 Days',
    badge: '',
    color: 'neutral'
  }
]
</script>

<template>
  <div class="space-y-6">

    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold">
          Quotation Comparison Analysis
        </h1>

        <p class="text-gray-500">
          RFQ: High-Precision Industrial Sensors (500 Units)
        </p>
      </div>

      <div class="flex gap-3">
        <UButton
          icon="i-lucide-download"
          variant="outline"
        >
          Export Report
        </UButton>

        <UButton
          icon="i-lucide-share-2"
          color="primary"
        >
          Share Comparison
        </UButton>
      </div>
    </div>

    <!-- Vendor Comparison -->

    <div class="grid lg:grid-cols-3 gap-4">

      <UCard
        v-for="vendor in vendors"
        :key="vendor.name"
      >
        <template #header>
          <div class="flex justify-between">
            <h3 class="font-bold">
              {{ vendor.name }}
            </h3>

            <UBadge
              v-if="vendor.badge"
              :colors="vendor.color"
            >
              {{ vendor.badge }}
            </UBadge>
          </div>
        </template>

        <div class="space-y-3">
          <div>
            <p class="text-sm text-gray-500">
              Quote Total
            </p>

            <p class="text-3xl font-bold">
              {{ vendor.price }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">
              Delivery
            </p>

            <p class="font-semibold">
              {{ vendor.delivery }}
            </p>
          </div>
        </div>
      </UCard>

    </div>

    <!-- Comparison Table -->

    <UCard>

      <table class="w-full text-sm">
        <thead>
          <tr class="border-b">
            <th class="text-left p-4">Line Item</th>
            <th class="text-left p-4">NorthStar</th>
            <th class="text-left p-4">Quantum</th>
            <th class="text-left p-4">Sapex</th>
          </tr>
        </thead>

        <tbody>

          <tr class="border-b">
            <td class="p-4 font-medium">
              PRX-900 High-Res Sensor
            </td>

            <td class="p-4">
              $80 / unit
            </td>

            <td class="p-4">
              $90 / unit
            </td>

            <td class="p-4">
              $85 / unit
            </td>
          </tr>

          <tr class="border-b">
            <td class="p-4 font-medium">
              Installation Kit
            </td>

            <td class="p-4">
              $5 / unit
            </td>

            <td class="p-4">
              $6.5 / unit
            </td>

            <td class="p-4">
              $6.8 / unit
            </td>
          </tr>

          <tr class="border-b">
            <td class="p-4 font-medium">
              Freight & Handling
            </td>

            <td class="p-4">
              Included
            </td>

            <td class="p-4">
              Expedited
            </td>

            <td class="p-4">
              Standard Ground
            </td>
          </tr>

          <tr>
            <td class="p-4 font-medium">
              Payment Terms
            </td>

            <td class="p-4">
              Net 30
            </td>

            <td class="p-4">
              Net 45
            </td>

            <td class="p-4">
              Net 30
            </td>
          </tr>

        </tbody>
      </table>

    </UCard>

    <!-- Actions -->

    <div class="grid lg:grid-cols-3 gap-4">

      <UButton
        block
        color="success"
      >
        Award PO
      </UButton>

      <UButton
        block
        color="primary"
      >
        Award PO
      </UButton>

      <UButton
        block
        color="neutral"
      >
        Award PO
      </UButton>

    </div>

    <!-- Bottom Stats -->

    <div class="grid lg:grid-cols-2 gap-6">

      <UCard>
        <template #header>
          <h3 class="font-semibold">
            Market Benchmarking
          </h3>
        </template>

        <p class="text-gray-600">
          This quote comparison reflects a 4.2% reduction in price.
        </p>

        <div class="mt-6">
          <div class="flex justify-between mb-2">
            <span>Market Savings</span>
            <span class="text-green-600 font-bold">
              7.4%
            </span>
          </div>

          <UProgress :value="74" />
        </div>
      </UCard>

      <UCard>
        <template #header>
          <h3 class="font-semibold">
            Historical Performance
          </h3>
        </template>

        <div class="space-y-5">

          <div>
            <div class="flex justify-between">
              <span>OTIF (NorthStar)</span>
              <span>98%</span>
            </div>

            <UProgress
              :value="98"
              color="success"
            />
          </div>

          <div>
            <div class="flex justify-between">
              <span>Quality Score (Quantum)</span>
              <span>100%</span>
            </div>

            <UProgress
              :value="100"
              color="primary"
            />
          </div>

          <div>
            <div class="flex justify-between">
              <span>On-Time (Sapex)</span>
              <span>82%</span>
            </div>

            <UProgress
              :value="82"
              color="warning"
            />
          </div>

        </div>
      </UCard>

    </div>

  </div>
</template>