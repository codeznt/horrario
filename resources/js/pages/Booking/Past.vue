<template>
    <Head :title="t('app.past_bookings')" />

    <AppLayoutWithNavigation :title="t('app.past_bookings')">
        <template #header-actions>
            <div class="flex gap-2">
                <Button @click="router.visit('/bookings')" size="sm" variant="outline">
                    <Icon name="Calendar" class="mr-1 h-4 w-4" />
                    {{ t('app.view_all') }}
                </Button>
                <Button @click="router.visit('/bookings/upcoming')" size="sm" variant="outline">
                    <Icon name="Clock" class="mr-1 h-4 w-4" />
                    {{ t('app.upcoming_bookings') }}
                </Button>
                <Button @click="router.visit('/browse/services')" size="sm">
                    <Icon name="Plus" class="mr-1 h-4 w-4" />
                    {{ t('app.book_service') }}
                </Button>
            </div>
        </template>

        <div v-if="bookings.length === 0" class="py-12 text-center">
            <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-tg-section-bg p-6">
                <Icon name="History" class="h-12 w-12 text-tg-hint" />
            </div>
            <h3 class="mb-2 text-xl font-semibold text-tg-text">{{ t('app.no_past_bookings') }}</h3>
            <p class="mb-6 px-4 text-tg-subtitle-text">
                {{ t('app.no_past_bookings_description') }}
            </p>
            <Button @click="router.visit('/browse/services')" class="mx-auto">
                <Icon name="Search" class="mr-2 h-4 w-4" />
                {{ t('app.browse_services') }}
            </Button>
        </div>

        <div v-else class="space-y-4">
            <!-- Summary Card -->
            <Card class="border-tg-section-separator bg-tg-section-bg">
                <CardContent class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-tg-text">{{ t('app.booking_history') }}</h3>
                            <p class="text-sm text-tg-subtitle-text">{{ t('app.completed_appointments') }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-tg-accent">{{ bookings.length }}</div>
                            <div class="text-sm text-tg-subtitle-text">{{ bookings.length === 1 ? t('app.booking') : t('app.bookings') }}</div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Bookings List -->
            <div class="space-y-3">
                <BookingCard v-for="booking in bookings" :key="booking.id" :booking="booking" @cancel="cancelBooking" />
            </div>
        </div>

        <!-- Confirmation Dialog -->
        <ConfirmDialog
            :show="showCancelDialog"
            :title="t('app.cancel_booking')"
            :message="t('app.cancel_booking_confirmation')"
            variant="destructive"
            @confirm="confirmCancel"
            @cancel="showCancelDialog = false"
        />
    </AppLayoutWithNavigation>
</template>

<script setup lang="ts">
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Icon from '@/components/Icon.vue';
import { Button, Card, CardContent } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import BookingCard from '../../components/BookingCard.vue';

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
    bookings: Booking[];
}

defineProps<Props>();
const { t } = useTranslations();

const showCancelDialog = ref(false);
const bookingToCancel = ref<number | null>(null);

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
