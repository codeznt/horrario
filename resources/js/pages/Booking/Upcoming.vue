<template>
  <Head title="Upcoming Bookings" />
  
  <MobileAppLayout title="Upcoming Bookings">
    <template #header-actions>
      <div class="flex gap-2">
        <Button @click="router.visit('/bookings')" size="sm" variant="outline">
          <Icon name="Calendar" class="h-4 w-4 mr-1" />
          All
        </Button>
        <Button @click="router.visit('/bookings/past')" size="sm" variant="outline">
          <Icon name="History" class="h-4 w-4 mr-1" />
          Past
        </Button>
        <Button @click="router.visit('/services')" size="sm">
          <Icon name="Plus" class="h-4 w-4 mr-1" />
          Book
        </Button>
      </div>
    </template>

    <div v-if="bookings.length === 0" class="text-center py-12">
      <div class="bg-tg-section-bg rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
        <Icon name="Clock" class="h-12 w-12 text-tg-hint" />
      </div>
      <h3 class="text-xl font-semibold mb-2 text-tg-text">No upcoming bookings</h3>
      <p class="text-tg-subtitle-text mb-6 px-4">
        You don't have any upcoming bookings. Browse our services to book your next appointment.
      </p>
      <Button @click="router.visit('/services')" class="mx-auto">
        <Icon name="Search" class="h-4 w-4 mr-2" />
        Browse Services
      </Button>
    </div>

    <div v-else class="space-y-4">
      <!-- Summary Card -->
      <Card class="bg-tg-section-bg border-tg-section-separator">
        <CardContent class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-semibold text-tg-text">Upcoming Bookings</h3>
              <p class="text-sm text-tg-subtitle-text">Your scheduled appointments</p>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-tg-accent">{{ bookings.length }}</div>
              <div class="text-sm text-tg-subtitle-text">{{ bookings.length === 1 ? 'Booking' : 'Bookings' }}</div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Bookings List -->
      <div class="space-y-3">
        <BookingCard 
          v-for="booking in bookings" 
          :key="booking.id" 
          :booking="booking"
          @cancel="cancelBooking"
        />
      </div>
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
import BookingCard from '../../components/BookingCard.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import Icon from '@/components/Icon.vue'
import { Button, Card, CardContent } from '@/components/ui'

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
  bookings: Booking[]
}

const props = defineProps<Props>()

const showCancelDialog = ref(false)
const bookingToCancel = ref<number | null>(null)

function cancelBooking(bookingId: number) {
  bookingToCancel.value = bookingId
  showCancelDialog.value = true
}

function confirmCancel() {
  if (!bookingToCancel.value) return
  
  router.post(`/bookings/${bookingToCancel.value}/cancel`, {}, {
    onFinish: () => {
      showCancelDialog.value = false
      bookingToCancel.value = null
    }
  })
}
</script>
