<template>
    <Head :title="t('app.page_links')" />

    <AppLayoutWithNavigation :title="t('app.page_links')" :subtitle="t('app.review_all_pages')">
        <div class="space-y-6">
            <!-- Main Routes -->
            <div class="space-y-3">
        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.main_routes') }}</h2>
                <div class="grid gap-2">
                    <Button @click="visitUrl('/')" variant="outline" class="justify-start">
                        <Icon name="Home" class="mr-2 h-4 w-4" />
                        {{ t('app.home') }} (/)
                    </Button>
                    <Button @click="visitUrl('/start')" variant="outline" class="justify-start">
                        <Icon name="Play" class="mr-2 h-4 w-4" />
                        {{ t('app.start') }} (/start)
                    </Button>
                    <Button @click="visitUrl('/tg')" variant="outline" class="justify-start">
                        <Icon name="MessageCircle" class="mr-2 h-4 w-4" />
                        {{ t('app.telegram_home') }} (/tg)
                    </Button>
                    <Button @click="visitUrl('/dashboard')" variant="outline" class="justify-start">
                        <Icon name="Layout" class="mr-2 h-4 w-4" />
                        {{ t('app.dashboard') }} (/dashboard)
                    </Button>
                </div>
            </div>

            <!-- Provider Routes -->
            <div class="space-y-3">
        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.provider_routes') }}</h2>
                <div class="grid gap-2">
                    <Button @click="visitUrl('/provider/create')" variant="outline" class="justify-start">
                        <Icon name="UserPlus" class="mr-2 h-4 w-4" />
                        {{ t('app.create_provider') }} (/provider/create)
                    </Button>
                    <Button @click="visitUrl('/provider/profile')" variant="outline" class="justify-start">
                        <Icon name="User" class="mr-2 h-4 w-4" />
                        {{ t('app.provider_profile') }} (/provider/profile)
                    </Button>
                    <Button @click="visitUrl('/provider/dashboard')" variant="outline" class="justify-start">
                        <Icon name="BarChart3" class="mr-2 h-4 w-4" />
                        {{ t('app.provider_dashboard') }} (/provider/dashboard)
                    </Button>
                    <Button @click="visitUrl('/provider/bookings')" variant="outline" class="justify-start">
                        <Icon name="Calendar" class="mr-2 h-4 w-4" />
                        {{ t('app.provider_bookings') }} (/provider/bookings)
                    </Button>
                    <Button @click="visitUrl('/provider/edit')" variant="outline" class="justify-start">
                        <Icon name="Edit" class="mr-2 h-4 w-4" />
                        {{ t('app.edit_provider') }} (/provider/edit)
                    </Button>
                </div>
            </div>

            <!-- Service Routes -->
            <div class="space-y-3">
        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.service_routes') }}</h2>
                <div class="grid gap-2">
                    <Button @click="visitUrl('/services')" variant="outline" class="justify-start">
                        <Icon name="Package" class="mr-2 h-4 w-4" />
                        {{ t('app.services') }} (/services)
                    </Button>
                    <Button @click="visitUrl('/services/create')" variant="outline" class="justify-start">
                        <Icon name="Plus" class="mr-2 h-4 w-4" />
                        {{ t('app.create_service') }} (/services/create)
                    </Button>
                    <Button @click="visitUrl('/services/1/edit')" variant="outline" class="justify-start">
                        <Icon name="Edit" class="mr-2 h-4 w-4" />
                        {{ t('app.edit_service') }} (/services/1/edit)
                    </Button>
                </div>
            </div>

            <!-- Schedule Routes -->
            <div class="space-y-3">
        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.schedule_routes') }}</h2>
                <div class="grid gap-2">
                    <Button @click="visitUrl('/schedules')" variant="outline" class="justify-start">
                        <Icon name="Clock" class="mr-2 h-4 w-4" />
                        {{ t('app.schedules') }} (/schedules)
                    </Button>
                </div>
            </div>

            <!-- Booking Routes -->
            <div class="space-y-3">
        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.booking_routes') }}</h2>
                <div class="grid gap-2">
                    <Button @click="visitUrl('/bookings')" variant="outline" class="justify-start">
                        <Icon name="Calendar" class="mr-2 h-4 w-4" />
                        {{ t('app.my_bookings') }} (/bookings)
                    </Button>
                    <Button @click="visitUrl('/bookings/upcoming')" variant="outline" class="justify-start">
                        <Icon name="Clock" class="mr-2 h-4 w-4" />
                        {{ t('app.upcoming_bookings') }} (/bookings/upcoming)
                    </Button>
                    <Button @click="visitUrl('/bookings/past')" variant="outline" class="justify-start">
                        <Icon name="History" class="mr-2 h-4 w-4" />
                        {{ t('app.past_bookings') }} (/bookings/past)
                    </Button>
                    <Button @click="visitUrl('/bookings/1')" variant="outline" class="justify-start">
                        <Icon name="Eye" class="mr-2 h-4 w-4" />
                        {{ t('app.view_booking') }} (/bookings/1)
                    </Button>
                    <Button @click="visitUrl('/services/1/book')" variant="outline" class="justify-start">
                        <Icon name="Plus" class="mr-2 h-4 w-4" />
                        {{ t('app.book_service') }} (/services/1/book)
                    </Button>
                </div>
            </div>

            <!-- Search Routes -->
            <div class="space-y-3">
        <h2 class="text-lg font-semibold text-tg-text">{{ t('app.search_routes') }}</h2>
                <div class="grid gap-2">
                    <Button @click="visitUrl('/search')" variant="outline" class="justify-start">
                        <Icon name="Search" class="mr-2 h-4 w-4" />
                        {{ t('app.search') }} (/search)
                    </Button>
                    <Button @click="visitUrl('/search/results?q=test')" variant="outline" class="justify-start">
                        <Icon name="Search" class="mr-2 h-4 w-4" />
                        {{ t('app.search_results') }} (/search/results?q=test)
                    </Button>
                    <Button @click="visitUrl('/search/provider/1')" variant="outline" class="justify-start">
                        <Icon name="User" class="mr-2 h-4 w-4" />
                        {{ t('app.provider_services') }} (/search/provider/1)
                    </Button>
                </div>
            </div>

            <!-- Note -->
            <div class="rounded-lg bg-tg-section-bg p-4">
                <p class="text-sm text-tg-subtitle-text">
                    <Icon name="Info" class="mr-1 inline h-4 w-4" />
                    {{ t('app.page_links_note') }}
                </p>
            </div>
        </div>
    </AppLayoutWithNavigation>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Button } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, router } from '@inertiajs/vue3';

const { t } = useTranslations();

const visitUrl = (path: string) => {
    router.visit(path);
};
</script>
