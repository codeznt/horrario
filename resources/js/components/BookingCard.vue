<template>
  <Card class="bg-tg-section-bg border-tg-section-separator" :class="statusClass">
    <CardContent class="p-4">
      <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-2">
            <h3 class="font-semibold text-tg-text">{{ booking.service.title }}</h3>
            <Badge :variant="statusVariant" class="text-xs">
              {{ formatStatus(booking.status) }}
            </Badge>
          </div>
          
          <div class="space-y-1 text-sm">
            <div class="flex items-center gap-2 text-tg-subtitle-text">
              <Icon name="Calendar" class="h-3 w-3" />
              {{ formatDate(booking.start_datetime) }}
            </div>
            <div class="flex items-center gap-2 text-tg-subtitle-text">
              <Icon name="Clock" class="h-3 w-3" />
              {{ formatTime(booking.start_datetime) }} - {{ formatTime(booking.end_datetime) }}
            </div>
            <div class="flex items-center gap-2 text-tg-subtitle-text">
              <Icon name="MapPin" class="h-3 w-3" />
              {{ booking.provider.business_name }}
            </div>
          </div>
        </div>

        <div class="text-right">
          <div class="font-semibold text-tg-accent">${{ booking.service.price }}</div>
        </div>
      </div>

      <div v-if="booking.notes" class="mb-3 p-2 bg-tg-bg rounded text-sm text-tg-subtitle-text">
        <Icon name="MessageSquare" class="h-3 w-3 inline mr-1" />
        {{ booking.notes }}
      </div>

      <div v-if="canCancel" class="flex gap-2 pt-2 border-t border-tg-section-separator">
        <Button
          variant="outline"
          size="sm"
          @click="$emit('cancel', booking.id)"
          class="flex-1"
        >
          <Icon name="X" class="h-3 w-3 mr-1" />
          Cancel Booking
        </Button>
        
        <Button
          variant="ghost"
          size="sm"
          @click="router.visit(`/bookings/${booking.id}`)"
          class="flex-1"
        >
          <Icon name="Eye" class="h-3 w-3 mr-1" />
          Details
        </Button>
      </div>

      <div v-else-if="booking.status === 'cancelled'" class="pt-2 border-t border-tg-section-separator">
        <div class="flex items-center gap-2 text-sm text-red-600">
          <Icon name="XCircle" class="h-4 w-4" />
          Booking cancelled
        </div>
      </div>

      <div v-else-if="booking.status === 'completed'" class="pt-2 border-t border-tg-section-separator">
        <div class="flex items-center gap-2 text-sm text-green-600">
          <Icon name="CheckCircle" class="h-4 w-4" />
          Booking completed
        </div>
      </div>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Icon from '@/components/Icon.vue'
import { Button, Badge, Card, CardContent } from '@/components/ui'

interface Booking {
  id: number
  status: string
  start_datetime: string
  end_datetime: string
  notes?: string
  service: {
    id: number
    title: string
    price: number
  }
  provider: {
    id: number
    business_name: string
    user: {
      name: string
    }
  }
}

interface Props {
  booking: Booking
}

const props = defineProps<Props>()

defineEmits<{
  cancel: [bookingId: number]
}>()

const statusVariant = computed(() => {
  switch (props.booking.status) {
    case 'confirmed': return 'default'
    case 'pending': return 'secondary'
    case 'declined': return 'destructive'
    case 'cancelled': return 'outline'
    case 'completed': return 'default'
    default: return 'secondary'
  }
})

const statusClass = computed(() => {
  return `status-${props.booking.status}`
})

const canCancel = computed(() => {
  return ['pending', 'confirmed'].includes(props.booking.status) && 
         new Date(props.booking.start_datetime) > new Date()
})

function formatDate(datetime: string) {
  return new Date(datetime).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric'
  })
}

function formatTime(datetime: string) {
  return new Date(datetime).toLocaleTimeString('en-US', { 
    hour: '2-digit', 
    minute: '2-digit',
    hour12: false
  })
}

function formatStatus(status: string) {
  return status.replace('_', ' ').toUpperCase()
}
</script>
