<template>
  <Head title="Services" />
  
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-foreground">My Services</h1>
          <p class="text-muted-foreground mt-2">Manage your service offerings</p>
        </div>
        <Button @click="router.visit('/services/create')" class="flex items-center gap-2">
          <Plus class="h-4 w-4" />
          Add Service
        </Button>
      </div>

      <div v-if="services.length === 0" class="text-center py-12">
        <div class="mx-auto w-24 h-24 bg-muted rounded-full flex items-center justify-center mb-4">
          <Package class="h-12 w-12 text-muted-foreground" />
        </div>
        <h3 class="text-lg font-semibold text-foreground mb-2">No services yet</h3>
        <p class="text-muted-foreground mb-4">Create your first service to start accepting bookings</p>
        <Button @click="router.visit('/services/create')">
          Create Service
        </Button>
      </div>

      <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <Card v-for="service in services" :key="service.id" class="hover:shadow-lg transition-shadow">
          <CardHeader>
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <CardTitle class="text-lg">{{ service.title }}</CardTitle>
                <CardDescription v-if="service.description" class="mt-1">
                  {{ service.description }}
                </CardDescription>
              </div>
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <Button variant="ghost" size="sm">
                    <MoreVertical class="h-4 w-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuItem @click="editService(service.id)">
                    <Edit class="h-4 w-4 mr-2" />
                    Edit
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="deleteService(service)" class="text-destructive">
                    <Trash2 class="h-4 w-4 mr-2" />
                    Delete
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </CardHeader>
          <CardContent>
            <div class="space-y-2">
              <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <Clock class="h-4 w-4" />
                {{ service.duration_minutes }} minutes
              </div>
              <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                <DollarSign class="h-4 w-4" />
                {{ service.display_price }}
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Dialog -->
  <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Delete Service</AlertDialogTitle>
        <AlertDialogDescription>
          Are you sure you want to delete "{{ serviceToDelete?.title }}"? This action cannot be undone.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>Cancel</AlertDialogCancel>
        <AlertDialogAction @click="confirmDelete" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
          Delete
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { 
  DropdownMenu, 
  DropdownMenuContent, 
  DropdownMenuItem, 
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu'
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
import { Plus, Package, MoreVertical, Edit, Trash2, Clock, DollarSign } from 'lucide-vue-next'

interface Service {
  id: number
  title: string
  description?: string
  duration_minutes: number
  display_price: string
  created_at: string
  updated_at: string
}

const props = defineProps<{
  services: Service[]
}>()

const showDeleteDialog = ref(false)
const serviceToDelete = ref<Service | null>(null)

const editService = (serviceId: number) => {
  router.visit(`/services/${serviceId}/edit`)
}

const deleteService = (service: Service) => {
  serviceToDelete.value = service
  showDeleteDialog.value = true
}

const confirmDelete = () => {
  if (serviceToDelete.value) {
    router.delete(`/services/${serviceToDelete.value.id}`, {
      onSuccess: () => {
        showDeleteDialog.value = false
        serviceToDelete.value = null
      }
    })
  }
}
</script>
