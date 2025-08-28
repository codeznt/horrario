<template>
  <Head title="Book Service" />
  
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <Button variant="ghost" @click="router.visit('/services')" class="mb-4">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Back to Services
          </Button>
          <div class="flex items-start gap-6">
            <div class="flex-1">
              <h1 class="text-3xl font-bold text-foreground">Book {{ service.title }}</h1>
              <p class="text-muted-foreground mt-2">{{ service.description }}</p>
              <div class="flex items-center gap-4 mt-4">
                <div class="flex items-center gap-2 text-sm">
                  <Clock class="h-4 w-4" />
                  {{ service.duration_minutes }} minutes
                </div>
                <div class="flex items-center gap-2 text-sm font-semibold">
                  <DollarSign class="h-4 w-4" />
                  {{ service.display_price }}
                </div>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm text-muted-foreground">Provider</p>
              <p class="font-semibold">{{ provider.business_name }}</p>
              <p class="text-sm text-muted-foreground">{{ provider.user.name }}</p>
            </div>
          </div>
        </div>

        <!-- Booking Form -->
        <Card>
          <CardHeader>
            <CardTitle>Select Date & Time</CardTitle>
            <CardDescription>
              Choose an available time slot for your appointment
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submitBooking" class="space-y-6">
              <!-- Date Selection -->
              <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                <div 
                  v-for="date in dates" 
                  :key="date.date"
                  class="border rounded-lg p-4 cursor-pointer transition-colors"
                  :class="{
                    'border-primary bg-primary/10': selectedDate === date.date,
                    'border-border hover:border-primary/50': selectedDate !== date.date,
                    'opacity-50 cursor-not-allowed': date.slots.length === 0
                  }"
                  @click="selectDate(date)"
                >
                  <div class="text-center">
                    <div class="text-sm text-muted-foreground">{{ date.day_short }}</div>
                    <div class="text-lg font-semibold">{{ date.day_number }}</div>
                    <div class="text-xs text-muted-foreground">{{ date.month }}</div>
                    <div class="text-xs mt-1">
                      {{ date.slots.length }} slot{{ date.slots.length !== 1 ? 's' : '' }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Time Slot Selection -->
              <div v-if="selectedDate && selectedDateData" class="space-y-4">
                <div>
                  <Label class="text-base font-medium">Available Times for {{ selectedDateData.day_name }}</Label>
                </div>
                
                <div v-if="selectedDateData.slots.length === 0" class="text-center py-8 text-muted-foreground">
                  <Clock class="h-8 w-8 mx-auto mb-2 opacity-50" />
                  <p>No available time slots for this date</p>
                </div>
                
                <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                  <button
                    v-for="slot in selectedDateData.slots"
                    :key="slot.start"
                    type="button"
                    class="p-3 text-sm border rounded-lg transition-colors"
                    :class="{
                      'border-primary bg-primary text-primary-foreground': selectedTime === slot.start,
                      'border-border hover:border-primary/50 hover:bg-primary/10': selectedTime !== slot.start
                    }"
                    @click="selectedTime = slot.start"
                  >
                    {{ formatTime(slot.start) }}
                  </button>
                </div>
              </div>

              <!-- Notes -->
              <div class="space-y-2">
                <Label for="notes">Additional Notes (Optional)</Label>
                <Textarea
                  id="notes"
                  v-model="form.notes"
                  placeholder="Any special requests or information for the provider..."
                  rows="3"
                  :class="{ 'border-destructive': errors.notes }"
                />
                <p v-if="errors.notes" class="text-sm text-destructive">{{ errors.notes }}</p>
              </div>

              <!-- Error Messages -->
              <div v-if="errors.start_time || errors.date || errors.error" class="space-y-2">
                <p v-if="errors.start_time" class="text-sm text-destructive">{{ errors.start_time }}</p>
                <p v-if="errors.date" class="text-sm text-destructive">{{ errors.date }}</p>
                <p v-if="errors.error" class="text-sm text-destructive">{{ errors.error }}</p>
              </div>

              <!-- Submit Button -->
              <div class="flex gap-4 pt-4">
                <Button 
                  type="submit" 
                  :disabled="!selectedDate || !selectedTime || processing" 
                  class="flex-1"
                >
                  <Loader2 v-if="processing" class="h-4 w-4 mr-2 animate-spin" />
                  Book Appointment
                </Button>
                <Button type="button" variant="outline" @click="router.visit('/services')">
                  Cancel
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { ArrowLeft, Clock, DollarSign, Loader2 } from 'lucide-vue-next'

interface Service {
  id: number
  title: string
  description?: string
  duration_minutes: number
  display_price: string
  provider: Provider
}

interface Provider {
  id: number
  business_name: string
  user: {
    name: string
  }
}

interface TimeSlot {
  start: string
  end: string
  datetime: string
  available: boolean
}

interface DateSlot {
  date: string
  day_name: string
  day_short: string
  day_number: string
  month: string
  slots: TimeSlot[]
}

const props = defineProps<{
  service: Service
  provider: Provider
  dates: DateSlot[]
}>()

const selectedDate = ref<string | null>(null)
const selectedTime = ref<string | null>(null)
const processing = ref(false)
const errors = ref<Record<string, string>>({})

const form = useForm({
  service_id: props.service.id,
  date: '',
  start_time: '',
  notes: ''
})

const selectedDateData = computed(() => {
  return selectedDate.value ? props.dates.find(d => d.date === selectedDate.value) : null
})

const selectDate = (date: DateSlot) => {
  if (date.slots.length === 0) return
  
  selectedDate.value = date.date
  selectedTime.value = null
  form.date = date.date
}

const formatTime = (time: string) => {
  return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

const submitBooking = () => {
  if (!selectedDate.value || !selectedTime.value) return

  processing.value = true
  errors.value = {}

  form.date = selectedDate.value
  form.start_time = selectedTime.value

  form.post('/bookings', {
    onSuccess: () => {
      // Redirect handled by controller
    },
    onError: (formErrors) => {
      errors.value = formErrors
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>
