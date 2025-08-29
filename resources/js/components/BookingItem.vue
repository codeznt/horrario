<template>
    <Card class="border-tg-section-separator bg-tg-section-bg">
        <CardContent class="p-4">
            <div class="mb-3 flex items-start justify-between">
                <div class="flex-1">
                    <div class="mb-2 flex items-center gap-2">
                        <h3 class="font-semibold text-tg-text">{{ booking.service.title }}</h3>
                        <Badge :variant="getStatusVariant(booking.status)" class="text-xs">
                            {{ formatStatus(booking.status) }}
                        </Badge>
                    </div>

                    <div class="space-y-1 text-sm">
                        <div class="flex items-center gap-2 text-tg-subtitle-text">
                            <Icon name="Clock" class="h-3 w-3" />
                            {{ formatTime(booking.start_datetime) }} - {{ formatTime(booking.end_datetime) }}
                        </div>
                        <div class="flex items-center gap-2 text-tg-subtitle-text">
                            <Icon name="User" class="h-3 w-3" />
                            {{ booking.user.name }}
                        </div>
                        <div class="flex items-center gap-2 text-tg-subtitle-text">
                            <Icon name="Mail" class="h-3 w-3" />
                            {{ booking.user.email }}
                        </div>
                        <div class="flex items-center gap-2 text-tg-subtitle-text">
                            <Icon name="Timer" class="h-3 w-3" />
                            {{ booking.service.duration_minutes }} {{ t('app.minutes') }}
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

            <div v-if="booking.status === 'pending'" class="flex gap-2 border-t border-tg-section-separator pt-2">
                <Button variant="outline" size="sm" @click="$emit('decline', booking.id)" class="flex-1">
                    <Icon name="X" class="mr-1 h-3 w-3" />
                    {{ t('app.decline') }}
                </Button>

                <Button size="sm" @click="$emit('confirm', booking.id)" class="flex-1">
                    <Icon name="Check" class="mr-1 h-3 w-3" />
                    {{ t('app.confirm') }}
                </Button>
            </div>

            <div v-else-if="booking.status === 'confirmed'" class="border-t border-tg-section-separator pt-2">
                <div class="flex items-center gap-2 text-sm text-green-600">
                    <Icon name="CheckCircle" class="h-4 w-4" />
                    Booking confirmed
                </div>
            </div>

            <div v-else-if="booking.status === 'declined'" class="border-t border-tg-section-separator pt-2">
                <div class="flex items-center gap-2 text-sm text-red-600">
                    <Icon name="XCircle" class="h-4 w-4" />
                    Booking declined
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Badge, Button, Card, CardContent } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

interface Booking {
    id: number;
    status: string;
    start_datetime: string;
    end_datetime: string;
    notes?: string;
    service: {
        id: number;
        title: string;
        duration_minutes: number;
        price: number;
    };
    user: {
        id: number;
        name: string;
        email: string;
    };
}

interface Props {
    booking: Booking;
}

defineProps<Props>();

defineEmits<{
    confirm: [bookingId: number];
    decline: [bookingId: number];
}>();

function formatTime(datetime: string) {
    return new Date(datetime).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
}

function formatStatus(status: string) {
    switch (status) {
        case 'confirmed':
            return t('app.confirmed');
        case 'pending':
            return t('app.pending');
        case 'declined':
            return t('app.declined');
        case 'completed':
            return t('app.completed');
        default:
            return status.replace('_', ' ').toUpperCase();
    }
}

function getStatusVariant(status: string) {
    switch (status) {
        case 'confirmed':
            return 'default';
        case 'pending':
            return 'secondary';
        case 'declined':
            return 'destructive';
        case 'completed':
            return 'default';
        default:
            return 'secondary';
    }
}
</script>
