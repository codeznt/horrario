<template>
    <Card class="border-tg-section-separator bg-tg-section-bg" :class="statusClass">
        <CardContent class="p-4">
            <div class="mb-3 flex items-start justify-between">
                <div class="flex-1">
                    <div class="mb-2 flex items-center gap-2">
                        <h3 class="font-semibold text-tg-text">{{ booking.service.title }}</h3>
                        <Badge :variant="statusVariant" class="text-xs">
                            {{ t(booking.status) }}
                        </Badge>
                    </div>

                    <div class="space-y-1 text-sm">
                        <div class="flex items-center gap-2 text-tg-subtitle-text">
                            <Icon name="Calendar" class="h-3 w-3" />
                            {{ formatDate(booking.start_datetime) }}
                        </div>
                        <div class="flex items-center gap-2 text-tg-subtitle-text">
                            <Icon name="Clock" class="h-3 w-3" />
                            {{ formatTime(booking.start_datetime) }} - {{ formatTime(booking.end_datetime) }}
                        </div>
                        <div class="flex items-center gap-2 text-tg-subtitle-text">
                            <Icon name="MapPin" class="h-3 w-3" />
                            {{ booking.provider.business_name }}
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <div class="font-semibold text-tg-accent">${{ booking.service.price }}</div>
                </div>
            </div>

            <div v-if="booking.notes" class="mb-3 rounded bg-tg-bg p-2 text-sm text-tg-subtitle-text">
                <Icon name="MessageSquare" class="mr-1 inline h-3 w-3" />
                {{ booking.notes }}
            </div>

            <div v-if="canCancel" class="flex gap-2 border-t border-tg-section-separator pt-2">
                <Button variant="outline" size="sm" @click="$emit('cancel', booking.id)" class="flex-1">
                    <Icon name="X" class="mr-1 h-3 w-3" />
                    {{ t('cancel_booking') }}
                </Button>

                <Button variant="ghost" size="sm" @click="router.visit(`/bookings/${booking.id}`)" class="flex-1">
                    <Icon name="Eye" class="mr-1 h-3 w-3" />
                    {{ t('view_booking') }}
                </Button>
            </div>

            <div v-else-if="booking.status === 'cancelled'" class="border-t border-tg-section-separator pt-2">
                <div class="flex items-center gap-2 text-sm text-red-600">
                    <Icon name="XCircle" class="h-4 w-4" />
                    {{ t('booking_cancelled') }}
                </div>
            </div>

            <div v-else-if="booking.status === 'completed'" class="border-t border-tg-section-separator pt-2">
                <div class="flex items-center gap-2 text-sm text-green-600">
                    <Icon name="CheckCircle" class="h-4 w-4" />
                    {{ t('booking_completed') }}
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Badge, Button, Card, CardContent } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Booking {
    id: number;
    status: string;
    start_datetime: string;
    end_datetime: string;
    notes?: string;
    service: {
        id: number;
        title: string;
        price: number;
    };
    provider: {
        id: number;
        business_name: string;
        user: {
            name: string;
        };
    };
}

interface Props {
    booking: Booking;
}

const props = defineProps<Props>();

defineEmits<{
    cancel: [bookingId: number];
}>();

const { t } = useTranslations();

const statusVariant = computed(() => {
    switch (props.booking.status) {
        case 'confirmed':
            return 'default';
        case 'pending':
            return 'secondary';
        case 'declined':
            return 'destructive';
        case 'cancelled':
            return 'outline';
        case 'completed':
            return 'default';
        default:
            return 'secondary';
    }
});

const statusClass = computed(() => {
    return `status-${props.booking.status}`;
});

const canCancel = computed(() => {
    return ['pending', 'confirmed'].includes(props.booking.status) && new Date(props.booking.start_datetime) > new Date();
});

function formatDate(datetime: string) {
    return new Date(datetime).toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });
}

function formatTime(datetime: string) {
    return new Date(datetime).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
}

// Using translations for status labels via t(status)
</script>
