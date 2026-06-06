<script setup lang="ts">
import * as z from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

// Use the custom premium auth layout
definePageMeta({
  layout: 'auth'
})

const toast = useToast()
const router = useRouter()
const config = useRuntimeConfig()

// Profile photo state (optional preview)
const photoUrl = ref<string | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)
const loading = ref(false)

function onPhotoChange(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    photoUrl.value = URL.createObjectURL(file)
  }
}

// Zod Validation Schema
const schema = z.object({
  email: z.string().email('Please enter a valid email address'),
  password: z.string().min(8, 'Must be at least 8 characters')
})

type Schema = z.output<typeof schema>

const fields = [
  {
    name: 'email',
    type: 'email',
    label: 'Email',
    placeholder: 'Enter your email address',
    required: true
  },
  {
    name: 'password',
    label: 'Password',
    type: 'password',
    placeholder: 'Enter your password',
    required: true
  }
]

// Login API handler
async function onSubmit(payload?: FormSubmitEvent<Schema>) {
  console.log('onSubmit called', payload)
  if (!payload) return
  loading.value = true
  
  try {
    const body = { email: payload.data.email, password: payload.data.password }
    const apiUrl = config.public.apiUrl
    
    console.log('Sending login request to:', `${apiUrl}/login`, body)

    const response = await $fetch<{ token: string; message?: string }>(
      `${apiUrl}/login`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body
      }
    )

    console.log('Login response:', response)
    
    const token = response.token
    if (token) {
      localStorage.setItem('auth_token', token)
      // notify components of auth change
      window.dispatchEvent(new Event('auth-changed'))
      
      toast.add({
        title: 'Logged In Successfully',
        description: response.message || 'Welcome back to Vendor Bridge!',
        color: 'green'
      })
      
      router.push('/')
    } else {
      throw new Error('Authorization token was not received from the server.')
    }
  } catch (error: any) {
    console.error('Login Error full:', error)
    
    const errorDetails = error.data?.message || error.message || 'Unable to connect to the login server.'
    
    toast.add({
      title: 'Login Failed',
      description: errorDetails,
      color: 'red'
    })
  } finally {
    loading.value = false
  }
}

// Developer Bypass function
function onBypass() {
  localStorage.setItem('auth_token', 'demo_bypass_token')
  window.dispatchEvent(new Event('auth-changed'))
  
  toast.add({
    title: 'Developer Mode',
    description: 'Bypassed login. Signed in as Demo User.',
    color: 'orange'
  })
  
  router.push('/')
}
</script>

<template>
  <div class="w-full flex flex-col gap-4 p-2">
    <!-- UI Auth Form -->
    <UAuthForm
      :schema="schema"
      title="Sign In"
      description="Enter your email and password to log in."
      icon="i-lucide-lock"
      :fields="fields"
      :submit="{ label: 'Sign In', block: true, loading }"
      :validate-on="['blur']"
      @submit="onSubmit"
      class="text-zinc-100"
    >
      <!-- Optional profile preview slot -->
      <template #header>
        <div class="flex flex-col items-center gap-3 pb-4">
          <div
            class="relative cursor-pointer group"
            @click="fileInput?.click()"
          >
            <UAvatar
              :src="photoUrl ?? undefined"
              icon="i-lucide-user"
              size="3xl"
              class="ring-2 ring-orange-500 ring-offset-2 ring-offset-zinc-900 bg-zinc-800"
            />
            <div class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
              <UIcon name="i-lucide-camera" class="text-white text-xl" />
            </div>
          </div>
          <p class="text-xs text-zinc-400">Click to upload avatar preview</p>
          <input
            ref="fileInput"
            type="file"
            accept="image/*"
            class="hidden"
            @change="onPhotoChange"
          />
        </div>
      </template>

      <!-- Custom footer with developer bypass trigger -->
      <template #footer>
        <div class="flex flex-col gap-3 mt-4 w-full">
          <div class="relative flex items-center justify-center my-1">
            <span class="absolute inset-x-0 h-px bg-zinc-800"></span>
            <span class="relative bg-zinc-900/60 backdrop-blur-md px-3 text-[10px] text-zinc-500 uppercase tracking-widest">
              Development Options
            </span>
          </div>
          <UButton
            block
            color="neutral"
            variant="outline"
            icon="i-lucide-shield-alert"
            class="border-orange-500/30 text-orange-400 hover:bg-orange-500/10 hover:text-orange-300 transition-all duration-200"
            @click="onBypass"
          >
            Demo Bypass (Skip Login)
          </UButton>
        </div>
      </template>
    </UAuthForm>
  </div>
</template>