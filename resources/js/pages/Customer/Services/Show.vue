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
            <!-- Enhanced Header with Gradient Background -->
            <div class="relative overflow-hidden bg-gradient-to-r from-tg-link to-blue-500 px-4 py-5">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjAuNSIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+')] opacity-10"></div>
                <div class="relative flex items-center gap-3">
                    <Link :href="'/browse/services'" class="rounded-full bg-white/20 p-2 backdrop-blur-sm transition-all hover:bg-white/30">
                        <Icon name="ArrowLeft" class="h-5 w-5 text-white" />
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-white truncate">{{ service.title }}</h1>
                        <p class="text-sm text-white/90 truncate">{{ service.provider.business_name }}</p>
                    </div>
                    <Button variant="ghost" size="sm" class="rounded-full bg-white/20 p-2 backdrop-blur-sm transition-all hover:bg-white/30">
                        <Icon name="Heart" class="h-5 w-5 text-white" />
                    </Button>
                </div>
            </div>

            <!-- Service Details -->
            <div class="flex-1 p-4 space-y-5">
                <!-- Enhanced Service Info Card -->
                <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm">
                    <CardHeader class="border-b border-tg-section-separator pb-4">
                        <CardTitle class="text-xl font-bold text-tg-text">{{ service.title }}</CardTitle>
                        <CardDescription v-if="service.description" class="text-tg-hint mt-2">
                            {{ service.description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-5 pt-5">
                        <!-- Price and Duration -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 rounded-lg bg-tg-section-separator/20">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-tg-link/10">
                                    <Icon name="Clock" class="h-5 w-5 text-tg-link" />
                                </div>
                                <div>
                                    <div class="text-sm text-tg-hint">{{ t('app.duration') }}</div>
                                    <div class="font-medium text-tg-text">{{ service.duration_minutes }} {{ t('app.minutes') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500/10">
                                    <Icon name="DollarSign" class="h-5 w-5 text-green-600" />
                                </div>
                                <div>
                                    <div class="text-sm text-tg-hint">{{ t('app.price') }}</div>
                                    <div class="text-xl font-bold text-tg-text">{{ service.display_price }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Book Button -->
                        <Button @click="bookService" size="lg" class="w-full bg-tg-link text-white hover:bg-tg-link/90 py-6 rounded-xl">
                            <Icon name="Calendar" class="mr-2 h-5 w-5" />
                            {{ t('app.book_now') }}
                        </Button>
                    </CardContent>
                </Card>

                <!-- Enhanced Provider Info Card -->
                <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm">
                    <CardHeader class="border-b border-tg-section-separator pb-4">
                        <CardTitle class="text-lg font-bold text-tg-text">{{ t('app.about_provider') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div>
                            <h4 class="text-lg font-bold text-tg-text">{{ service.provider.business_name }}</h4>
                            <p v-if="service.provider.description" class="text-tg-hint mt-2">
                                {{ service.provider.description }}
                            </p>
                        </div>

                        <!-- Contact Info -->
                        <div class="space-y-3">
                            <div v-if="service.provider.contact_phone" class="flex items-center gap-3 p-3 rounded-lg bg-tg-section-separator/20">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-tg-link/10">
                                    <Icon name="Phone" class="h-5 w-5 text-tg-link" />
                                </div>
                                <div>
                                    <div class="text-sm text-tg-hint">{{ t('app.phone') }}</div>
                                    <div class="font-medium text-tg-text">{{ service.provider.contact_phone }}</div>
                                </div>
                            </div>
                            <div v-if="service.provider.address" class="flex items-start gap-3 p-3 rounded-lg bg-tg-section-separator/20">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-tg-link/10 flex-shrink-0 mt-0.5">
                                    <Icon name="MapPin" class="h-5 w-5 text-tg-link" />
                                </div>
                                <div>
                                    <div class="text-sm text-tg-hint">{{ t('app.address') }}</div>
                                    <div class="font-medium text-tg-text">{{ service.provider.address }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Provider Actions -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <Button variant="outline" class="flex-1 border-tg-section-separator text-tg-text hover:bg-tg-section-separator/20">
                                {{ t('app.view_all_services') }}
                            </Button>
                            <Button v-if="service.provider.contact_phone" variant="outline" class="flex-1 border-tg-section-separator text-tg-text hover:bg-tg-section-separator/20">
                                <Icon name="Phone" class="mr-2 h-4 w-4" />
                                {{ t('app.call') }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Enhanced Additional Info -->
                <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm">
                    <CardHeader class="border-b border-tg-section-separator pb-4">
                        <CardTitle class="text-lg font-bold text-tg-text">{{ t('app.booking_info') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-tg-section-separator/20">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-tg-link/10 flex-shrink-0 mt-0.5">
                                <Icon name="Clock" class="h-5 w-5 text-tg-link" />
                            </div>
                            <div>
                                <p class="font-medium text-tg-text">{{ t('app.duration') }}</p>
                                <p class="text-tg-hint">{{ service.duration_minutes }} {{ t('app.minutes_session') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-tg-section-separator/20">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-tg-link/10 flex-shrink-0 mt-0.5">
                                <Icon name="Calendar" class="h-5 w-5 text-tg-link" />
                            </div>
                            <div>
                                <p class="font-medium text-tg-text">{{ t('app.booking_policy') }}</p>
                                <p class="text-tg-hint">{{ t('app.booking_confirmation_required') }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Enhanced Bottom Actions -->
            <div class="border-t border-tg-section-separator bg-tg-secondary-bg p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <Link :href="'/browse/services'" class="flex items-center justify-center gap-2 rounded-xl p-3 transition-colors bg-tg-section-bg text-tg-text hover:bg-tg-section-separator/20">
                        <Icon name="ArrowLeft" class="h-5 w-5" />
                        <span>{{ t('app.back_to_services') }}</span>
                    </Link>
                    
                    <Button @click="bookService" size="lg" class="flex-1 bg-tg-link text-white hover:bg-tg-link/90 py-6 rounded-xl">
                        <Icon name="Calendar" class="mr-2 h-5 w-5" />
                        {{ t('app.book_this_service') }}
                    </Button>
                </div>
            </div>
        </div>
    </AppLayoutWithNavigation>
</template>