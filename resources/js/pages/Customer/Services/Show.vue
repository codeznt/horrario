<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, Link, router } from '@inertiajs/vue3';

// shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

// Icon component
import Icon from '@/components/Icon.vue';


const { t } = useTranslations();

// Props interface
interface Provider {
    id: number;
    business_name: string;
    description: string | null;
    contact_phone: string | null;
    address: string | null;
}

interface Service {
    id: number;
    title: string;
    description: string | null;
    duration_minutes: number;
    display_price: string;
    provider: Provider;
}

interface Props {
    service: Service;
}

const props = defineProps<Props>();

const bookService = () => {
    router.get(`/services/${props.service.id}/book`);
};
</script>

<template>
    <Head :title="`${service.title} - ${service.provider.business_name}`" />

    <AppLayoutWithNavigation class="bg-tg-bg">
        <div class="flex min-h-[--spacing-viewport-h] flex-col">
            <!-- Header -->
            <div class="border-b border-tg-section-separator bg-tg-secondary-bg px-4 py-4">
                <div class="flex items-center gap-3">
                    <Link :href="'/browse/services'" class="rounded-full bg-tg-accent/10 p-2">
                        <Icon name="ArrowLeft" class="h-5 w-5 text-tg-accent" />
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-tg-text">{{ service.title }}</h1>
                        <p class="text-sm text-tg-accent font-medium">{{ service.provider.business_name }}</p>
                    </div>
                    <Button variant="ghost" size="sm" class="rounded-full bg-tg-accent/10 p-2">
                        <Icon name="Heart" class="h-5 w-5 text-tg-accent" />
                    </Button>
                </div>
            </div>

            <!-- Service Details -->
            <div class="flex-1 p-4 space-y-4">
                <!-- Service Info Card -->
                <Card class="border-tg-section-separator bg-tg-section-bg">
                    <CardHeader>
                        <CardTitle class="text-tg-text">{{ service.title }}</CardTitle>
                        <CardDescription v-if="service.description" class="text-tg-hint">
                            {{ service.description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Price and Duration -->
                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center gap-2 text-tg-hint">
                                <Icon name="Clock" class="h-5 w-5" />
                                <span>{{ service.duration_minutes }} {{ t('app.minutes') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Icon name="DollarSign" class="h-5 w-5 text-tg-accent" />
                                <span class="text-xl font-bold text-tg-text">{{ service.display_price }}</span>
                            </div>
                        </div>

                        <!-- Book Button -->
                        <Button @click="bookService" class="w-full bg-tg-accent text-white hover:bg-tg-accent/90">
                            <Icon name="Calendar" class="mr-2 h-4 w-4" />
                            {{ t('app.book_now') }}
                        </Button>
                    </CardContent>
                </Card>

                <!-- Provider Info Card -->
                <Card class="border-tg-section-separator bg-tg-section-bg">
                    <CardHeader>
                        <CardTitle class="text-tg-text">{{ t('app.about_provider') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div>
                            <h4 class="font-semibold text-tg-text">{{ service.provider.business_name }}</h4>
                            <p v-if="service.provider.description" class="text-sm text-tg-hint mt-1">
                                {{ service.provider.description }}
                            </p>
                        </div>

                        <!-- Contact Info -->
                        <div class="space-y-2">
                            <div v-if="service.provider.contact_phone" class="flex items-center gap-2 text-sm text-tg-hint">
                                <Icon name="Phone" class="h-4 w-4" />
                                <span>{{ service.provider.contact_phone }}</span>
                            </div>
                            <div v-if="service.provider.address" class="flex items-center gap-2 text-sm text-tg-hint">
                                <Icon name="MapPin" class="h-4 w-4" />
                                <span>{{ service.provider.address }}</span>
                            </div>
                        </div>

                        <!-- Provider Actions -->
                        <div class="flex gap-2 pt-2">
                            <Button variant="outline" size="sm" class="flex-1">
                                {{ t('app.view_all_services') }}
                            </Button>
                            <Button v-if="service.provider.contact_phone" variant="outline" size="sm" class="flex-1">
                                <Icon name="Phone" class="mr-1 h-3 w-3" />
                                {{ t('app.call') }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Additional Info -->
                <Card class="border-tg-section-separator bg-tg-section-bg">
                    <CardHeader>
                        <CardTitle class="text-tg-text">{{ t('app.booking_info') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3 text-sm text-tg-hint">
                        <div class="flex items-start gap-2">
                            <Icon name="Clock" class="h-4 w-4 mt-0.5 text-tg-accent" />
                            <div>
                                <p class="font-medium text-tg-text">{{ t('app.duration') }}</p>
                                <p>{{ service.duration_minutes }} {{ t('app.minutes_session') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <Icon name="Calendar" class="h-4 w-4 mt-0.5 text-tg-accent" />
                            <div>
                                <p class="font-medium text-tg-text">{{ t('app.booking_policy') }}</p>
                                <p>{{ t('app.booking_confirmation_required') }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Bottom Actions -->
            <div class="border-t border-tg-section-separator bg-tg-secondary-bg p-4">
                <div class="flex gap-3">
                    <Link :href="'/browse/services'" class="flex flex-col items-center gap-1 rounded-lg p-3 transition-colors hover:bg-tg-section-bg">
                        <Icon name="ArrowLeft" class="h-5 w-5 text-tg-hint" />
                        <span class="text-xs text-tg-hint">{{ t('app.back') }}</span>
                    </Link>
                    
                    <Button @click="bookService" class="flex-1 bg-tg-accent text-white hover:bg-tg-accent/90">
                        <Icon name="Calendar" class="mr-2 h-4 w-4" />
                        {{ t('app.book_this_service') }}
                    </Button>
                </div>
            </div>
        </div>
    </AppLayoutWithNavigation>
</template>