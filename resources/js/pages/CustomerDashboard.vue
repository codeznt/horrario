<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

// Icons
import Icon from '@/components/Icon.vue';

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
            icon: 'Search',
            variant: 'default' as const,
            urgent: true
        },
        {
            title: t('app.my_bookings'),
            description: t('app.manage_appointments'),
            href: '/bookings',
            icon: 'Calendar',
            variant: 'outline' as const,
            urgent: false
        },
        {
            title: t('app.favorites'),
            description: t('app.saved_providers'),
            href: '/favorites',
            icon: 'Heart',
            variant: 'outline' as const,
            urgent: false
        },
        {
            title: t('app.booking_history'),
            description: t('app.past_appointments'),
            href: '/bookings/history',
            icon: 'History',
            variant: 'outline' as const,
            urgent: false
        }
    ];
});

// Format date for bookings
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('uk-UA', {
        weekday: 'short',
        day: 'numeric',
        month: 'short'
    });
};

// Format time for bookings
const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('uk-UA', {
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="t('app.customer_dashboard')" />

    <AppLayoutWithNavigation class="bg-tg-bg">
        <div class="flex min-h-full flex-col">
            <!-- Enhanced Header with Personalized Greeting -->
            <div class="relative overflow-hidden bg-gradient-to-r from-tg-accent to-blue-500 px-4 py-6">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjAuNSIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+')] opacity-10"></div>
                <div class="relative flex items-center justify-between">
                    <div class="max-w-[80%]">
                        <h1 class="text-2xl font-bold text-white">
                            {{ t('app.welcome_back') }}
                        </h1>
                        <p class="mt-1 text-sm text-white/90">
                            {{ t('app.find_book_services') }}
                        </p>
                    </div>
                    <Link :href="'/settings/profile'" class="rounded-full bg-white/20 p-2 backdrop-blur-sm transition-all hover:bg-white/30">
                        <Icon name="Settings" class="h-5 w-5 text-white" />
                    </Link>
                </div>
            </div>

            <!-- Enhanced Stats Cards with Better Visual Design -->
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2">
                <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-shadow duration-300">
                    <CardContent class="p-5 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-tg-accent/10">
                            <Icon name="Calendar" class="h-6 w-6 text-tg-accent" />
                        </div>
                        <div class="text-3xl font-bold text-tg-text">{{ bookingsCount }}</div>
                        <div class="text-sm font-medium text-tg-hint">{{ t('app.total_bookings') }}</div>
                    </CardContent>
                </Card>
                
                <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-shadow duration-300">
                    <CardContent class="p-5 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-pink-500/10">
                            <Icon name="Heart" class="h-6 w-6 text-pink-600" />
                        </div>
                        <div class="text-3xl font-bold text-tg-text">{{ favoriteProvidersCount }}</div>
                        <div class="text-sm font-medium text-tg-hint">{{ t('app.favorites') }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Enhanced Quick Actions with Better Visual Hierarchy -->
            <div class="flex-1 p-4">
                <div class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-tg-text">
                            {{ t('app.quick_actions') }}
                        </h2>
                        <div class="h-1 w-12 rounded-full bg-tg-accent"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Link
                            v-for="(action, index) in quickActions"
                            :key="action.href"
                            :href="action.href"
                            class="block transition-all duration-300 hover:-translate-y-0.5"
                        >
                            <Card 
                                class="border-2 h-full transition-all duration-300 hover:shadow-lg"
                                :class="[
                                    action.urgent 
                                        ? 'border-tg-accent bg-gradient-to-br from-tg-accent/10 to-tg-accent/5' 
                                        : 'border-tg-section-separator bg-gradient-to-br from-tg-section-bg to-white hover:border-tg-accent/30'
                                ]"
                            >
                                <CardContent class="flex items-center gap-4 p-5">
                                    <div 
                                        class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl"
                                        :class="action.urgent ? 'bg-tg-accent/10' : 'bg-tg-section-separator/20'"
                                    >
                                        <Icon :name="action.icon" class="h-7 w-7" :class="action.urgent ? 'text-tg-accent' : 'text-tg-hint'" />
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-tg-text">{{ action.title }}</h3>
                                        <p class="mt-1 text-sm text-tg-hint">{{ action.description }}</p>
                                    </div>
                                    <Icon name="ChevronRight" class="h-5 w-5 text-tg-hint" />
                                </CardContent>
                            </Card>
                        </Link>
                    </div>
                </div>

                <!-- Enhanced Upcoming Bookings with Better Visual Design -->
                <div v-if="hasUpcomingBookings" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-tg-text">{{ t('app.upcoming_bookings') }}</h2>
                        <Link :href="'/bookings'" class="text-sm font-medium text-tg-accent hover:underline">
                            {{ t('app.view_all') }}
                        </Link>
                    </div>
                    <div class="space-y-3">
                        <Card 
                            v-for="booking in upcomingBookings.slice(0, 3)" 
                            :key="booking.id"
                            class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-shadow duration-300"
                        >
                            <CardContent class="p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-tg-accent/10">
                                            <Icon name="Calendar" class="h-5 w-5 text-tg-accent" />
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-tg-text">{{ booking.service.title }}</h4>
                                            <p class="text-sm text-tg-hint">{{ booking.provider.business_name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-tg-text">
                                                {{ formatDate(booking.start_datetime) }}
                                            </div>
                                            <div 
                                                class="text-xs px-2 py-1 rounded-full font-medium"
                                                :class="{
                                                    'bg-yellow-500/20 text-yellow-700': booking.status === 'pending',
                                                    'bg-green-500/20 text-green-700': booking.status === 'confirmed',
                                                    'bg-blue-500/20 text-blue-700': booking.status === 'completed'
                                                }"
                                            >
                                                {{ t(`app.booking_status_${booking.status}`) }}
                                            </div>
                                        </div>
                                        <div class="text-xs text-tg-hint mt-1">
                                            {{ formatTime(booking.start_datetime) }}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Enhanced Recently Viewed Services with Visual Cards -->
                <div v-if="hasRecentServices" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-tg-text">{{ t('app.recently_viewed') }}</h2>
                        <Link :href="'/browse/services'" class="text-sm font-medium text-tg-accent hover:underline">
                            {{ t('app.view_all') }}
                        </Link>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Card 
                            v-for="service in recentServices.slice(0, 4)" 
                            :key="service.id"
                            class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5"
                        >
                            <CardContent class="p-5">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-tg-text">{{ service.title }}</h4>
                                        <p class="text-xs text-tg-hint mt-1">{{ service.provider.business_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-tg-accent">{{ service.display_price }}</div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Enhanced Empty State for New Users -->
                <div v-if="!hasUpcomingBookings && !hasRecentServices" class="py-8">
                    <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg">
                        <CardContent class="p-8 text-center">
                            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-tg-accent/10">
                                <Icon name="Search" class="h-8 w-8 text-tg-accent" />
                            </div>
                            <h3 class="mb-3 text-xl font-bold text-tg-text">{{ t('app.start_exploring') }}</h3>
                            <p class="mb-6 text-tg-hint max-w-md mx-auto">
                                {{ t('app.discover_services_nearby') }}
                            </p>
                            <Button as="a" :href="'/browse/services'" size="lg" class="bg-tg-accent text-white hover:bg-tg-accent/90">
                                <Icon name="Search" class="mr-2 h-5 w-5" />
                                {{ t('app.browse_services') }}
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>

        </div>
    </AppLayoutWithNavigation>
</template>