<template>
  <div class="min-h-screen bg-tg-bg text-tg-text">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="max-w-md w-full">
        <div class="text-center">
          <div class="mb-6">
            <svg 
              class="mx-auto h-16 w-16 text-tg-link" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor"
            >
              <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" 
              />
            </svg>
          </div>
          
          <h1 class="text-2xl font-bold text-tg-text mb-4">
            Authentication Required
          </h1>
          
          <p class="text-tg-hint mb-6">
            {{ message || 'This application requires valid Telegram authentication. Please make sure you\'re accessing this app through Telegram.' }}
          </p>
          
          <div class="bg-tg-section-bg rounded-lg p-4 mb-6">
            <p class="text-sm text-tg-hint">
              <strong>For developers:</strong><br>
              Make sure your Telegram bot token is configured and the WebApp data is being sent correctly.
            </p>
          </div>
          
          <button 
            @click="closeTelegramApp"
            class="w-full bg-tg-accent text-tg-accent-foreground px-4 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity"
          >
            Close App
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'

defineProps<{
  message?: string
  error?: string
}>()

const closeTelegramApp = () => {
  // Try to close the Telegram WebApp if available
  if (window.Telegram?.WebApp?.close) {
    window.Telegram.WebApp.close()
  } else {
    // Fallback for development
    console.log('Telegram WebApp not available')
  }
}

onMounted(() => {
  // Initialize Telegram WebApp if available
  if (window.Telegram?.WebApp) {
    window.Telegram.WebApp.ready()
    window.Telegram.WebApp.expand()
  }
})
</script>