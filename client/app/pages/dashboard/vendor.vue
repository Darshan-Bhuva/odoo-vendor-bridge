<script setup lang="ts">
definePageMeta({
  layout: 'default'
})

const vendors = [
  {
    name: 'Quantum Logistics GmbH',
    category: 'Logistics',
    status: 'Verified',
    gst: '27AAACQ1234A1Z5',
    contact: 'sarah@quantum.de'
  },
  {
    name: 'Apex Manufacturing Inc.',
    category: 'Manufacturing',
    status: 'Pending',
    gst: '06AAACA5678B1Z4',
    contact: 'procurement@apex.com'
  },
  {
    name: 'Skyward IT Solutions',
    category: 'IT & Software',
    status: 'Verified',
    gst: '18AAAAK9876L1ZA',
    contact: 'hi@skyward.io'
  }
]
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
       

        <h1 class="text-3xl font-bold mt-2">
          Vendor Management
        </h1>

        <p class="text-gray-500">
          Manage your supplier network and onboarding workflows.
        </p>
      </div>

      <UButton
        icon="i-lucide-plus"
      >
        Add New Vendor
      </UButton>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4">

      <UCard>
        <p class="text-xs text-gray-500">TOTAL VENDORS</p>
        <h2 class="text-3xl font-bold">1,284</h2>
      </UCard>

      <UCard>
        <p class="text-xs text-gray-500">VERIFIED</p>
        <h2 class="text-3xl font-bold text-green-600">1,102</h2>
      </UCard>

      <UCard>
        <p class="text-xs text-gray-500">PENDING</p>
        <h2 class="text-3xl font-bold text-orange-500">142</h2>
      </UCard>

      <UCard>
        <p class="text-xs text-gray-500">BLACKLISTED</p>
        <h2 class="text-3xl font-bold text-red-500">40</h2>
      </UCard>

    </div>

    <!-- Filters -->
    <UCard>
      <div class="flex gap-3">

        <UInput
          icon="i-lucide-search"
          placeholder="Filter by name, GSTIN or location..."
          class="flex-1"
        />

        <USelect
          :items="['All Categories','Logistics','Manufacturing','IT']"
        />

        <USelect
          :items="['All Status','Verified','Pending','Blacklisted']"
        />

        <UButton
          variant="outline"
          icon="i-lucide-filter"
        >
          More Filters
        </UButton>

      </div>
    </UCard>

    <!-- Vendor Table -->
    <UCard>

      <table class="w-full">

        <thead>
          <tr class="border-b text-left">
            <th class="py-4">Vendor Name</th>
            <th>Category</th>
            <th>Status</th>
            <th>GST Details</th>
            <th>Contact</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>

          <tr
            v-for="vendor in vendors"
            :key="vendor.name"
            class="border-b"
          >
            <td class="py-4 font-medium">
              {{ vendor.name }}
            </td>

            <td>
              <UBadge variant="soft">
                {{ vendor.category }}
              </UBadge>
            </td>

            <td>
              <UBadge
                :color="
                  vendor.status === 'Verified'
                    ? 'success'
                    : vendor.status === 'Pending'
                    ? 'warning'
                    : 'error'
                "
              >
                {{ vendor.status }}
              </UBadge>
            </td>

            <td>
              {{ vendor.gst }}
            </td>

            <td>
              {{ vendor.contact }}
            </td>

            <td>
              <div class="flex gap-2">
                <UButton
                  icon="i-lucide-pencil"
                  size="xs"
                  variant="ghost"
                />
                <UButton
                  icon="i-lucide-eye"
                  size="xs"
                  variant="ghost"
                />
              </div>
            </td>

          </tr>

        </tbody>

      </table>

    </UCard>

    <!-- Bottom Cards -->
    <div class="grid grid-cols-3 gap-6">

      <UCard class="col-span-2">

        <template #header>
          Quick Vendor Summary
        </template>

        <div class="grid grid-cols-3 gap-6">

          <div>
            <p class="text-sm text-gray-500">
              Compliance Score
            </p>

            <h2 class="text-4xl font-bold text-green-600">
              94%
            </h2>
          </div>

          <div>
            <p class="text-sm text-gray-500">
              Avg Delivery Time
            </p>

            <h2 class="text-4xl font-bold">
              3.2
            </h2>

            <span class="text-gray-500">
              days
            </span>
          </div>

          <div>
            <p class="text-sm text-gray-500">
              Active Orders
            </p>

            <h2 class="text-4xl font-bold">
              12
            </h2>
          </div>

        </div>

      </UCard>

      <UCard class="bg-slate-950 text-white">

        <template #header>
          Audit Readiness
        </template>

        <p class="text-sm opacity-80">
          Ensure vendor documentation is up-to-date before the next audit.
        </p>

        <UButton
          class="mt-6"
          block
          color="neutral"
        >
          Run Compliance Check
        </UButton>

      </UCard>

    </div>

  </div>
</template>