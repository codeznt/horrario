<template>
  <Head title="Provider Bookings" />
  
  <MobileAppLayout title="Bookings Calendar" :subtitle="formatDate(selectedDate)">
    <template #header-actions>
      <Button @click="router.visit('/provider/dashboard')" size="sm" variant="outline">
        <Icon name="Home" class="h-4 w-4 mr-1" />
        Today
      </Button>
    </template>

    <!-- Date Navigation -->
    <Card class="bg-tg-section-bg border-tg-section-separator mb-4">
      <CardContent class="p-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-tg-text">Select Date</h3>
          <Button @click="goToToday" size="sm" variant="ghost">
            Today
          </Button>
        </div>
        
        <div class="flex items-center gap-2 mb-4">
          <Button @click="previousWeek" size="sm" variant="outline">
            <Icon name="ChevronLeft" class="h-4 w-4" />
          </Button>
          
          <div class="flex-1 text-center">
            <div class="font-medium text-tg-text">{{ formatWeekRange() }}</div>
          </div>
          
          <Button @click="nextWeek" size="sm" variant="outline">
            <Icon name="ChevronRight" class="h-4 w-4" />
          </Button>
        </div>

        <!-- Week Days -->
        <div class="grid grid-cols-7 gap-1">
          <div 
            v-for="day in weekDays" 
            :key="day.date"
            @click="selectDate(day.date)"
            :class="[
              'p-2 text-center rounded cursor-pointer transition-colors',
              day.date === selectedDate 
                ? 'bg-tg-accent text-white' 
                : day.isToday 
                  ? 'bg-tg-section-separator text-tg-text font-medium'
                  : 'text-tg-subtitle-text hover:bg-tg-section-separator'
            ]"
          >
            <div class="text-xs">{{ day.dayName }}</div>
            <div class="text-sm font-medium">{{ day.dayNumber }}</div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Bookings for Selected Date -->
    <div v-if="bookings.length === 0" class="text-center py-12">
      <div class="bg-tg-section-bg rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
        <Icon name="Calendar" class="h-12 w-12 text-tg-hint" />
      </div>
      <h3 class="text-xl font-semibold mb-2 text-tg-text">No bookings</h3>
      <p class="text-tg-subtitle-text mb-6 px-4">
        You don't have any bookings scheduled for {{ formatDate(selectedDate) }}.
      </p>
    </div>

    <div v-else class="space-y-4">
      <!-- Summary Card -->
      <Card class="bg-tg-section-bg border-tg-section-separator">
        <CardContent class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-semibold text-tg-text">{{ formatDate(selectedDate) }}</h3>
              <p class="text-sm text-tg-subtitle-text">{{ bookings.length }} {{ bookings.length === 1 ? 'booking' : 'bookings' }}</p>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-tg-accent">{{ bookings.length }}</div>
              <div class="text-sm text-tg-subtitle-text">Total</div>
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
import { ref, computed, watch } from 'vue'
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

const selectedDate = ref(props.date)
const currentWeekStart = ref(getWeekStart(new Date(props.date)))
const showConfirmDialog = ref(false)
const showDeclineDialog = ref(false)
const selectedBookingId = ref<number | null>(null)

const weekDays = computed(() => {
  const days = []
  const today = new Date().toISOString().split('T')[0]
  
  for (let i = 0; i < 7; i++) {
    const date = new Date(currentWeekStart.value)
    date.setDate(date.getDate() + i)
    const dateString = date.toISOString().split('T')[0]
    
    days.push({
      date: dateString,
      dayName: date.toLocaleDateString('en-US', { weekday: 'short' }),
      dayNumber: date.getDate(),
      isToday: dateString === today
    })
  }
  
  return days
})

function getWeekStart(date: Date) {
  const d = new Date(date)
  const day = d.getDay()
  const diff = d.getDate() - day + (day === 0 ? -6 : 1) // Monday as first day
  return new Date(d.setDate(diff))
}

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function formatWeekRange() {
  const start = new Date(currentWeekStart.value)
  const end = new Date(start)
  end.setDate(end.getDate() + 6)
  
  const startMonth = start.toLocaleDateString('en-US', { month: 'short' })
  const endMonth = end.toLocaleDateString('en-US', { month: 'short' })
  
  if (start.getMonth() === end.getMonth()) {
    return `${startMonth} ${start.getDate()} - ${end.getDate()}, ${start.getFullYear()}`
  } else {
    return `${startMonth} ${start.getDate()} - ${endMonth} ${end.getDate()}, ${start.getFullYear()}`
  }
}

function selectDate(date: string) {
  selectedDate.value = date
  router.get('/provider/bookings', { date }, {
    preserveState: true,
    preserveScroll: true
  })
}

function previousWeek() {
  const newStart = new Date(currentWeekStart.value)
  newStart.setDate(newStart.getDate() - 7)
  currentWeekStart.value = newStart
}

function nextWeek() {
  const newStart = new Date(currentWeekStart.value)
  newStart.setDate(newStart.getDate() + 7)
  currentWeekStart.value = newStart
}

function goToToday() {
  const today = new Date().toISOString().split('T')[0]
  currentWeekStart.value = getWeekStart(new Date())
  selectDate(today)
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

// Watch for date changes in props
watch(() => props.date, (newDate) => {
  selectedDate.value = newDate
  currentWeekStart.value = getWeekStart(new Date(newDate))
})
</script>
