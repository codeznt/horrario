<template>
  <Head title="Edit Service" />
  
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-2xl mx-auto">
        <div class="mb-8">
          <Button variant="ghost" @click="router.visit('/services')" class="mb-4">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Back to Services
          </Button>
          <h1 class="text-3xl font-bold text-foreground">Edit Service</h1>
          <p class="text-muted-foreground mt-2">Update your service information</p>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Service Details</CardTitle>
            <CardDescription>
              Update information about your service
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="space-y-2">
                <Label for="title">Service Title *</Label>
                <Input
                  id="title"
                  v-model="form.title"
                  type="text"
                  placeholder="e.g., Hair Cut, Massage Therapy"
                  :class="{ 'border-destructive': errors.title }"
                />
                <p v-if="errors.title" class="text-sm text-destructive">{{ errors.title }}</p>
              </div>

              <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                  id="description"
                  v-model="form.description"
                  placeholder="Describe your service (optional)"
                  rows="3"
                  :class="{ 'border-destructive': errors.description }"
                />
                <p v-if="errors.description" class="text-sm text-destructive">{{ errors.description }}</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="duration_minutes">Duration (minutes) *</Label>
                  <Input
                    id="duration_minutes"
                    v-model.number="form.duration_minutes"
                    type="number"
                    min="5"
                    max="480"
                    placeholder="60"
                    :class="{ 'border-destructive': errors.duration_minutes }"
                  />
                  <p v-if="errors.duration_minutes" class="text-sm text-destructive">{{ errors.duration_minutes }}</p>
                  <p class="text-xs text-muted-foreground">Between 5 and 480 minutes</p>
                </div>

                <div class="space-y-2">
                  <Label for="display_price">Display Price *</Label>
                  <Input
                    id="display_price"
                    v-model="form.display_price"
                    type="text"
                    placeholder="$50 or Contact for pricing"
                    :class="{ 'border-destructive': errors.display_price }"
                  />
                  <p v-if="errors.display_price" class="text-sm text-destructive">{{ errors.display_price }}</p>
                  <p class="text-xs text-muted-foreground">How pricing is displayed to customers</p>
                </div>
              </div>

              <div class="flex gap-4 pt-4">
                <Button type="submit" :disabled="processing" class="flex-1">
                  <Loader2 v-if="processing" class="h-4 w-4 mr-2 animate-spin" />
                  Update Service
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
import { ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { ArrowLeft, Loader2 } from 'lucide-vue-next'

interface Service {
  id: number
  title: string
  description?: string
  duration_minutes: number
  display_price: string
}

const props = defineProps<{
  service: Service
}>()

const form = useForm({
  title: props.service.title,
  description: props.service.description || '',
  duration_minutes: props.service.duration_minutes,
  display_price: props.service.display_price
})

const errors = ref<Record<string, string>>({})
const processing = ref(false)

const submit = () => {
  processing.value = true
  errors.value = {}

  form.put(`/services/${props.service.id}`, {
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
