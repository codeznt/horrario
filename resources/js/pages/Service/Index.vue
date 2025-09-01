<template>
    <Head :title="t('app.services')" />

    <AppLayoutWithNavigation>
        <div class="min-h-full bg-tg-bg">
            <!-- Enhanced Header with Gradient Background -->
            <div class="relative overflow-hidden bg-gradient-to-r from-tg-accent to-purple-600 px-4 py-6">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjAuNSIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+')] opacity-10"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ t('app.my_services') }}</h1>
                        <p class="mt-1 text-sm text-white/90">{{ t('app.manage_services') }}</p>
                    </div>
                    <Button @click="router.visit('/services/create')" class="flex items-center gap-2 bg-white text-tg-accent hover:bg-white/90 shadow-lg">
                        <Icon name="Plus" class="h-4 w-4" />
                        {{ t('app.add_service') }}
                    </Button>
                </div>
            </div>

            <div class="container mx-auto px-4 py-6">
                <div v-if="services.length === 0" class="py-12 text-center">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-tg-accent/10">
                        <Icon name="Package" class="h-12 w-12 text-tg-accent" />
                    </div>
                    <h3 class="mb-3 text-xl font-bold text-tg-text">{{ t('app.no_services') }}</h3>
                    <p class="mb-6 text-tg-hint max-w-md mx-auto">{{ t('app.create_first_service') }}</p>
                    <Button @click="router.visit('/services/create')" class="bg-tg-accent text-white hover:bg-tg-accent/90 px-6 py-3 rounded-full">
                        <Icon name="Plus" class="mr-2 h-4 w-4" />
                        {{ t('app.create_service') }}
                    </Button>
                </div>

                <div v-else class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <Card 
                        v-for="service in services" 
                        :key="service.id" 
                        class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5"
                    >
                        <CardHeader class="pb-3">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <CardTitle class="text-lg font-semibold text-tg-text">{{ service.title }}</CardTitle>
                                    <CardDescription v-if="service.description" class="mt-2 text-sm text-tg-hint line-clamp-2">
                                        {{ service.description }}
                                    </CardDescription>
                                </div>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="sm" class="h-8 w-8 p-0 hover:bg-tg-section-separator/20">
                                            <Icon name="MoreVertical" class="h-4 w-4 text-tg-hint" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="border-tg-section-separator bg-white shadow-lg">
                                        <DropdownMenuItem @click="editService(service.id)" class="cursor-pointer hover:bg-tg-section-separator/20">
                                            <Icon name="Edit" class="mr-2 h-4 w-4 text-tg-hint" />
                                            {{ t('edit') }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="deleteService(service)" class="cursor-pointer text-red-600 hover:bg-red-500/10 hover:text-red-700">
                                            <Icon name="Trash2" class="mr-2 h-4 w-4" />
                                            {{ t('delete') }}
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 text-sm">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-tg-accent/10">
                                        <Icon name="Clock" class="h-4 w-4 text-tg-accent" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-tg-text">{{ service.duration_minutes }} {{ t('minutes') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500/10">
                                        <Icon name="DollarSign" class="h-4 w-4 text-green-600" />
                                    </div>
                                    <div class="text-lg font-bold text-tg-text">{{ service.display_price }}</div>
                                </div>
                            </div>
                        </CardContent>
                        <div class="px-6 pb-4">
                            <Button 
                                @click="router.visit(`/services/${service.id}/edit`)" 
                                variant="outline" 
                                size="sm" 
                                class="w-full border-tg-section-separator text-tg-text hover:bg-tg-section-separator/20"
                            >
                                <Icon name="Edit" class="mr-2 h-4 w-4" />
                                {{ t('edit') }}
                            </Button>
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayoutWithNavigation>

    <!-- Enhanced Delete Confirmation Dialog -->
    <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
        <AlertDialogContent class="max-w-md rounded-xl border-tg-section-separator bg-white shadow-xl">
            <AlertDialogHeader class="text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10">
                    <Icon name="AlertTriangle" class="h-6 w-6 text-red-600" />
                </div>
                <AlertDialogTitle class="text-xl font-bold text-tg-text">{{ t('app.delete_service') }}</AlertDialogTitle>
                <AlertDialogDescription class="text-tg-hint">
                    {{ t('app.delete_service_confirmation', { name: serviceToDelete?.title || '' }) }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter class="flex-col sm:flex-col gap-3">
                <AlertDialogCancel class="w-full border-tg-section-separator bg-tg-section-bg text-tg-text hover:bg-tg-section-separator/20">
                    {{ t('app.cancel') }}
                </AlertDialogCancel>
                <AlertDialogAction 
                    @click="confirmDelete" 
                    class="w-full bg-red-600 text-white hover:bg-red-700"
                >
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
import Icon from '@/components/Icon.vue';
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
