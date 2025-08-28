<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input, Label, Textarea } from '@/components/ui'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Building2, Phone, MapPin, FileText, Upload, User, Save } from 'lucide-vue-next'

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

const fileInput = ref<HTMLInputElement>()
const imagePreview = ref<string>()

const form = useForm({
  business_name: props.provider.business_name,
  address: props.provider.address || '',
  contact_phone: props.provider.contact_phone || '',
  description: props.provider.description || '',
  profile_image: null as File | null,
})

const currentImageUrl = computed(() => {
  if (props.provider.profile_image) {
    return `/storage/${props.provider.profile_image}`
  }
  return null
})

const displayImage = computed(() => {
  return imagePreview.value || currentImageUrl.value
})

const handleImageSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    form.profile_image = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const removeImage = () => {
  form.profile_image = null
  imagePreview.value = undefined
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const submit = () => {
  form.put('/provider', {
    forceFormData: true,
  })
}
</script>

<template>
  <Head title="Edit Provider Profile" />

  <TelegramAppLayout>
    <div class="container mx-auto px-4 py-6 max-w-2xl">
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Building2 class="h-5 w-5" />
            Edit Provider Profile
          </CardTitle>
          <CardDescription>
            Update your business profile information
          </CardDescription>
        </CardHeader>
        
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Business Name -->
            <div class="space-y-2">
              <Label for="business_name" class="flex items-center gap-2">
                <Building2 class="h-4 w-4" />
                Business Name *
              </Label>
              <Input
                id="business_name"
                v-model="form.business_name"
                type="text"
                placeholder="Enter your business name"
                :class="{ 'border-red-500': form.errors.business_name }"
                required
              />
              <p v-if="form.errors.business_name" class="text-sm text-red-600">
                {{ form.errors.business_name }}
              </p>
            </div>

            <!-- Address -->
            <div class="space-y-2">
              <Label for="address" class="flex items-center gap-2">
                <MapPin class="h-4 w-4" />
                Address
              </Label>
              <Textarea
                id="address"
                v-model="form.address"
                placeholder="Enter your business address"
                :rows="2"
                :class="{ 'border-red-500': form.errors.address }"
              />
              <p v-if="form.errors.address" class="text-sm text-red-600">
                {{ form.errors.address }}
              </p>
            </div>

            <!-- Contact Phone -->
            <div class="space-y-2">
              <Label for="contact_phone" class="flex items-center gap-2">
                <Phone class="h-4 w-4" />
                Contact Phone
              </Label>
              <Input
                id="contact_phone"
                v-model="form.contact_phone"
                type="tel"
                placeholder="Enter your contact phone number"
                :class="{ 'border-red-500': form.errors.contact_phone }"
              />
              <p v-if="form.errors.contact_phone" class="text-sm text-red-600">
                {{ form.errors.contact_phone }}
              </p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
              <Label for="description" class="flex items-center gap-2">
                <FileText class="h-4 w-4" />
                Description
              </Label>
              <Textarea
                id="description"
                v-model="form.description"
                placeholder="Describe your business and services"
                :rows="4"
                :class="{ 'border-red-500': form.errors.description }"
              />
              <p v-if="form.errors.description" class="text-sm text-red-600">
                {{ form.errors.description }}
              </p>
            </div>

            <!-- Profile Image -->
            <div class="space-y-2">
              <Label class="flex items-center gap-2">
                <User class="h-4 w-4" />
                Profile Image
              </Label>
              
              <div v-if="displayImage" class="space-y-2">
                <div class="relative inline-block">
                  <img 
                    :src="displayImage" 
                    alt="Profile" 
                    class="w-24 h-24 object-cover rounded-lg border"
                  />
                  <Button
                    type="button"
                    variant="destructive"
                    size="sm"
                    class="absolute -top-2 -right-2 h-6 w-6 rounded-full p-0"
                    @click="removeImage"
                  >
                    ×
                  </Button>
                </div>
                <div>
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="fileInput?.click()"
                  >
                    Change Image
                  </Button>
                </div>
              </div>
              
              <div v-else class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <Upload class="mx-auto h-8 w-8 text-gray-400 mb-2" />
                <p class="text-sm text-gray-600 mb-2">Upload a profile image</p>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  @click="fileInput?.click()"
                >
                  Choose File
                </Button>
              </div>
              
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleImageSelect"
              />
              
              <p v-if="form.errors.profile_image" class="text-sm text-red-600">
                {{ form.errors.profile_image }}
              </p>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-3 pt-4">
              <Button
                type="submit"
                class="flex-1"
                :disabled="form.processing"
              >
                <Save class="h-4 w-4 mr-2" />
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </TelegramAppLayout>
</template>
