<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <!-- Service Selection -->
    <FormField 
      id="service_id" 
      label="Service" 
      :error="errors.service_id"
      required
    >
      <Select v-model="form.service_id" @update:model-value="clearError('service_id')">
        <SelectTrigger>
          <SelectValue placeholder="Select a service" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem 
            v-for="service in services" 
            :key="service.id" 
            :value="service.id.toString()"
          >
            <div class="flex justify-between items-center w-full">
              <span>{{ service.title }}</span>
              <Badge variant="secondary" class="ml-2">{{ service.display_price }}</Badge>
            </div>
          </SelectItem>
        </SelectContent>
      </Select>
    </FormField>

    <!-- Date Selection -->
    <FormField 
      id="date" 
      label="Date" 
      :error="errors.date"
      required
    >
      <div class="grid grid-cols-2 gap-2">
        <Button
          v-for="dateOption in availableDates"
          :key="dateOption.date"
          type="button"
          :variant="form.date === dateOption.date ? 'default' : 'outline'"
          @click="selectDate(dateOption.date)"
          class="h-auto p-3 flex flex-col items-start"
        >
          <span class="font-medium">{{ dateOption.dayName }}</span>
          <span class="text-sm opacity-75">{{ dateOption.formatted }}</span>
        </Button>
      </div>
    </FormField>

    <!-- Time Slot Selection -->
    <FormField 
      v-if="form.date && availableSlots.length > 0"
      id="start_time" 
      label="Time Slot" 
      :error="errors.start_time"
      required
    >
      <div class="grid grid-cols-3 gap-2">
        <Button
          v-for="slot in availableSlots"
          :key="slot.start"
          type="button"
          :variant="form.start_time === slot.start ? 'default' : 'outline'"
          @click="selectTimeSlot(slot.start)"
          :disabled="!slot.available"
          class="text-sm"
        >
          {{ slot.start }}
        </Button>
      </div>
      <p v-if="availableSlots.length === 0" class="text-sm text-tg-hint mt-2">
        No available time slots for this date
      </p>
    </FormField>

    <!-- Notes -->
    <FormField 
      id="notes" 
      label="Notes (Optional)" 
      :error="errors.notes"
      hint="Any special requests or information for the provider"
    >
      <Textarea 
        v-model="form.notes" 
        placeholder="Enter any additional notes..."
        :rows="3"
        @input="clearError('notes')"
      />
    </FormField>

    <!-- Summary Card -->
    <Card v-if="selectedService && form.date && form.start_time" class="bg-tg-section-bg">
      <CardHeader>
        <CardTitle class="text-base">Booking Summary</CardTitle>
      </CardHeader>
      <CardContent class="space-y-2">
        <div class="flex justify-between">
          <span class="text-tg-subtitle-text">Service:</span>
          <span class="font-medium">{{ selectedService.title }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-tg-subtitle-text">Date:</span>
          <span class="font-medium">{{ formatSelectedDate }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-tg-subtitle-text">Time:</span>
          <span class="font-medium">{{ form.start_time }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-tg-subtitle-text">Duration:</span>
          <span class="font-medium">{{ selectedService.duration_minutes }} minutes</span>
        </div>
        <div class="flex justify-between border-t border-tg-section-separator pt-2">
          <span class="font-medium">Total:</span>
          <span class="font-bold text-tg-accent">{{ selectedService.display_price }}</span>
        </div>
      </CardContent>
    </Card>

    <!-- Submit Button -->
    <div class="pt-4">
      <Button 
        type="submit" 
        :disabled="processing || !canSubmit"
        class="w-full"
        size="lg"
      >
        <Spinner v-if="processing" size="sm" class="mr-2" />
        <Icon v-else name="Calendar" class="mr-2 h-4 w-4" />
        {{ processing ? 'Creating Booking...' : 'Book Appointment' }}
      </Button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useFormValidation } from '@/composables/useFormValidation'
import FormField from '@/components/FormField.vue'
import Icon from '@/components/Icon.vue'
import { 
  Button, 
  Select, 
  SelectContent, 
  SelectItem, 
  SelectTrigger, 
  SelectValue,
  Textarea,
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  Badge,
  Spinner
} from '@/components/ui'

interface Service {
  id: number
  title: string
  duration_minutes: number
  display_price: string
}

interface DateOption {
  date: string
  dayName: string
  formatted: string
}

interface TimeSlot {
  start: string
  available: boolean
}

interface Props {
  services: Service[]
  availableDates: DateOption[]
  serviceId?: number
}

const props = defineProps<Props>()

const emit = defineEmits<{
  submit: [data: any]
}>()

const { form, errors, processing, validate, clearError } = useFormValidation({
  service_id: props.serviceId?.toString() || '',
  date: '',
  start_time: '',
  notes: '',
})

const selectedService = computed(() => {
  if (!form.value.service_id) return null
  return props.services.find(s => s.id.toString() === form.value.service_id)
})

const availableSlots = computed<TimeSlot[]>(() => {
  if (!form.value.date || !selectedService.value) return []
  
  // Mock time slots - in real app, this would come from the backend
  const slots = [
    '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
    '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'
  ]
  
  return slots.map(time => ({
    start: time,
    available: Math.random() > 0.3 // Mock availability
  }))
})

const formatSelectedDate = computed(() => {
  if (!form.value.date) return ''
  const dateOption = props.availableDates.find(d => d.date === form.value.date)
  return dateOption ? `${dateOption.dayName}, ${dateOption.formatted}` : ''
})

const canSubmit = computed(() => {
  return form.value.service_id && 
         form.value.date && 
         form.value.start_time && 
         !processing.value
})

function selectDate(date: string) {
  form.value.date = date
  form.value.start_time = '' // Reset time when date changes
  clearError('date')
}

function selectTimeSlot(time: string) {
  form.value.start_time = time
  clearError('start_time')
}

function handleSubmit() {
  const isValid = validate({
    service_id: { required: 'Please select a service' },
    date: { required: 'Please select a date' },
    start_time: { required: 'Please select a time slot' },
    notes: { maxLength: 500 }
  })

  if (!isValid) return

  processing.value = true
  
  router.post('/bookings', form.value, {
    onSuccess: () => {
      // Success handled by redirect
    },
    onError: (errors) => {
      Object.keys(errors).forEach(key => {
        if (errors[key]) {
          clearError(key)
        }
      })
    },
    onFinish: () => {
      processing.value = false
    }
  })
}

// Watch for service changes to update form
watch(() => props.serviceId, (newServiceId) => {
  if (newServiceId) {
    form.value.service_id = newServiceId.toString()
  }
})
</script>
