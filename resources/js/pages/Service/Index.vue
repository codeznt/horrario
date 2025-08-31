<template>
    <Head :title="t('app.services')" />

    <AppLayoutWithNavigation>
        <div class="min-h-full bg-tg-bg">
        <div class="container mx-auto px-4 py-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-foreground">{{ t('app.my_services') }}</h1>
                    <p class="mt-2 text-muted-foreground">{{ t('app.manage_services') }}</p>
                </div>
                <Button @click="router.visit('/services/create')" class="flex items-center gap-2">
                    <Plus class="h-4 w-4" />
                    {{ t('app.add_service') }}
                </Button>
            </div>

            <div v-if="services.length === 0" class="py-12 text-center">
                <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-muted">
                    <Package class="h-12 w-12 text-muted-foreground" />
                </div>
                <h3 class="mb-2 text-lg font-semibold text-foreground">{{ t('app.no_services') }}</h3>
                <p class="mb-4 text-muted-foreground">{{ t('app.create_first_service') }}</p>
                <Button @click="router.visit('/services/create')">
                    {{ t('app.create_service') }}
                </Button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="service in services" :key="service.id" class="transition-shadow hover:shadow-lg">
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
                                        <Edit class="mr-2 h-4 w-4" />
                                        {{ t('edit') }}
                                    </DropdownMenuItem>
                                    <DropdownMenuItem @click="deleteService(service)" class="text-destructive">
                                        <Trash2 class="mr-2 h-4 w-4" />
                                        {{ t('delete') }}
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Clock class="h-4 w-4" />
                                {{ service.duration_minutes }} {{ t('minutes') }}
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
    </AppLayoutWithNavigation>

    <!-- Delete Confirmation Dialog -->
    <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ t('app.delete_service') }}</AlertDialogTitle>
                <AlertDialogDescription>
                    {{ t('app.delete_service_confirmation', { name: serviceToDelete?.title }) }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>{{ t('app.cancel') }}</AlertDialogCancel>
                <AlertDialogAction @click="confirmDelete" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
                    {{ t('app.delete') }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, router } from '@inertiajs/vue3';
import { Clock, DollarSign, Edit, MoreVertical, Package, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const { t } = useTranslations();

interface Service {
    id: number;
    title: string;
    description?: string;
    duration_minutes: number;
    display_price: string;
    created_at: string;
    updated_at: string;
}

defineProps<{
    services: Service[];
}>();

const showDeleteDialog = ref(false);
const serviceToDelete = ref<Service | null>(null);

const editService = (serviceId: number) => {
    router.visit(`/services/${serviceId}/edit`);
};

const deleteService = (service: Service) => {
    serviceToDelete.value = service;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    if (serviceToDelete.value) {
        router.delete(`/services/${serviceToDelete.value.id}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                serviceToDelete.value = null;
            },
        });
    }
};
</script>
