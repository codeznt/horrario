<template>
    <AppShell>
        <div class="mx-auto max-w-4xl px-4 py-6">
            <!-- Back Button -->
            <div class="mb-4">
                <Button @click="router.get('/search')" variant="ghost" size="sm" class="flex items-center">
                    <Icon name="ArrowLeft" class="mr-1 h-4 w-4" />
                    {{ t('app.back') }} {{ t('app.search') }}
                </Button>
            </div>

            <!-- Provider Header -->
            <div class="mb-6 flex flex-col items-start gap-4 md:flex-row md:items-center">
                <!-- Provider Image -->
                <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-full bg-tg-section-bg">
                    <div v-if="!provider.profile_image" class="h-full w-full">
                        <PlaceholderPattern :seed="provider.id" class="h-full w-full" />
                    </div>
                    <img v-else :src="provider.profile_image" :alt="provider.business_name" class="h-full w-full object-cover" />
                </div>

                <!-- Provider Info -->
                <div class="flex-1">
                    <Heading :title="provider.business_name"></Heading>
                    <p class="mt-1 text-tg-subtitle-text">
                        {{ provider.description || t('app.no_description_provided') }}
                    </p>
                </div>
            </div>

            <!-- Services List -->
            <div class="mt-8">
                <h2 class="mb-4 text-lg font-medium">{{ t('app.available_services') }}</h2>

                <div v-if="services.data.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <ServiceCard v-for="service in services.data" :key="`service-${service.id}`" :service="service" />
                </div>

                <div v-else class="py-8 text-center">
                    <Icon name="Calendar" class="mx-auto h-12 w-12 text-tg-hint" />
                    <p class="mt-4 text-tg-subtitle-text">{{ t('app.provider_no_available_services') }}</p>
                </div>

                <!-- Pagination -->
                <div v-if="services.data.length > 0" class="mt-8 flex justify-center">
                    <div class="flex space-x-2">
                        <Button
                            v-for="link in services.links"
                            :key="link.label"
                            :disabled="!link.url || link.active"
                            :variant="link.active ? 'default' : 'outline'"
                            @click="goToPage(link.url)"
                            class="flex h-10 w-10 items-center justify-center p-0"
                        >
                            <span v-html="link.label" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppShell>
</template>

<script setup lang="ts">
import AppShell from '@/components/AppShell.vue';
import Heading from '@/components/Heading.vue';
import Icon from '@/components/Icon.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import ServiceCard from '@/components/ServiceCard.vue';
import { Button } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import { router } from '@inertiajs/vue3';

interface Provider {
    id: number;
    business_name: string;
    description: string | null;
    profile_image?: string;
}

interface Service {
    id: number;
    title: string;
    description: string | null;
    duration_minutes: number;
    display_price: string;
    provider: Provider;
    profile_image?: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Pagination {
    data: Service[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
}

interface Props {
    provider: Provider;
    services: Pagination;
}

const props = defineProps<Props>();
const { t } = useTranslations();

function goToPage(url: string | null) {
    if (!url) return;

    // Extract the page number from the URL
    const urlObj = new URL(url);
    const page = urlObj.searchParams.get('page');

    router.get(
        `/search/provider/${props.provider.id}`,
        { page },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['services'],
        },
    );
}
</script>
