<template>
  <Card class="overflow-hidden hover:shadow-md transition-shadow duration-200">
    <div class="relative">
      <!-- Service Image or Placeholder -->
      <div class="h-32 bg-tg-section-bg flex items-center justify-center">
        <div v-if="!service.profile_image" class="text-tg-hint">
          <PlaceholderPattern :seed="service.id" class="w-full h-full" />
        </div>
        <img 
          v-else 
          :src="service.profile_image" 
          :alt="service.title" 
          class="w-full h-full object-cover"
        />
      </div>
      
      <!-- Price Badge -->
      <Badge class="absolute top-2 right-2 font-medium">
        {{ service.display_price }}
      </Badge>
    </div>
    
    <CardContent class="p-4">
      <!-- Service Title -->
      <h3 class="font-medium text-base mb-1 line-clamp-1">{{ service.title }}</h3>
      
      <!-- Provider Info -->
      <div class="flex items-center text-sm text-tg-subtitle-text mb-2">
        <Icon name="Store" class="h-3.5 w-3.5 mr-1" />
        <span class="line-clamp-1">{{ service.provider.business_name }}</span>
      </div>
      
      <!-- Service Description -->
      <p class="text-sm text-tg-text mb-3 line-clamp-2">
        {{ service.description || 'No description provided.' }}
      </p>
      
      <!-- Duration -->
      <div class="flex items-center text-xs text-tg-subtitle-text mb-3">
        <Icon name="Clock" class="h-3.5 w-3.5 mr-1" />
        <span>{{ service.duration_minutes }} minutes</span>
      </div>
      
      <!-- Action Button -->
      <div class="mt-2">
        <Button 
          :href="`/services/${service.id}/book`" 
          class="w-full"
          size="sm"
        >
          Book Now
        </Button>
      </div>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { 
  Card, 
  CardContent, 
  Badge, 
  Button 
} from '@/components/ui'
import Icon from '@/components/Icon.vue'
import PlaceholderPattern from '@/components/PlaceholderPattern.vue'

interface Provider {
  id: number
  business_name: string
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

defineProps<{
  service: Service
}>()
</script>
