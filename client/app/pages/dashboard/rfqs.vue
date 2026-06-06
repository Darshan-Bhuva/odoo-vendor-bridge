<script setup lang="ts">
definePageMeta({
  layout: 'default'
})

const items = ref([
  {
    product: 'Enterprise Server Node X1',
    qty: 12,
    specs: '64GB RAM, 2TB SSD, Intel Xeon'
  }
])
</script>

<template>
  <div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
       

        <h1 class="text-3xl font-bold mt-2">
          Create Request for Quotation
        </h1>
      </div>

      <div class="flex gap-3">
        <UButton
          color="neutral"
          variant="outline"
        >
          Save as Draft
        </UButton>

        <UButton>
          Submit for Approval
        </UButton>
      </div>
    </div>

    <div class="grid grid-cols-12 gap-6">

      <!-- Steps -->
    <div class="col-span-2">
  <div class="space-y-8">

    <div class="flex gap-3">
      <div class="flex flex-col items-center">
        <div class="size-3 rounded-full bg-blue-600" />
        <div class="w-px h-16 bg-gray-200" />
      </div>

      <div>
        <p class="font-medium text-sm text-blue-600">
          RFQ Details
        </p>
        <p class="text-xs text-gray-500">
          Basic information & deadline
        </p>
      </div>
    </div>

    <div class="flex gap-3">
      <div class="flex flex-col items-center">
        <div class="size-3 rounded-full bg-gray-300" />
        <div class="w-px h-16 bg-gray-200" />
      </div>

      <div>
        <p class="font-medium text-sm">
          Items List
        </p>
        <p class="text-xs text-gray-500">
          Line items and specifications
        </p>
      </div>
    </div>

    <div class="flex gap-3">
      <div class="flex flex-col items-center">
        <div class="size-3 rounded-full bg-gray-300" />
        <div class="w-px h-16 bg-gray-200" />
      </div>

      <div>
        <p class="font-medium text-sm">
          Attachments
        </p>
        <p class="text-xs text-gray-500">
          Upload supporting documents
        </p>
      </div>
    </div>

    <div class="flex gap-3">
      <div class="size-3 rounded-full bg-gray-300 mt-1" />

      <div>
        <p class="font-medium text-sm">
          Vendor Assignment
        </p>
        <p class="text-xs text-gray-500">
          Select vendors to invite
        </p>
      </div>
    </div>

  </div>
</div>

      <!-- Form -->
      <div class="col-span-10 space-y-6">

        <!-- RFQ Details -->
        <UCard>
          <template #header>
            <h3 class="font-semibold">
              RFQ Details
            </h3>
          </template>

          <div class="grid md:grid-cols-2 gap-4">

            <UFormField label="RFQ Title">
              <UInput
                placeholder="e.g. Annual IT Infrastructure Hardware Renewal 2024"
              />
            </UFormField>

            <div />

            <UFormField label="Category">
              <USelect
                :items="[
                  'Hardware & Equipment',
                  'Software',
                  'Services'
                ]"
              />
            </UFormField>

            <UFormField label="Submission Deadline">
              <UInput
                type="date"
              />
            </UFormField>

          </div>
        </UCard>

        <!-- Items -->
        <UCard>

          <template #header>
            <div class="flex justify-between">
              <h3 class="font-semibold">
                Items List
              </h3>

              <UButton
                icon="i-lucide-plus"
                variant="ghost"
              >
                Add New Item
              </UButton>
            </div>
          </template>

          <table class="w-full text-sm">

            <thead>
              <tr class="border-b">
                <th class="text-left py-3">
                  Product Name
                </th>
                <th class="text-left py-3">
                  Qty
                </th>
                <th class="text-left py-3">
                  Specifications
                </th>
                <th />
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="(item,index) in items"
                :key="index"
                class="border-b"
              >
                <td class="py-3">
                  <UInput v-model="item.product" />
                </td>

                <td>
                  <UInput
                    v-model="item.qty"
                    type="number"
                  />
                </td>

                <td>
                  <UInput v-model="item.specs" />
                </td>

                <td>
                  <UButton
                    icon="i-lucide-trash-2"
                    color="error"
                    variant="ghost"
                  />
                </td>
              </tr>
            </tbody>

          </table>

        </UCard>

        <!-- Attachments -->
        <UCard>

          <template #header>
            <h3 class="font-semibold">
              Attachments
            </h3>
          </template>

          <div
            class="border-2 border-dashed rounded-xl p-12 text-center"
          >
            <UIcon
              name="i-lucide-upload"
              class="size-12 mx-auto mb-4 text-gray-400"
            />

            <p>
              Click to upload or drag and drop
            </p>

            <p class="text-sm text-gray-500">
              PDF, DOCX, XLSX, JPG, PNG
            </p>
          </div>

        </UCard>

        <!-- Vendors -->
        <UCard>

          <template #header>
            <div class="flex justify-between">
              <h3 class="font-semibold">
                Vendor Assignment
              </h3>

              <UInput
                icon="i-lucide-search"
                placeholder="Search approved vendors..."
              />
            </div>
          </template>

          <div class="grid md:grid-cols-2 gap-4">

            <UCard>
              <div class="flex justify-between">
                <div>
                  <h4 class="font-semibold">
                    Synergetic Circuits Inc.
                  </h4>

                  <p class="text-sm text-gray-500">
                    Hardware, Infrastructure
                  </p>
                </div>

                <UBadge color="success">
                  ELITE
                </UBadge>
              </div>
            </UCard>

            <UCard>
              <div class="flex justify-between">
                <div>
                  <h4 class="font-semibold">
                    Nexus Digital Solutions
                  </h4>

                  <p class="text-sm text-gray-500">
                    Cloud Services
                  </p>
                </div>

                <UBadge>
                  STANDARD
                </UBadge>
              </div>
            </UCard>

          </div>

        </UCard>

      </div>

    </div>

  </div>
</template>