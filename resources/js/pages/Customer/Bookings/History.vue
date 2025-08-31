<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, Link, router } from '@inertiajs/vue3';

// shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

// Icons
import Icon from '@/components/Icon.vue';

const { t } = useTranslations();

// Props interface
interface Service {
    id: number;
    title: string;
    display_price: string;
    duration_minutes: number;
}

interface Provider {
    id: number;
    business_name: string;
    address: string | null;
}

interface Booking {
    id: number;
    status: string;
    start_datetime: string;
    end_datetime: string;
    notes: string | null;
    service: Service;
    provider: Provider;
}

interface Props {
    bookings: {
        data: Booking[];
        links: any[];
        meta: any;
    };
}

defineProps<Props>();

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'completed':
            return 'CheckCircle';
        case 'cancelled':
        case 'declined':
            return 'XCircle';
        default:
            return 'AlertCircle';
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed':
            return 'bg-green-500/20 text-green-600 border-green-500/30';
        case 'cancelled':
        case 'declined':
            return 'bg-red-500/20 text-red-600 border-red-500/30';
        case 'confirmed':
            return 'bg-blue-500/20 text-blue-600 border-blue-500/30';
        default:
            return 'bg-yellow-500/20 text-yellow-600 border-yellow-500/30';
    }
};
</script>

<template>
    <Head :title="t('app.booking_history')" />

    <AppLayoutWithNavigation class="bg-tg-bg">
        <div class="flex min-h-[--spacing-viewport-h] flex-col">
            <!-- Header -->
            <div class="border-b border-tg-section-separator bg-tg-secondary-bg px-4 py-4">
                <div class="flex items-center gap-3">
                    <Link :href="'/customer/dashboard'" class="rounded-full bg-tg-accent/10 p-2">
                        <Icon name="ArrowLeft" class="h-5 w-5 text-tg-accent" />
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-tg-text">{{ t('app.booking_history') }}</h1>
                        <p class="text-sm text-tg-hint">{{ t('app.past_appointments') }}</p>
                    </div>
                    <div class="flex items-center gap-1 bg-tg-accent/10 px-2 py-1 rounded-full">
                        <Icon name="History" class="h-4 w-4 text-tg-accent" />
                        <span class="text-sm font-medium text-tg-accent">{{ bookings?.meta?.total || 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Booking History List -->
            <div class="flex-1 p-4">
                <div v-if="(bookings?.data?.length || 0) === 0" class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-tg-section-bg">
                        <Icon name="History" class="h-8 w-8 text-tg-hint" />
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-tg-text">{{ t('app.no_booking_history') }}</h3>
                    <p class="mb-4 text-sm text-tg-hint px-4">{{ t('app.no_past_bookings_desc') }}</p>
                    <Button @click="router.visit('/browse/services')" class="bg-tg-accent text-white hover:bg-tg-accent/90">
                        <Icon name="Search" class="mr-2 h-4 w-4" />
                        {{ t('app.browse_services') }}
                    </Button>
                </div>

                <div v-else class="space-y-4">
                    <Card
                        v-for="booking in (bookings?.data || [])"
                        :key="booking.id"
                        class="border-tg-section-separator bg-tg-section-bg"
                    >
                        <CardHeader class="pb-3">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <CardTitle class="text-lg text-tg-text">{{ booking.service.title }}</CardTitle>
                                    <CardDescription class="text-tg-accent font-medium">
                                        {{ booking.provider.business_name }}
                                    </CardDescription>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Icon 
                                        :name="getStatusIcon(booking.status)" 
                                        class="h-4 w-4"
                                        :class="booking.status === 'completed' ? 'text-green-600' : 
                                               booking.status === 'cancelled' || booking.status === 'declined' ? 'text-red-600' : 
                                               'text-yellow-600'"
                                    />
                                    <Badge 
                                        :class="getStatusColor(booking.status)"
                                        class="text-xs border"
                                    >
                                        {{ t(`app.booking_status_${booking.status}`) }}
                                    </Badge>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <!-- Date and Time -->
                            <div class="flex items-center gap-4 text-sm text-tg-hint">
                                <div class="flex items-center gap-2">
                                    <Icon name="Calendar" class="h-4 w-4" />
                                    <span>{{ booking.start_datetime }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Icon name="Clock" class="h-4 w-4" />
                                    <span>{{ booking.service.duration_minutes }} {{ t('app.minutes') }}</span>
                                </div>
                            </div>

                            <!-- Location -->
                            <div v-if="booking.provider.address" class="flex items-center gap-2 text-sm text-tg-hint">
                                <Icon name="MapPin" class="h-4 w-4" />
                                <span>{{ booking.provider.address }}</span>
                            </div>

                            <!-- Price -->
                            <div class="flex items-center gap-2 text-sm">
                                <Icon name="DollarSign" class="h-4 w-4 text-tg-accent" />
                                <span class="font-semibold text-tg-text">{{ booking.service.display_price }}</span>
                            </div>

                            <!-- Notes -->
                            <div v-if="booking.notes" class="bg-tg-bg rounded-lg p-3">
                                <p class="text-sm text-tg-hint">
                                    <span class="font-medium text-tg-text">{{ t('app.notes') }}:</span>
                                    {{ booking.notes }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-2">
                                <Button 
                                    @click="router.visit(`/browse/services/${booking.service.id}`)"
                                    variant="outline" 
                                    size="sm" 
                                    class="flex-1"
                                >
                                    {{ t('app.book_again') }}
                                </Button>
                                <Button 
                                    variant="outline" 
                                    size="sm"
                                    class="flex-1"
                                >
                                    {{ t('app.view_provider') }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Pagination -->
                    <div v-if="(bookings?.links?.length || 0) > 3" class="flex justify-center gap-1 mt-6">
                        <template v-for="link in (bookings?.links || [])" :key="link.label">
                            <Button
                                v-if="link.url"
                                @click="router.get(link.url)"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                class="min-w-[40px]"
                            >
                                {{ link.label }}
                            </Button>
                            <Button
                                v-else
                                variant="ghost"
                                size="sm"
                                disabled
                                class="min-w-[40px]"
                            >
                                {{ link.label }}
                            </Button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayoutWithNavigation>
</template>