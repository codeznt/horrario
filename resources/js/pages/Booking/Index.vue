<template>
    <Head :title="t('app.my_bookings')" />

    <MobileAppLayout :title="t('app.my_bookings')">
        <template #header-actions>
            <div class="flex gap-2">
                <Button @click="router.visit('/bookings/upcoming')" size="sm" variant="outline">
                    <Icon name="Clock" class="mr-1 h-4 w-4" />
                    {{ t('app.upcoming_bookings') }}
                </Button>
                <Button @click="router.visit('/bookings/past')" size="sm" variant="outline">
                    <Icon name="History" class="mr-1 h-4 w-4" />
                    {{ t('app.past_bookings') }}
                </Button>
                <Button @click="router.visit('/browse/services')" size="sm">
                    <Icon name="Plus" class="mr-1 h-4 w-4" />
                    {{ t('app.book_service') }}
                </Button>
            </div>
        </template>

        <div v-if="Object.keys(bookingsByDate).length === 0" class="py-12 text-center">
            <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-tg-section-bg p-6">
                <Icon name="Calendar" class="h-12 w-12 text-tg-hint" />
            </div>
            <h3 class="mb-2 text-xl font-semibold text-tg-text">{{ t('app.no_bookings_found') }}</h3>
            <p class="mb-6 px-4 text-tg-subtitle-text">
                {{ t('app.no_bookings_description') }}
            </p>
            <Button @click="router.visit('/browse/services')" class="mx-auto">
                <Icon name="Search" class="mr-2 h-4 w-4" />
                {{ t('app.find_services') }}
            </Button>
        </div>

        <div v-else class="space-y-6">
            <div v-for="(bookings, date) in bookingsByDate" :key="date" class="space-y-3">
                <!-- Date Header -->
                <div class="sticky top-0 z-10 bg-tg-bg py-2">
                    <h2 class="text-lg font-semibold text-tg-text">{{ formatDate(date) }}</h2>
                    <p class="text-sm text-tg-subtitle-text">{{ bookings.length }} {{ bookings.length === 1 ? t('app.booking') : t('app.bookings') }}</p>
                </div>

                <!-- Bookings for this date -->
                <div class="space-y-3">
                    <BookingCard v-for="booking in bookings" :key="booking.id" :booking="booking" @cancel="cancelBooking" />
                </div>
            </div>
        </div>

        <!-- Confirmation Dialog -->
        <ConfirmDialog
            :show="showCancelDialog"
            :title="t('app.cancel_booking')"
            :message="t('app.decline_booking_message')"
            variant="destructive"
            @confirm="confirmCancel"
            @cancel="showCancelDialog = false"
        />
    </MobileAppLayout>
</template>

<script setup lang="ts">
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Icon from '@/components/Icon.vue';
import { Button } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import MobileAppLayout from '@/layouts/MobileAppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import BookingCard from '../../components/BookingCard.vue';

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
    bookingsByDate: Record<string, Booking[]>;
}

defineProps<Props>();

const showCancelDialog = ref(false);
const bookingToCancel = ref<number | null>(null);

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function cancelBooking(bookingId: number) {
    bookingToCancel.value = bookingId;
    showCancelDialog.value = true;
}

function confirmCancel() {
    if (!bookingToCancel.value) return;

    router.post(
        `/bookings/${bookingToCancel.value}/cancel`,
        {},
        {
            onFinish: () => {
                showCancelDialog.value = false;
                bookingToCancel.value = null;
            },
        },
    );
}
</script>
