<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue'

defineProps<{
  message: string
  required_role: string
  user_role: string
  dashboard_url: string
}>()
</script>

<template>
  <AppLayoutWithNavigation>
    <Head title="Access Denied" />
    
    <div class="flex items-center justify-center py-8">
      <div class="max-w-md w-full space-y-8 px-6">
        <div class="text-center">
          <!-- Error Icon -->
          <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-red-100">
            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
          </div>
          
          <h1 class="mt-6 text-3xl font-bold text-tg-text">
            Access Denied
          </h1>
          
          <p class="mt-2 text-tg-hint">
            {{ message }}
          </p>
          
          <div class="mt-6 p-4 bg-tg-section-bg rounded-lg">
            <div class="text-sm text-tg-text space-y-1">
              <p><strong>Required Role:</strong> {{ required_role }}</p>
              <p><strong>Your Role:</strong> {{ user_role }}</p>
            </div>
          </div>
        </div>
        
        <div class="flex justify-center space-x-4">
          <Link
            :href="dashboard_url"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-tg-button-color hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            Back to Dashboard
          </Link>
        </div>
      </div>
    </div>
  </AppLayoutWithNavigation>
</template>