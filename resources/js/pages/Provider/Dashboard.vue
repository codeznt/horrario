<template>
  <Head title="Provider Dashboard" />
  
  <MobileAppLayout title="Today's Bookings" :subtitle="formatDate(date)">
    <template #header-actions>
      <Button @click="router.visit('/provider/bookings')" size="sm" variant="outline">
        <Icon name="Calendar" class="h-4 w-4 mr-1" />
        Calendar
      </Button>
    </template>

    <div v-if="bookings.length === 0" class="text-center py-12">
      <div class="bg-tg-section-bg rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
        <Icon name="Calendar" class="h-12 w-12 text-tg-hint" />
      </div>
      <h3 class="text-xl font-semibold mb-2 text-tg-text">No bookings today</h3>
      <p class="text-tg-subtitle-text mb-6 px-4">
        You don't have any bookings scheduled for today.
      </p>
    </div>

    <div v-else class="space-y-4">
      <!-- Summary Card -->
      <Card class="bg-tg-section-bg border-tg-section-separator">
        <CardContent class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-semibold text-tg-text">Today's Summary</h3>
              <p class="text-sm text-tg-subtitle-text">{{ formatDate(date) }}</p>
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
        <BookingItem 
          v-for="booking in bookings" 
          :key="booking.id" 
          :booking="booking"
          @confirm="confirmBooking"
          @decline="declineBooking"
        />
      </div>
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmDialog
      :show="showConfirmDialog"
      title="Confirm Booking"
      message="Are you sure you want to confirm this booking?"
      @confirm="handleConfirm"
      @cancel="showConfirmDialog = false"
    />

    <!-- Decline Dialog -->
    <ConfirmDialog
      :show="showDeclineDialog"
      title="Decline Booking"
      message="Are you sure you want to decline this booking? The customer will be notified."
      variant="destructive"
      @confirm="handleDecline"
      @cancel="showDeclineDialog = false"
    />
  </MobileAppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import MobileAppLayout from '@/layouts/MobileAppLayout.vue'
import BookingItem from '@/components/BookingItem.vue'
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
    duration_minutes: number
    price: number
  }
  user: {
    id: number
    name: string
    email: string
  }
}

interface Props {
  bookings: Booking[]
  date: string
}

const props = defineProps<Props>()

const showConfirmDialog = ref(false)
const showDeclineDialog = ref(false)
const selectedBookingId = ref<number | null>(null)

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function confirmBooking(bookingId: number) {
  selectedBookingId.value = bookingId
  showConfirmDialog.value = true
}

function declineBooking(bookingId: number) {
  selectedBookingId.value = bookingId
  showDeclineDialog.value = true
}

function handleConfirm() {
  if (!selectedBookingId.value) return
  
  router.post(`/bookings/${selectedBookingId.value}/confirm`, {}, {
    onFinish: () => {
      showConfirmDialog.value = false
      selectedBookingId.value = null
    }
  })
}

function handleDecline() {
  if (!selectedBookingId.value) return
  
  router.post(`/bookings/${selectedBookingId.value}/decline`, {}, {
    onFinish: () => {
      showDeclineDialog.value = false
      selectedBookingId.value = null
    }
  })
}
</script>
