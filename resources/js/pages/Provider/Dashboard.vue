<template>
    <Head :title="t('app.provider_dashboard')" />

    <MobileAppLayout :title="t('app.todays_bookings')" :subtitle="formatDate(date)">
        <template #header-actions>
            <Button @click="router.visit('/provider/bookings')" size="sm" variant="outline">
                <Icon name="Calendar" class="mr-1 h-4 w-4" />
                {{ t('schedule') }}
            </Button>
        </template>

        <div v-if="bookings?.length === 0" class="py-12 text-center">
            <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-tg-section-bg p-6">
                <Icon name="Calendar" class="h-12 w-12 text-tg-hint" />
            </div>
            <h3 class="mb-2 text-xl font-semibold text-tg-text">{{ t('app.no_bookings_today') }}</h3>
            <p class="mb-6 px-4 text-tg-subtitle-text">
                {{ t('app.no_bookings_description') }}
            </p>
        </div>

        <div v-else class="space-y-4">
            <!-- Summary Card -->
            <Card class="border-tg-section-separator bg-tg-section-bg">
                <CardContent class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-tg-text">{{ t('app.todays_summary') }}</h3>
                            <p class="text-sm text-tg-subtitle-text">{{ formatDate(date) }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-tg-accent">{{ bookings?.length }}</div>
                            <div class="text-sm text-tg-subtitle-text">{{ bookings?.length === 1 ? t('app.booking') : t('app.bookings') }}</div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Bookings List -->
            <div class="space-y-3">
                <BookingItem v-for="booking in bookings" :key="booking.id" :booking="booking" @confirm="confirmBooking" @decline="declineBooking" />
            </div>
        </div>

        <!-- Confirmation Dialog -->
        <ConfirmDialog
            :show="showConfirmDialog"
            :title="t('app.confirm_booking')"
            :message="t('app.confirm_booking_message')"
            @confirm="handleConfirm"
            @cancel="showConfirmDialog = false"
        />

        <!-- Decline Dialog -->
        <ConfirmDialog
            :show="showDeclineDialog"
            :title="t('app.decline_booking')"
            :message="t('app.decline_booking_message')"
            variant="destructive"
            @confirm="handleDecline"
            @cancel="showDeclineDialog = false"
        />
    </MobileAppLayout>
</template>

<script setup lang="ts">
import BookingItem from '@/components/BookingItem.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Icon from '@/components/Icon.vue';
import { Button, Card, CardContent } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import MobileAppLayout from '@/layouts/MobileAppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    bookings: Booking[];
    date: string;
}

defineProps<Props>();

const showConfirmDialog = ref(false);
const showDeclineDialog = ref(false);
const selectedBookingId = ref<number | null>(null);

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function confirmBooking(bookingId: number) {
    selectedBookingId.value = bookingId;
    showConfirmDialog.value = true;
}

function declineBooking(bookingId: number) {
    selectedBookingId.value = bookingId;
    showDeclineDialog.value = true;
}

function handleConfirm() {
    if (!selectedBookingId.value) return;

    router.post(
        `/bookings/${selectedBookingId.value}/confirm`,
        {},
        {
            onFinish: () => {
                showConfirmDialog.value = false;
                selectedBookingId.value = null;
            },
        },
    );
}

function handleDecline() {
    if (!selectedBookingId.value) return;

    router.post(
        `/bookings/${selectedBookingId.value}/decline`,
        {},
        {
            onFinish: () => {
                showDeclineDialog.value = false;
                selectedBookingId.value = null;
            },
        },
    );
}
</script>
