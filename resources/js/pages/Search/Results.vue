<template>
  <AppShell>
    <div class="max-w-4xl mx-auto px-4 py-6">
      <Heading title="Search Results"></Heading>
      
      <!-- Search Form -->
      <div class="mt-4 mb-6">
        <SearchForm 
          :initial-query="filters.query"
          :initial-category="filters.category"
          :initial-sort="filters.sort"
        />
      </div>
      
      <!-- Search Summary -->
      <div class="mb-6">
        <p v-if="filters.query" class="text-tg-subtitle-text">
          {{ totalResults }} results for "{{ filters.query }}"
          <span v-if="filters.category"> in {{ filters.category }}</span>
        </p>
        <p v-else class="text-tg-subtitle-text">
          {{ totalResults }} services found
          <span v-if="filters.category"> in {{ filters.category }}</span>
        </p>
      </div>
      
      <!-- Provider Results (if any) -->
      <div v-if="providers.length > 0" class="mb-8">
        <h2 class="text-lg font-medium mb-4">Providers</h2>
        
        <div class="space-y-4">
          <Card v-for="provider in providers" :key="`provider-${provider.id}`" class="overflow-hidden">
            <div class="flex items-center p-4">
              <!-- Provider Image -->
              <div class="h-16 w-16 rounded-full overflow-hidden bg-tg-section-bg flex-shrink-0">
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
              <div class="ml-4 flex-1">
                <h3 class="font-medium">{{ provider.business_name }}</h3>
                <p class="text-sm text-tg-subtitle-text line-clamp-1">
                  {{ provider.description || 'No description provided.' }}
                </p>
              </div>
              
              <!-- View Provider Button -->
              <Button 
                :href="`/search/provider/${provider.id}`" 
                variant="outline"
                size="sm"
              >
                View Services
              </Button>
            </div>
          </Card>
        </div>
      </div>
      
      <!-- Service Results -->
      <div>
        <h2 class="text-lg font-medium mb-4">Services</h2>
        
        <div v-if="services.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <ServiceCard 
            v-for="service in services.data" 
            :key="`service-${service.id}`" 
            :service="service" 
          />
        </div>
        
        <div v-else class="text-center py-8">
          <Icon name="Search" class="h-12 w-12 mx-auto text-tg-hint" />
          <p class="mt-4 text-tg-subtitle-text">No services found matching your search criteria.</p>
          <Button @click="resetSearch" variant="outline" class="mt-4">
            Clear Search
          </Button>
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
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppShell from '@/components/AppShell.vue'
import Heading from '@/components/Heading.vue'
import SearchForm from '@/components/SearchForm.vue'
import ServiceCard from '@/components/ServiceCard.vue'
import PlaceholderPattern from '@/components/PlaceholderPattern.vue'
import Icon from '@/components/Icon.vue'
import { 
  Card,
  Button
} from '@/components/ui'

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

interface Filters {
  query: string
  category: string
  sort: string
}

interface Props {
  services: Pagination
  providers: Provider[]
  filters: Filters
}

const props = defineProps<Props>()

const totalResults = computed(() => {
  return props.services.total + props.providers.length
})

function resetSearch() {
  router.get('/search', {})
}

function goToPage(url: string | null) {
  if (!url) return
  
  // Extract the page number from the URL
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  
  router.get('/search', {
    query: props.filters.query,
    category: props.filters.category,
    sort: props.filters.sort,
    page
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['services']
  })
}
</script>
