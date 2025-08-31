<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

// Icons
import { 
    Building2, 
    Calendar, 
    Clock, 
    DollarSign, 
    Package, 
    Settings, 
    TrendingUp, 
    Users 
} from 'lucide-vue-next';

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
            icon: Building2,
            variant: 'default' as const,
            urgent: true
        });
    } else {
        if (!hasServices.value) {
            actions.push({
                title: t('app.add_first_service'),
                description: t('app.create_services_to_offer'),
                href: '/services/create',
                icon: Package,
                variant: 'default' as const,
                urgent: true
            });
        } else {
            actions.push({
                title: t('app.add_new_service'),
                description: t('app.expand_your_offerings'),
                href: '/services/create',
                icon: Package,
                variant: 'outline' as const,
                urgent: false
            });
        }
        
        actions.push({
            title: t('app.view_schedule'),
            description: t('app.manage_working_hours'),
            href: '/schedules',
            icon: Clock,
            variant: 'outline' as const,
            urgent: false
        });
        
        actions.push({
            title: t('app.today_bookings'),
            description: t('app.check_todays_appointments'),
            href: '/provider/dashboard',
            icon: Calendar,
            variant: 'outline' as const,
            urgent: false
        });
    }
    
    return actions;
});
</script>

<template>
    <Head :title="t('app.business_dashboard')" />

    <TelegramAppLayout class="bg-tg-bg">
        <div class="flex min-h-[--spacing-viewport-h] flex-col">
            <!-- Header -->
            <div class="border-b border-tg-section-separator bg-tg-secondary-bg px-4 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-tg-text">
                            {{ hasProvider ? t('app.welcome_back') : t('app.welcome_to_horario') }}
                        </h1>
                        <p class="mt-1 text-sm text-tg-hint">
                            {{ hasProvider ? provider?.business_name : t('app.setup_your_business') }}
                        </p>
                    </div>
                    <Link :href="'/settings/profile'" class="rounded-full bg-tg-accent/10 p-2">
                        <Settings class="h-5 w-5 text-tg-accent" />
                    </Link>
                </div>
            </div>

            <!-- Stats Cards (only show if provider exists) -->
            <div v-if="hasProvider" class="grid grid-cols-3 gap-3 p-4">
                <Card class="border-tg-section-separator bg-tg-section-bg">
                    <CardContent class="p-4 text-center">
                        <Calendar class="mx-auto mb-2 h-8 w-8 text-tg-accent" />
                        <div class="text-xl font-bold text-tg-text">{{ todaysBookings }}</div>
                        <div class="text-xs text-tg-hint">{{ t('app.today') }}</div>
                    </CardContent>
                </Card>
                
                <Card class="border-tg-section-separator bg-tg-section-bg">
                    <CardContent class="p-4 text-center">
                        <TrendingUp class="mx-auto mb-2 h-8 w-8 text-tg-accent" />
                        <div class="text-xl font-bold text-tg-text">{{ weeklyBookings }}</div>
                        <div class="text-xs text-tg-hint">{{ t('app.this_week') }}</div>
                    </CardContent>
                </Card>
                
                <Card class="border-tg-section-separator bg-tg-section-bg">
                    <CardContent class="p-4 text-center">
                        <DollarSign class="mx-auto mb-2 h-8 w-8 text-tg-accent" />
                        <div class="text-xl font-bold text-tg-text">{{ monthlyRevenue }}</div>
                        <div class="text-xs text-tg-hint">{{ t('app.this_month') }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <div class="flex-1 p-4">
                <div class="mb-6">
                    <h2 class="mb-4 text-lg font-semibold text-tg-text">
                        {{ hasProvider ? t('app.quick_actions') : t('app.get_started') }}
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

                <!-- Recent Activity (if provider exists and has bookings) -->
                <div v-if="hasProvider && hasBookings" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.recent_bookings') }}</h2>
                        <Link :href="'/provider/bookings'" class="text-sm text-tg-accent">
                            {{ t('app.view_all') }}
                        </Link>
                    </div>
                    <div class="space-y-2">
                        <Card 
                            v-for="booking in recentBookings.slice(0, 3)" 
                            :key="booking.id"
                            class="border-tg-section-separator bg-tg-section-bg"
                        >
                            <CardContent class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-tg-text">{{ booking.service.title }}</h4>
                                        <p class="text-sm text-tg-hint">{{ booking.user.name }}</p>
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
                                                'bg-red-500/20 text-red-600': booking.status === 'declined'
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

                <!-- Services Overview (if provider exists) -->
                <div v-if="hasProvider" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.your_services') }}</h2>
                        <Link :href="'/services'" class="text-sm text-tg-accent">
                            {{ t('app.manage') }}
                        </Link>
                    </div>
                    
                    <div v-if="hasServices" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <Card 
                            v-for="service in services.slice(0, 4)" 
                            :key="service.id"
                            class="border-tg-section-separator bg-tg-section-bg"
                        >
                            <CardContent class="p-4">
                                <h4 class="font-medium text-tg-text">{{ service.title }}</h4>
                                <p class="text-sm font-semibold text-tg-accent">{{ service.display_price }}</p>
                            </CardContent>
                        </Card>
                    </div>
                    
                    <div v-else class="rounded-lg border border-dashed border-tg-section-separator p-6 text-center">
                        <Package class="mx-auto mb-2 h-8 w-8 text-tg-hint" />
                        <p class="text-sm text-tg-hint">{{ t('app.no_services_yet') }}</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Navigation -->
            <div class="border-t border-tg-section-separator bg-tg-secondary-bg p-4">
                <div class="grid grid-cols-4 gap-2">
                    <Link :href="'/dashboard'" class="flex flex-col items-center gap-1 rounded-lg p-3 bg-tg-accent/10">
                        <Building2 class="h-5 w-5 text-tg-accent" />
                        <span class="text-xs font-medium text-tg-accent">{{ t('app.dashboard') }}</span>
                    </Link>
                    
                    <Link :href="'/services'" class="flex flex-col items-center gap-1 rounded-lg p-3 transition-colors hover:bg-tg-section-bg">
                        <Package class="h-5 w-5 text-tg-hint" />
                        <span class="text-xs text-tg-hint">{{ t('app.services') }}</span>
                    </Link>
                    
                    <Link :href="'/provider/bookings'" class="flex flex-col items-center gap-1 rounded-lg p-3 transition-colors hover:bg-tg-section-bg">
                        <Calendar class="h-5 w-5 text-tg-hint" />
                        <span class="text-xs text-tg-hint">{{ t('app.bookings') }}</span>
                    </Link>
                    
                    <Link :href="'/schedules'" class="flex flex-col items-center gap-1 rounded-lg p-3 transition-colors hover:bg-tg-section-bg">
                        <Clock class="h-5 w-5 text-tg-hint" />
                        <span class="text-xs text-tg-hint">{{ t('app.schedule') }}</span>
                    </Link>
                </div>
            </div>
        </div>
    </TelegramAppLayout>
</template>