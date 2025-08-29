<template>
    <AppShell>
        <div class="mx-auto max-w-4xl px-4 py-6">
            <Heading :title="t('app.search_results')"></Heading>

            <!-- Search Form -->
            <div class="mt-4 mb-6">
                <SearchForm :initial-query="filters.query" :initial-category="filters.category" :initial-sort="filters.sort" />
            </div>

            <!-- Search Summary -->
            <div class="mb-6">
                <p v-if="filters.query" class="text-tg-subtitle-text">
                    {{ totalResults }} {{ t('app.results_found') }} {{ t('app.search_for') }} "{{ filters.query }}"
                    <span v-if="filters.category"> {{ t('app.in') }} {{ filters.category }}</span>
                </p>
                <p v-else class="text-tg-subtitle-text">
                    {{ totalResults }} {{ t('app.results_found') }}
                    <span v-if="filters.category"> {{ t('app.in') }} {{ filters.category }}</span>
                </p>
            </div>

            <!-- Provider Results (if any) -->
            <div v-if="providers.length > 0" class="mb-8">
                <h2 class="mb-4 text-lg font-medium">{{ t('app.providers') }}</h2>

                <div class="space-y-4">
                    <Card v-for="provider in providers" :key="`provider-${provider.id}`" class="overflow-hidden">
                        <div class="flex items-center p-4">
                            <!-- Provider Image -->
                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-full bg-tg-section-bg">
                                <div v-if="!provider.profile_image" class="h-full w-full">
                                    <PlaceholderPattern :seed="provider.id" class="h-full w-full" />
                                </div>
                                <img v-else :src="provider.profile_image" :alt="provider.business_name" class="h-full w-full object-cover" />
                            </div>

                            <!-- Provider Info -->
                            <div class="ml-4 flex-1">
                                <h3 class="font-medium">{{ provider.business_name }}</h3>
                                <p class="line-clamp-1 text-sm text-tg-subtitle-text">
                                    {{ provider.description || t('app.no_description_provided') }}
                                </p>
                            </div>

                            <!-- View Provider Button -->
                            <Button :href="`/search/provider/${provider.id}`" variant="outline" size="sm">
                                {{ t('app.view_services') }}
                            </Button>
                        </div>
                    </Card>
                </div>
            </div>

            <!-- Service Results -->
            <div>
                <h2 class="mb-4 text-lg font-medium">{{ t('app.services') }}</h2>

                <div v-if="services.data.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <ServiceCard v-for="service in services.data" :key="`service-${service.id}`" :service="service" />
                </div>

                <div v-else class="py-8 text-center">
                    <Icon name="Search" class="mx-auto h-12 w-12 text-tg-hint" />
                    <p class="mt-4 text-tg-subtitle-text">{{ t('app.no_results_found') }}</p>
                    <Button @click="resetSearch" variant="outline" class="mt-4">
                        {{ t('app.clear_search') }}
                    </Button>
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
import SearchForm from '@/components/SearchForm.vue';
import ServiceCard from '@/components/ServiceCard.vue';
import { Button, Card } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

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

interface Filters {
    query: string;
    category: string;
    sort: string;
}

interface Props {
    services: Pagination;
    providers: Provider[];
    filters: Filters;
}

const props = defineProps<Props>();
const { t } = useTranslations();

const totalResults = computed(() => {
    return props.services.total + props.providers.length;
});

function resetSearch() {
    router.get('/search', {});
}

function goToPage(url: string | null) {
    if (!url) return;

    // Extract the page number from the URL
    const urlObj = new URL(url);
    const page = urlObj.searchParams.get('page');

    router.get(
        '/search',
        {
            query: props.filters.query,
            category: props.filters.category,
            sort: props.filters.sort,
            page,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['services'],
        },
    );
}
</script>
