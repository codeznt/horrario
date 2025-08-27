<template>
  <TelegramAppLayout>
    <div class="max-w-2xl mx-auto p-4">
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-tg-text mb-2">
          {{ message }}
        </h1>
        <p class="text-tg-hint">
          Welcome, {{ user.first_name }}!
        </p>
      </div>

      <!-- User Info Card -->
      <div class="bg-tg-section-bg rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold text-tg-text mb-3">
          Your Profile
        </h2>
        
        <div class="grid grid-cols-1 gap-3">
          <div class="flex justify-between">
            <span class="text-tg-hint">Name:</span>
            <span class="text-tg-text">{{ user.name }}</span>
          </div>
          
          <div class="flex justify-between" v-if="user.username">
            <span class="text-tg-hint">Username:</span>
            <span class="text-tg-text">@{{ user.username }}</span>
          </div>
          
          <div class="flex justify-between">
            <span class="text-tg-hint">Language:</span>
            <span class="text-tg-text">{{ user.language_code || 'Unknown' }}</span>
          </div>
          
          <div class="flex justify-between">
            <span class="text-tg-hint">Role:</span>
            <span class="text-tg-text">{{ user.role }}</span>
          </div>
          
          <div class="flex justify-between">
            <span class="text-tg-hint">Premium:</span>
            <span :class="user.is_premium ? 'text-tg-link' : 'text-tg-hint'">
              {{ user.is_premium ? 'Yes' : 'No' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="space-y-3">
        <button 
          @click="openMainBot"
          class="w-full bg-tg-accent text-tg-accent-foreground px-4 py-3 rounded-lg font-medium hover:opacity-90 transition-opacity"
        >
          Open Main Bot
        </button>
        
        <button 
          @click="shareApp"
          class="w-full bg-tg-secondary-bg text-tg-text px-4 py-3 rounded-lg font-medium hover:opacity-90 transition-opacity border border-tg-section-separator"
        >
          Share App
        </button>
      </div>

      <!-- Debug Info (Development only) -->
      <div v-if="isDev && telegramData" class="mt-8 bg-tg-section-bg rounded-lg p-4">
        <h3 class="text-sm font-semibold text-tg-text mb-2">
          Debug Info
        </h3>
        <pre class="text-xs text-tg-hint overflow-auto">{{ JSON.stringify(telegramData, null, 2) }}</pre>
      </div>
    </div>
  </TelegramAppLayout>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue'

interface User {
  id: number
  name: string
  first_name: string
  last_name?: string
  username?: string
  language_code?: string
  role: string
  is_premium: boolean
  allows_write_to_pm: boolean
  telegram_id: number
}

const props = defineProps<{
  message: string
  user: User
  telegramData?: Record<string, any>
}>()

const isDev = computed(() => import.meta.env.DEV)

const openMainBot = () => {
  // Open the main bot chat
  if (window.Telegram?.WebApp?.openTelegramLink) {
    window.Telegram.WebApp.openTelegramLink('https://t.me/your_bot_username')
  }
}

const shareApp = () => {
  // Share the app
  if (window.Telegram?.WebApp?.openTelegramLink) {
    const shareUrl = `https://t.me/share/url?url=${encodeURIComponent(window.location.origin)}&text=${encodeURIComponent('Check out this awesome Telegram Mini App!')}`
    window.Telegram.WebApp.openTelegramLink(shareUrl)
  }
}

onMounted(() => {
  // Initialize Telegram WebApp if available
  if (window.Telegram?.WebApp) {
    window.Telegram.WebApp.ready()
    window.Telegram.WebApp.expand()
    
    // Set main button if needed
    window.Telegram.WebApp.MainButton.text = 'Main Action'
    window.Telegram.WebApp.MainButton.show()
  }
})
</script>