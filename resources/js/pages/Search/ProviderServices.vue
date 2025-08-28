<template>
  <AppShell>
    <div class="max-w-4xl mx-auto px-4 py-6">
      <!-- Back Button -->
      <div class="mb-4">
        <Button 
          @click="router.get('/search')" 
          variant="ghost" 
          size="sm"
          class="flex items-center"
        >
          <Icon name="ArrowLeft" class="h-4 w-4 mr-1" />
          Back to Search
        </Button>
      </div>
      
      <!-- Provider Header -->
      <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-6">
        <!-- Provider Image -->
        <div class="h-20 w-20 rounded-full overflow-hidden bg-tg-section-bg flex-shrink-0">
          <div v-if="!provider.profile_image" class="h-full w-full">
            <PlaceholderPattern :seed="provider.id" class="h-full w-full" />
          </div>
          <img 
            v-else 
            :src="provider.profile_image" 
            :alt="provider.business_name" 
            class="h-full w-full object-cover"
          />
        </div>
        
        <!-- Provider Info -->
        <div class="flex-1">
          <Heading :title="provider.business_name"></Heading>
          <p class="text-tg-subtitle-text mt-1">
            {{ provider.description || 'No description provided.' }}
          </p>
        </div>
      </div>
      
      <!-- Services List -->
      <div class="mt-8">
        <h2 class="text-lg font-medium mb-4">Available Services</h2>
        
        <div v-if="services.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <ServiceCard 
            v-for="service in services.data" 
            :key="`service-${service.id}`" 
            :service="service" 
          />
        </div>
        
        <div v-else class="text-center py-8">
          <Icon name="Calendar" class="h-12 w-12 mx-auto text-tg-hint" />
          <p class="mt-4 text-tg-subtitle-text">This provider has no available services.</p>
        </div>
        
        <!-- Pagination -->
        <div v-if="services.data.length > 0" class="mt-8 flex justify-center">
          <div class="flex space-x-2">
            <Button 
              v-for="link in services.links" 
              :key="link.label"
              :disabled="!link.url || link.active"
              :variant="link.active ? 'default' : 'outline'"
              @click="goToPage(link.url)"
              v-html="link.label"
              class="w-10 h-10 p-0 flex items-center justify-center"
            />
          </div>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AppShell from '@/components/AppShell.vue'
import Heading from '@/components/Heading.vue'
import ServiceCard from '@/components/ServiceCard.vue'
import PlaceholderPattern from '@/components/PlaceholderPattern.vue'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui'

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

interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

interface Pagination {
  data: Service[]
  links: PaginationLink[]
  current_page: number
  last_page: number
  total: number
}

interface Props {
  provider: Provider
  services: Pagination
}

const props = defineProps<Props>()

function goToPage(url: string | null) {
  if (!url) return
  
  // Extract the page number from the URL
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  
  router.get(`/search/provider/${props.provider.id}`, { page }, {
    preserveState: true,
    preserveScroll: true,
    only: ['services']
  })
}
</script>
