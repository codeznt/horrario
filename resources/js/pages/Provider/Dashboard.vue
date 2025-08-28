<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Building2, Edit, User, Phone, MapPin, FileText } from 'lucide-vue-next'

interface Provider {
  id: number
  business_name: string
  address: string | null
  contact_phone: string | null
  description: string | null
  profile_image: string | null
}

const props = defineProps<{
  provider: Provider
}>()

const profileImageUrl = props.provider.profile_image 
  ? `/storage/${props.provider.profile_image}` 
  : null
</script>

<template>
  <Head title="Provider Dashboard" />

  <TelegramAppLayout>
    <div class="container mx-auto px-4 py-6 max-w-2xl">
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Building2 class="h-5 w-5" />
            Provider Dashboard
          </CardTitle>
          <CardDescription>
            Manage your business profile and services
          </CardDescription>
        </CardHeader>
        
        <CardContent class="space-y-6">
          <!-- Profile Image -->
          <div v-if="profileImageUrl" class="flex justify-center">
            <img 
              :src="profileImageUrl" 
              :alt="provider.business_name"
              class="w-24 h-24 object-cover rounded-lg border"
            />
          </div>

          <!-- Business Information -->
          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <Building2 class="h-5 w-5 text-muted-foreground" />
              <div>
                <p class="font-medium">{{ provider.business_name }}</p>
                <p class="text-sm text-muted-foreground">Business Name</p>
              </div>
            </div>

            <div v-if="provider.address" class="flex items-center gap-3">
              <MapPin class="h-5 w-5 text-muted-foreground" />
              <div>
                <p class="font-medium">{{ provider.address }}</p>
                <p class="text-sm text-muted-foreground">Address</p>
              </div>
            </div>

            <div v-if="provider.contact_phone" class="flex items-center gap-3">
              <Phone class="h-5 w-5 text-muted-foreground" />
              <div>
                <p class="font-medium">{{ provider.contact_phone }}</p>
                <p class="text-sm text-muted-foreground">Contact Phone</p>
              </div>
            </div>

            <div v-if="provider.description" class="flex items-start gap-3">
              <FileText class="h-5 w-5 text-muted-foreground mt-0.5" />
              <div>
                <p class="font-medium">{{ provider.description }}</p>
                <p class="text-sm text-muted-foreground">Description</p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4">
            <Button 
              as="a" 
              href="/provider/edit"
              class="flex-1"
            >
              <Edit class="h-4 w-4 mr-2" />
              Edit Profile
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </TelegramAppLayout>
</template>
