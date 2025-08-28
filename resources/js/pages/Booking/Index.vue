<template>
  <Head title="My Bookings" />
  
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-foreground">My Bookings</h1>
          <p class="text-muted-foreground mt-2">View and manage your appointments</p>
        </div>
        <Button @click="router.visit('/services')" class="flex items-center gap-2">
          <Plus class="h-4 w-4" />
          Book Service
        </Button>
      </div>

      <div v-if="bookings.length === 0" class="text-center py-12">
        <div class="mx-auto w-24 h-24 bg-muted rounded-full flex items-center justify-center mb-4">
          <Calendar class="h-12 w-12 text-muted-foreground" />
        </div>
        <h3 class="text-lg font-semibold text-foreground mb-2">No bookings yet</h3>
        <p class="text-muted-foreground mb-4">Book your first service to get started</p>
        <Button @click="router.visit('/services')">
          Browse Services
        </Button>
      </div>

      <div v-else class="space-y-4">
        <Card v-for="booking in bookings" :key="booking.id" class="hover:shadow-lg transition-shadow">
          <CardContent class="p-6">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-lg font-semibold">{{ booking.service.title }}</h3>
                  <Badge :variant="getStatusVariant(booking.status)">
                    {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                  </Badge>
                </div>
                
                <div class="space-y-2 text-sm text-muted-foreground">
                  <div class="flex items-center gap-2">
                    <Calendar class="h-4 w-4" />
                    {{ formatDate(booking.start_datetime) }}
                  </div>
                  <div class="flex items-center gap-2">
                    <Clock class="h-4 w-4" />
                    {{ formatTime(booking.start_datetime) }} - {{ formatTime(booking.end_datetime) }}
                  </div>
                  <div class="flex items-center gap-2">
                    <User class="h-4 w-4" />
                    {{ booking.provider.business_name }}
                  </div>
                  <div class="flex items-center gap-2">
                    <DollarSign class="h-4 w-4" />
                    {{ booking.service.display_price }}
                  </div>
                </div>

                <div v-if="booking.notes" class="mt-3 p-2 bg-muted rounded text-sm">
                  <strong>Notes:</strong> {{ booking.notes }}
                </div>
              </div>

              <div class="flex flex-col gap-2 ml-4">
                <Button 
                  variant="outline" 
                  size="sm" 
                  @click="router.visit(`/bookings/${booking.id}`)"
                  class="flex items-center gap-1"
                >
                  <Eye class="h-3 w-3" />
                  View
                </Button>
                
                <Button 
                  v-if="canCancelBooking(booking)"
                  variant="destructive" 
                  size="sm" 
                  @click="cancelBooking(booking)"
                  class="flex items-center gap-1"
                >
                  <X class="h-3 w-3" />
                  Cancel
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>

  <!-- Cancel Confirmation Dialog -->
  <AlertDialog :open="showCancelDialog" @update:open="showCancelDialog = $event">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Cancel Booking</AlertDialogTitle>
        <AlertDialogDescription>
          Are you sure you want to cancel "{{ bookingToCancel?.service.title }}"? This action cannot be undone.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>Keep Booking</AlertDialogCancel>
        <AlertDialogAction @click="confirmCancel" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
          Cancel Booking
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent } from '@/components/ui/card'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Plus, Calendar, Clock, User, DollarSign, Eye, X } from 'lucide-vue-next'

interface Booking {
  id: number
  start_datetime: string
  end_datetime: string
  status: string
  notes?: string
  service: {
    id: number
    title: string
    display_price: string
  }
  provider: {
    id: number
    business_name: string
  }
}

const props = defineProps<{
  bookings: Booking[]
}>()

const showCancelDialog = ref(false)
const bookingToCancel = ref<Booking | null>(null)

const getStatusVariant = (status: string) => {
  switch (status) {
    case 'pending':
      return 'secondary'
    case 'confirmed':
      return 'default'
    case 'completed':
      return 'default'
    case 'cancelled':
      return 'destructive'
    default:
      return 'secondary'
  }
}

const canCancelBooking = (booking: Booking) => {
  return ['pending', 'confirmed'].includes(booking.status)
}

const formatDate = (datetime: string) => {
  return new Date(datetime).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTime = (datetime: string) => {
  return new Date(datetime).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

const cancelBooking = (booking: Booking) => {
  bookingToCancel.value = booking
  showCancelDialog.value = true
}

const confirmCancel = () => {
  if (bookingToCancel.value) {
    router.post(`/bookings/${bookingToCancel.value.id}/cancel`, {}, {
      onSuccess: () => {
        showCancelDialog.value = false
        bookingToCancel.value = null
      }
    })
  }
}
</script>
