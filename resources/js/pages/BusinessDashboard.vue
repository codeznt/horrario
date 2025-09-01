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
interface Provider {
    id: number;
    business_name: string;
    description: string | null;
}

interface Service {
    id: number;
    title: string;
    display_price: string;
    duration_minutes: number;
}

interface Booking {
    id: number;
    status: string;
    start_datetime: string;
    service: {
        title: string;
    };
    user: {
        name: string;
    };
}

interface Props {
    provider: Provider | null;
    recentBookings: Booking[];
    services: Service[];
    todaysBookings: number;
    weeklyBookings: number;
    monthlyRevenue: string;
}

const props = defineProps<Props>();

// Computed values
const hasProvider = computed(() => props.provider !== null);
const hasServices = computed(() => props.services.length > 0);
const hasBookings = computed(() => props.recentBookings.length > 0);

// Quick actions based on setup status
const quickActions = computed(() => {
    const actions = [];
    
    if (!hasProvider.value) {
        actions.push({
            title: t('app.create_business_profile'),
            description: t('app.setup_your_business_details'),
            href: '/provider/create',
            icon: 'Building2',
            variant: 'default' as const,
            urgent: true
        });
    } else {
        if (!hasServices.value) {
            actions.push({
                title: t('app.add_first_service'),
                description: t('app.create_services_to_offer'),
                href: '/services/create',
                icon: 'Package',
                variant: 'default' as const,
                urgent: true
            });
        } else {
            actions.push({
                title: t('app.add_new_service'),
                description: t('app.expand_your_offerings'),
                href: '/services/create',
                icon: 'Package',
                variant: 'outline' as const,
                urgent: false
            });
        }
        
        actions.push({
            title: t('app.view_schedule'),
            description: t('app.manage_working_hours'),
            href: '/schedules',
            icon: 'Clock',
            variant: 'outline' as const,
            urgent: false
        });
        
        actions.push({
            title: t('app.today_bookings'),
            description: t('app.check_todays_appointments'),
            href: '/provider/dashboard',
            icon: 'Calendar',
            variant: 'outline' as const,
            urgent: false
        });
    }
    
    return actions;
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
    <Head :title="t('app.business_dashboard')" />

    <AppLayoutWithNavigation class="bg-tg-bg">
        <div class="flex min-h-full flex-col">
            <!-- Enhanced Header with Gradient Background -->
            <div class="relative overflow-hidden bg-gradient-to-r from-tg-accent to-purple-600 px-4 py-6">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjAuNSIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+')] opacity-10"></div>
                <div class="relative flex items-center justify-between">
                    <div class="max-w-[80%]">
                        <h1 class="text-2xl font-bold text-white">
                            {{ hasProvider ? t('app.welcome_back') : t('app.welcome_to_horario') }}
                        </h1>
                        <p class="mt-1 text-sm text-white/90 truncate">
                            {{ hasProvider ? provider?.business_name : t('app.setup_your_business') }}
                        </p>
                    </div>
                    <Link :href="'/settings/profile'" class="rounded-full bg-white/20 p-2 backdrop-blur-sm transition-all hover:bg-white/30">
                        <Icon name="Settings" class="h-5 w-5 text-white" />
                    </Link>
                </div>
            </div>

            <!-- Enhanced Stats Cards with Animations -->
            <div v-if="hasProvider" class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-3">
                <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-shadow duration-300">
                    <CardContent class="p-5 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-tg-accent/10">
                            <Icon name="Calendar" class="h-6 w-6 text-tg-accent" />
                        </div>
                        <div class="text-3xl font-bold text-tg-text">{{ todaysBookings }}</div>
                        <div class="text-sm font-medium text-tg-hint">{{ t('app.today') }}</div>
                    </CardContent>
                </Card>
                
                <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-shadow duration-300">
                    <CardContent class="p-5 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-green-500/10">
                            <Icon name="TrendingUp" class="h-6 w-6 text-green-600" />
                        </div>
                        <div class="text-3xl font-bold text-tg-text">{{ weeklyBookings }}</div>
                        <div class="text-sm font-medium text-tg-hint">{{ t('app.this_week') }}</div>
                    </CardContent>
                </Card>
                
                <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-shadow duration-300">
                    <CardContent class="p-5 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10">
                            <Icon name="DollarSign" class="h-6 w-6 text-blue-600" />
                        </div>
                        <div class="text-3xl font-bold text-tg-text">{{ monthlyRevenue }}</div>
                        <div class="text-sm font-medium text-tg-hint">{{ t('app.this_month') }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Enhanced Quick Actions with Better Visual Hierarchy -->
            <div class="flex-1 p-4">
                <div class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-tg-text">
                            {{ hasProvider ? t('app.quick_actions') : t('app.get_started') }}
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

                <!-- Enhanced Recent Activity with Better Visual Design -->
                <div v-if="hasProvider && hasBookings" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-tg-text">{{ t('app.recent_bookings') }}</h2>
                        <Link :href="'/provider/bookings'" class="text-sm font-medium text-tg-accent hover:underline">
                            {{ t('app.view_all') }}
                        </Link>
                    </div>
                    <div class="space-y-3">
                        <Card 
                            v-for="booking in recentBookings.slice(0, 3)" 
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
                                            <p class="text-sm text-tg-hint">{{ booking.user.name }}</p>
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
                                                    'bg-red-500/20 text-red-700': booking.status === 'declined'
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

                <!-- Enhanced Services Overview with Visual Cards -->
                <div v-if="hasProvider" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-tg-text">{{ t('app.your_services') }}</h2>
                        <Link :href="'/services'" class="text-sm font-medium text-tg-accent hover:underline">
                            {{ t('app.manage') }}
                        </Link>
                    </div>
                    
                    <div v-if="hasServices" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Card 
                            v-for="service in services.slice(0, 4)" 
                            :key="service.id"
                            class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5"
                        >
                            <CardContent class="p-5">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-tg-text">{{ service.title }}</h4>
                                        <div class="mt-2 flex items-center gap-2 text-sm text-tg-hint">
                                            <Icon name="Clock" class="h-4 w-4" />
                                            <span>{{ service.duration_minutes }} {{ t('app.minutes') }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-tg-accent">{{ service.display_price }}</div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                    
                    <div v-else class="rounded-xl border border-dashed border-tg-section-separator bg-gradient-to-br from-tg-section-bg to-white p-8 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-tg-accent/10">
                            <Icon name="Package" class="h-8 w-8 text-tg-accent" />
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-tg-text">{{ t('app.no_services_yet') }}</h3>
                        <p class="mb-4 text-sm text-tg-hint">{{ t('app.start_by_adding_service') }}</p>
                        <Button as="a" href="/services/create" class="bg-tg-accent text-white hover:bg-tg-accent/90">
                            <Icon name="Plus" class="mr-2 h-4 w-4" />
                            {{ t('app.add_first_service') }}
                        </Button>
                    </div>
                </div>
            </div>

        </div>
    </AppLayoutWithNavigation>
</template>