<template>
    <Head :title="t('app.booking_details')" />

    <AppLayoutWithNavigation :title="t('app.booking_details')">

        <div class="container mx-auto px-4 py-8">
            <div class="mx-auto max-w-2xl">
                <!-- Header -->
                <div class="mb-8">
                    <Button variant="ghost" @click="router.visit('/bookings')" class="mb-4">
                        <Icon name="ArrowLeft" class="mr-2 h-4 w-4" />
                        {{ t('app.my_bookings') }}
                    </Button>
                    <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-bold text-foreground">{{ t('app.booking_details') }}</h1>
                        <Badge :variant="getStatusVariant(booking.status)">
                            {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                        </Badge>
                    </div>
                </div>

                <!-- Booking Information -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Icon name="Calendar" class="h-5 w-5" />
                            {{ booking.service.title }}
                        </CardTitle>
                        <CardDescription> {{ t('app.booking') }} #{{ booking.id }} </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Date & Time -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label class="text-sm font-medium text-muted-foreground">{{ t('app.date') }}</Label>
                                <p class="text-lg font-semibold">{{ formatDate(booking.start_datetime) }}</p>
                            </div>
                            <div class="space-y-2">
                                <Label class="text-sm font-medium text-muted-foreground">{{ t('app.time') }}</Label>
                                <p class="text-lg font-semibold">{{ formatTime(booking.start_datetime) }} - {{ formatTime(booking.end_datetime) }}</p>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-muted-foreground">{{ t('app.service') }}</Label>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">{{ booking.service.title }}</p>
                                    <p v-if="booking.service.description" class="text-sm text-muted-foreground">
                                        {{ booking.service.description }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold">{{ booking.service.display_price }}</p>
                                    <p class="text-sm text-muted-foreground">{{ booking.service.duration_minutes }} min</p>
                                </div>
                            </div>
                        </div>

                        <!-- Provider Information -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-muted-foreground">{{ t('app.provider') }}</Label>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                    <Icon name="User" class="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <p class="font-semibold">{{ booking.provider.business_name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ booking.provider.user.name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="booking.notes" class="space-y-2">
                            <Label class="text-sm font-medium text-muted-foreground">{{ t('app.notes') }}</Label>
                            <p class="rounded-lg bg-muted p-3 text-sm">{{ booking.notes }}</p>
                        </div>

                        <!-- Booking Dates -->
                        <div class="grid grid-cols-1 gap-4 text-xs text-muted-foreground md:grid-cols-2">
                            <div>
                <span class="font-medium">{{ t('app.booked') }}:</span> {{ formatDateTime(booking.created_at) }}
                            </div>
                            <div v-if="booking.updated_at !== booking.created_at">
                <span class="font-medium">{{ t('app.updated') }}:</span> {{ formatDateTime(booking.updated_at) }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions -->
                <div v-if="canCancelBooking" class="flex justify-end">
                    <Button variant="destructive" @click="showCancelDialog = true" class="flex items-center gap-2">
                        <Icon name="X" class="h-4 w-4" />
            {{ t('app.cancel_booking') }}
                    </Button>
                </div>
            </div>
        </div>
    <!-- Cancel Confirmation Dialog -->
    </AppLayoutWithNavigation>

    <AlertDialog :open="showCancelDialog" @update:open="showCancelDialog = $event">
        <AlertDialogContent>
            <AlertDialogHeader>
        <AlertDialogTitle>{{ t('app.cancel_booking') }}</AlertDialogTitle>
                <AlertDialogDescription>
          {{ t('app.cancel_booking_confirmation') }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
        <AlertDialogCancel>{{ t('app.keep_booking') }}</AlertDialogCancel>
                <AlertDialogAction @click="cancelBooking" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
          {{ t('app.cancel_booking') }}
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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { useTranslations } from '@/composables/useTranslations';
import { Head, router } from '@inertiajs/vue3';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import Icon from '@/components/Icon.vue';
import { computed, ref } from 'vue';

interface Booking {
    id: number;
    start_datetime: string;
    end_datetime: string;
    status: string;
    notes?: string;
    created_at: string;
    updated_at: string;
    service: {
        id: number;
        title: string;
        description?: string;
        duration_minutes: number;
        display_price: string;
    };
    provider: {
        id: number;
        business_name: string;
        user: {
            name: string;
        };
    };
    user: {
        id: number;
        name: string;
    };
}

const props = defineProps<{
    booking: Booking;
}>();
const { t } = useTranslations();

const showCancelDialog = ref(false);

const canCancelBooking = computed(() => {
    return ['pending', 'confirmed'].includes(props.booking.status);
});

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'pending':
            return 'secondary';
        case 'confirmed':
            return 'default';
        case 'completed':
            return 'default';
        case 'cancelled':
            return 'destructive';
        default:
            return 'secondary';
    }
};

const formatDate = (datetime: string) => {
    return new Date(datetime).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatTime = (datetime: string) => {
    return new Date(datetime).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const formatDateTime = (datetime: string) => {
    return new Date(datetime).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const cancelBooking = () => {
    router.post(
        `/bookings/${props.booking.id}/cancel`,
        {},
        {
            onSuccess: () => {
                showCancelDialog.value = false;
            },
        },
    );
};
</script>
