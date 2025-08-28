<template>
  <Head title="My Bookings" />
  
  <MobileAppLayout title="My Bookings">
    <template #header-actions>
      <Button @click="router.visit('/services')" size="sm">
        <Icon name="Plus" class="h-4 w-4 mr-1" />
        Book
      </Button>
    </template>

    <div v-if="bookings.length === 0" class="text-center py-12">
      <div class="bg-tg-section-bg rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
        <Icon name="Calendar" class="h-12 w-12 text-tg-hint" />
      </div>
      <h3 class="text-xl font-semibold mb-2 text-tg-text">No bookings yet</h3>
      <p class="text-tg-subtitle-text mb-6 px-4">
        You haven't made any bookings. Browse our services to get started.
      </p>
      <Button @click="router.visit('/services')" class="mx-auto">
        <Icon name="Search" class="h-4 w-4 mr-2" />
        Browse Services
      </Button>
    </div>

    <div v-else class="space-y-4">
      <Card v-for="booking in bookings" :key="booking.id" class="bg-tg-section-bg border-tg-section-separator">
        <CardContent class="p-4">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <h3 class="font-semibold text-tg-text">{{ booking.service.title }}</h3>
                <Badge :variant="getStatusVariant(booking.status)" class="text-xs">
                  {{ formatStatus(booking.status) }}
                </Badge>
              </div>
              
              <div class="space-y-1 text-sm">
                <div class="flex items-center gap-2 text-tg-subtitle-text">
                  <Icon name="Calendar" class="h-3 w-3" />
                  {{ formatDate(booking.date) }}
                </div>
                <div class="flex items-center gap-2 text-tg-subtitle-text">
                  <Icon name="Clock" class="h-3 w-3" />
                  {{ booking.start_time }} - {{ booking.end_time }}
                </div>
                <div class="flex items-center gap-2 text-tg-subtitle-text">
                  <Icon name="MapPin" class="h-3 w-3" />
                  {{ booking.provider?.business_name || 'Provider' }}
                </div>
              </div>
            </div>

            <div class="text-right">
              <div class="font-semibold text-tg-accent">{{ booking.service.display_price }}</div>
            </div>
          </div>

          <div v-if="booking.notes" class="mb-3 p-2 bg-tg-bg rounded text-sm text-tg-subtitle-text">
            {{ booking.notes }}
          </div>

          <div class="flex gap-2 pt-2 border-t border-tg-section-separator">
            <Button
              v-if="canCancelBooking(booking)"
              variant="outline"
              size="sm"
              @click="cancelBooking(booking.id)"
              :disabled="cancellingBookings.includes(booking.id)"
              class="flex-1"
            >
              <Icon v-if="!cancellingBookings.includes(booking.id)" name="X" class="h-3 w-3 mr-1" />
              <Spinner v-else size="sm" class="mr-1" />
              Cancel
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
        </CardContent>
      </Card>
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmDialog
      :show="showCancelDialog"
      title="Cancel Booking"
      message="Are you sure you want to cancel this booking? This action cannot be undone."
      variant="destructive"
      @confirm="confirmCancel"
      @cancel="showCancelDialog = false"
    />
  </MobileAppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import MobileAppLayout from '@/layouts/MobileAppLayout.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import Icon from '@/components/Icon.vue'
import { Button, Badge, Card, CardContent, Spinner } from '@/components/ui'

interface Booking {
  id: number
  status: string
  date: string
  start_time: string
  end_time: string
  notes?: string
  service: {
    id: number
    title: string
    display_price: string
  }
  provider?: {
    business_name: string
  }
}

interface Props {
  bookings: Booking[]
}

const props = defineProps<Props>()

const showCancelDialog = ref(false)
const cancellingBookings = ref<number[]>([])
const bookingToCancel = ref<number | null>(null)

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function formatStatus(status: string) {
  return status.replace('_', ' ').toUpperCase()
}

function getStatusVariant(status: string) {
  switch (status) {
    case 'confirmed': return 'default'
    case 'pending': return 'secondary'
    case 'cancelled': return 'destructive'
    case 'completed': return 'default'
    default: return 'secondary'
  }
}

function canCancelBooking(booking: Booking) {
  return booking.status === 'confirmed' || booking.status === 'pending'
}

function cancelBooking(bookingId: number) {
  bookingToCancel.value = bookingId
  showCancelDialog.value = true
}

function confirmCancel() {
  if (!bookingToCancel.value) return
  
  cancellingBookings.value.push(bookingToCancel.value)
  
  router.patch(`/bookings/${bookingToCancel.value}/cancel`, {}, {
    onSuccess: () => {
      // Remove from cancelling list
      const index = cancellingBookings.value.indexOf(bookingToCancel.value!)
      if (index > -1) {
        cancellingBookings.value.splice(index, 1)
      }
    },
    onError: () => {
      // Remove from cancelling list on error
      const index = cancellingBookings.value.indexOf(bookingToCancel.value!)
      if (index > -1) {
        cancellingBookings.value.splice(index, 1)
      }
    },
    onFinish: () => {
      showCancelDialog.value = false
      bookingToCancel.value = null
    }
  })
}
</script>
