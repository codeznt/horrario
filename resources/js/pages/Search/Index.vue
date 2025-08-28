<template>
  <AppShell>
    <div class="max-w-4xl mx-auto px-4 py-6">
      <Heading title="Find Services"></Heading>
      
      <div class="mt-6">
        <SearchForm />
      </div>
      
      <div class="mt-8">
        <h2 class="text-lg font-medium mb-4">Popular Services</h2>
        
        <!-- Display popular services if available -->
        <div v-if="popularServices && popularServices.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <ServiceCard 
            v-for="service in popularServices" 
            :key="`service-${service.id}`" 
            :service="service" 
          />
        </div>
        
        <!-- Skeleton loader shown only when loading -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <div v-for="i in 3" :key="i" class="animate-pulse">
            <div class="h-32 bg-tg-section-bg rounded-t-md"></div>
            <div class="p-4 bg-tg-section-bg rounded-b-md space-y-2">
              <div class="h-4 bg-tg-section-separator rounded w-3/4"></div>
              <div class="h-3 bg-tg-section-separator rounded w-1/2"></div>
              <div class="h-10 bg-tg-section-separator rounded w-full mt-3"></div>
            </div>
          </div>
        </div>
        
        <div v-if="!popularServices || popularServices.length === 0" class="mt-8 text-center">
          <p class="text-tg-subtitle-text">
            Search for services by name, provider, or category
          </p>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup lang="ts">
import AppShell from '@/components/AppShell.vue'
import Heading from '@/components/Heading.vue'
import SearchForm from '@/components/SearchForm.vue'
import ServiceCard from '@/components/ServiceCard.vue'

interface Provider {
  id: number
  business_name: string
  description: string | null
  profile_image?: string
}

interface Service {
  id: number
  title: string
  description: string | null
  duration_minutes: number
  display_price: string
  provider: Provider
  profile_image?: string
}

interface Props {
  popularServices?: Service[]
}

const props = defineProps<Props>()

// Provide default empty array if popularServices is undefined
const popularServices = props.popularServices || []
</script>
