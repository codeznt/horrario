<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

// Icons
import { 
    Calendar, 
    Search, 
    Settings,
    Heart,
    History
} from 'lucide-vue-next';

const { t } = useTranslations();

// Props interface
interface Service {
    id: number;
    title: string;
    display_price: string;
    provider: {
        business_name: string;
        id: number;
    };
}

interface Booking {
    id: number;
    status: string;
    start_datetime: string;
    service: {
        title: string;
    };
    provider: {
        business_name: string;
    };
}

interface Props {
    upcomingBookings: Booking[];
    recentServices: Service[];
    bookingsCount: number;
    favoriteProvidersCount: number;
}

const props = defineProps<Props>();

// Computed values
const hasUpcomingBookings = computed(() => props.upcomingBookings.length > 0);
const hasRecentServices = computed(() => props.recentServices.length > 0);

// Quick actions for customers
const quickActions = computed(() => {
    return [
        {
            title: t('app.find_services'),
            description: t('app.discover_local_services'),
            href: '/browse/services',
            icon: Search,
            variant: 'default' as const,
            urgent: true
        },
        {
            title: t('app.my_bookings'),
            description: t('app.manage_appointments'),
            href: '/bookings',
            icon: Calendar,
            variant: 'outline' as const,
            urgent: false
        },
        {
            title: t('app.favorites'),
            description: t('app.saved_providers'),
            href: '/favorites',
            icon: Heart,
            variant: 'outline' as const,
            urgent: false
        },
        {
            title: t('app.booking_history'),
            description: t('app.past_appointments'),
            href: '/bookings/history',
            icon: History,
            variant: 'outline' as const,
            urgent: false
        }
    ];
});
</script>

<template>
    <Head :title="t('app.customer_dashboard')" />

    <AppLayoutWithNavigation class="bg-tg-bg">
        <div class="flex min-h-full flex-col">
            <!-- Header -->
            <div class="border-b border-tg-section-separator bg-tg-secondary-bg px-4 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-tg-text">
                            {{ t('app.welcome_back') }}
                        </h1>
                        <p class="mt-1 text-sm text-tg-hint">
                            {{ t('app.find_book_services') }}
                        </p>
                    </div>
                    <Link :href="'/settings/profile'" class="rounded-full bg-tg-accent/10 p-2">
                        <Settings class="h-5 w-5 text-tg-accent" />
                    </Link>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-3 p-4">
                <Card class="border-tg-section-separator bg-tg-section-bg">
                    <CardContent class="p-4 text-center">
                        <Calendar class="mx-auto mb-2 h-8 w-8 text-tg-accent" />
                        <div class="text-xl font-bold text-tg-text">{{ bookingsCount }}</div>
                        <div class="text-xs text-tg-hint">{{ t('app.total_bookings') }}</div>
                    </CardContent>
                </Card>
                
                <Card class="border-tg-section-separator bg-tg-section-bg">
                    <CardContent class="p-4 text-center">
                        <Heart class="mx-auto mb-2 h-8 w-8 text-tg-accent" />
                        <div class="text-xl font-bold text-tg-text">{{ favoriteProvidersCount }}</div>
                        <div class="text-xs text-tg-hint">{{ t('app.favorites') }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <div class="flex-1 p-4">
                <div class="mb-6">
                    <h2 class="mb-4 text-lg font-semibold text-tg-text">
                        {{ t('app.quick_actions') }}
                    </h2>
                    <div class="space-y-3">
                        <Link
                            v-for="action in quickActions"
                            :key="action.href"
                            :href="action.href"
                            class="block"
                        >
                            <Card 
                                class="border-2 transition-all duration-200 hover:scale-[0.98] active:scale-95"
                                :class="[
                                    action.urgent 
                                        ? 'border-tg-accent bg-tg-accent/5' 
                                        : 'border-tg-section-separator bg-tg-section-bg hover:bg-tg-secondary-bg'
                                ]"
                            >
                                <CardContent class="flex items-center gap-4 p-4">
                                    <div 
                                        class="flex h-12 w-12 items-center justify-center rounded-xl"
                                        :class="action.urgent ? 'bg-tg-accent/10' : 'bg-tg-accent/10'"
                                    >
                                        <component :is="action.icon" class="h-6 w-6 text-tg-accent" />
                                    </div>
                                    <div class="flex-1 text-left">
                                        <h3 class="font-semibold text-tg-text">{{ action.title }}</h3>
                                        <p class="text-sm text-tg-hint">{{ action.description }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                        </Link>
                    </div>
                </div>

                <!-- Upcoming Bookings -->
                <div v-if="hasUpcomingBookings" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.upcoming_bookings') }}</h2>
                        <Link :href="'/bookings'" class="text-sm text-tg-accent">
                            {{ t('app.view_all') }}
                        </Link>
                    </div>
                    <div class="space-y-2">
                        <Card 
                            v-for="booking in upcomingBookings.slice(0, 3)" 
                            :key="booking.id"
                            class="border-tg-section-separator bg-tg-section-bg"
                        >
                            <CardContent class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-tg-text">{{ booking.service.title }}</h4>
                                        <p class="text-sm text-tg-hint">{{ booking.provider.business_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-tg-text">
                                            {{ new Date(booking.start_datetime).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}
                                        </div>
                                        <div 
                                            class="text-xs px-2 py-1 rounded-full"
                                            :class="{
                                                'bg-yellow-500/20 text-yellow-600': booking.status === 'pending',
                                                'bg-green-500/20 text-green-600': booking.status === 'confirmed',
                                                'bg-blue-500/20 text-blue-600': booking.status === 'completed'
                                            }"
                                        >
                                            {{ t(`app.booking_status_${booking.status}`) }}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Recently Viewed Services -->
                <div v-if="hasRecentServices" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.recently_viewed') }}</h2>
                        <Link :href="'/browse/services'" class="text-sm text-tg-accent">
                            {{ t('app.view_all') }}
                        </Link>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <Card 
                            v-for="service in recentServices.slice(0, 4)" 
                            :key="service.id"
                            class="border-tg-section-separator bg-tg-section-bg"
                        >
                            <CardContent class="p-4">
                                <h4 class="font-medium text-tg-text">{{ service.title }}</h4>
                                <p class="text-xs text-tg-hint mb-2">{{ service.provider.business_name }}</p>
                                <p class="text-sm font-semibold text-tg-accent">{{ service.display_price }}</p>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Empty State for New Users -->
                <div v-if="!hasUpcomingBookings && !hasRecentServices" class="py-8 text-center">
                    <div class="rounded-lg border border-dashed border-tg-section-separator p-6">
                        <Search class="mx-auto mb-4 h-12 w-12 text-tg-hint" />
                        <h3 class="mb-2 text-lg font-semibold text-tg-text">{{ t('app.start_exploring') }}</h3>
                        <p class="mb-4 text-sm text-tg-hint">{{ t('app.discover_services_nearby') }}</p>
                        <Button as="a" :href="'/browse/services'" class="bg-tg-accent text-white hover:bg-tg-accent/90">
                            <Search class="mr-2 h-4 w-4" />
                            {{ t('app.browse_services') }}
                        </Button>
                    </div>
                </div>
            </div>

        </div>
    </AppLayoutWithNavigation>
</template>