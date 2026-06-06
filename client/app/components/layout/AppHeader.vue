<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from '#imports'

const router = useRouter()
const isLoggedIn = ref(false)

function goToLogin() {
  router.push('/login')
}
function goToDashboard() {
  router.push('/')
}
function logout() {
  localStorage.removeItem('auth_token')
  isLoggedIn.value = false
  // notify same-window listeners
  window.dispatchEvent(new Event('auth-changed'))
  router.push('/')
}

function handleAuthChange() {
  isLoggedIn.value = !!localStorage.getItem('auth_token')
}

onMounted(() => {
  handleAuthChange()
  window.addEventListener('auth-changed', handleAuthChange)
  window.addEventListener('storage', handleAuthChange) // cross-tab updates
})
onBeforeUnmount(() => {
  window.removeEventListener('auth-changed', handleAuthChange)
  window.removeEventListener('storage', handleAuthChange)
})
</script>

<template>
  <UHeader>

    <!-- Logo -->
    <template #title>
      <NuxtLink
        to="/"
        class="flex items-center gap-3"
      >
        <img
          src="\image.png"
          alt="Vendor Bridge"
          class="h-15 w-auto "
        >
      </NuxtLink>
    </template>

    <!-- Right Side -->
    <template #right>
      <div class="flex items-center gap-2">

        <UButton
          icon="i-lucide-bell"
          variant="ghost"
          color="neutral"
        />

        <UButton
          icon="i-lucide-settings"
          variant="ghost"
          color="neutral"
        />

        <UColorModeButton />

        <!-- If user not logged in, show Login button -->
        <UButton
          v-if="!isLoggedIn"
          variant="ghost"
          color="primary"
          class="ml-2"
          @click="goToLogin"
        >
          Login
        </UButton>

        <!-- If logged in, show Dashboard and Logout buttons and avatar -->
        <div v-else class="flex items-center gap-2">
          <UButton
            variant="ghost"
            color="neutral"
            @click="goToDashboard"
          >
            Dashboard
          </UButton>

          <UButton
            variant="ghost"
            colors="danger"
            @click="logout"
          >
            Logout
          </UButton>

          <UAvatar
            src="https://i.pravatar.cc/150?img=12"
            size="sm"
          />
        </div>

      </div>
    </template>

  </UHeader>
</template>