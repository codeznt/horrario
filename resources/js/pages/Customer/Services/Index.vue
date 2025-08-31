<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

// Icons
import { 
    Search, 
    Clock, 
    Star,
    ArrowLeft
} from 'lucide-vue-next';

const { t } = useTranslations();

// Props interface
interface Provider {
    id: number;
    business_name: string;
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
    services: {
        data: Service[];
        links: any[];
        meta: any;
    };
    filters: {
        search: string;
        category: string;
    };
}

const props = defineProps<Props>();

// Search functionality
const searchQuery = ref(props.filters.search);

const performSearch = () => {
    router.get('/browse/services', {
        search: searchQuery.value,
        category: props.filters.category,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearSearch = () => {
    searchQuery.value = '';
    performSearch();
};

// Computed values
const hasServices = computed(() => (props.services?.data?.length || 0) > 0);
const hasFilters = computed(() => props.filters?.search || props.filters?.category);
</script>

<template>
    <Head :title="t('app.browse_services')" />

    <AppLayoutWithNavigation class="bg-tg-bg">
        <div class="flex min-h-full flex-col">
            <!-- Header -->
            <div class="border-b border-tg-section-separator bg-tg-secondary-bg px-4 py-4">
                <div class="flex items-center gap-3 mb-4">
                    <Link :href="'/customer/dashboard'" class="rounded-full bg-tg-accent/10 p-2">
                        <ArrowLeft class="h-5 w-5 text-tg-accent" />
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-tg-text">
                            {{ t('app.browse_services') }}
                        </h1>
                        <p class="text-sm text-tg-hint">
                            {{ t('app.discover_local_services') }}
                        </p>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="flex gap-2">
                    <div class="flex-1 relative">
                        <Input
                            v-model="searchQuery"
                            :placeholder="t('app.search_services_providers')"
                            class="pl-10 bg-tg-section-bg border-tg-section-separator text-tg-text"
                            @keydown.enter="performSearch"
                        />
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-tg-hint" />
                    </div>
                    <Button @click="performSearch" class="bg-tg-accent text-white hover:bg-tg-accent/90">
                        <Search class="h-4 w-4" />
                    </Button>
                </div>

                <!-- Active Filters -->
                <div v-if="hasFilters" class="flex flex-wrap gap-2 mt-3">
                    <div v-if="filters.search" class="flex items-center gap-1 bg-tg-accent/10 text-tg-accent px-2 py-1 rounded-full text-sm">
                        <span>{{ filters.search }}</span>
                        <button @click="clearSearch" class="hover:bg-tg-accent/20 rounded-full p-0.5">
                            <span class="sr-only">Clear search</span>
                            ×
                        </button>
                    </div>
                </div>
            </div>

            <!-- Services Grid -->
            <div class="flex-1 p-4">
                <div v-if="!hasServices && hasFilters" class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-tg-section-bg">
                        <Search class="h-8 w-8 text-tg-hint" />
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-tg-text">{{ t('app.no_results_found') }}</h3>
                    <p class="mb-4 text-sm text-tg-hint">{{ t('app.try_different_search') }}</p>
                    <Button @click="clearSearch" variant="outline">
                        {{ t('app.clear_search') }}
                    </Button>
                </div>

                <div v-else-if="!hasServices" class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-tg-section-bg">
                        <Star class="h-8 w-8 text-tg-hint" />
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-tg-text">{{ t('app.no_services_available') }}</h3>
                    <p class="text-sm text-tg-hint">{{ t('app.check_back_later') }}</p>
                </div>

                <div v-else class="space-y-4">
                    <!-- Results Count -->
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-tg-hint">
                            {{ t('app.showing_results', { 
                                count: services?.data?.length || 0, 
                                total: services?.meta?.total || 0 
                            }) }}
                        </p>
                    </div>

                    <!-- Services List -->
                    <div class="space-y-3">
                        <Link
                            v-for="service in services.data"
                            :key="service.id"
                            :href="`/browse/services/${service.id}`"
                            class="block"
                        >
                            <Card class="border-tg-section-separator bg-tg-section-bg hover:bg-tg-secondary-bg transition-colors">
                                <CardContent class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-tg-text text-lg">{{ service.title }}</h3>
                                            <p class="text-sm text-tg-accent font-medium">{{ service.provider.business_name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-tg-text">{{ service.display_price }}</div>
                                        </div>
                                    </div>
                                    
                                    <div v-if="service.description" class="mb-3">
                                        <p class="text-sm text-tg-hint line-clamp-2">{{ service.description }}</p>
                                    </div>
                                    
                                    <div class="flex items-center gap-4 text-xs text-tg-hint">
                                        <div class="flex items-center gap-1">
                                            <Clock class="h-3 w-3" />
                                            <span>{{ service.duration_minutes }} {{ t('app.minutes') }}</span>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="services.links.length > 3" class="flex justify-center gap-1 mt-6">
                        <template v-for="link in services.links" :key="link.label">
                            <Button
                                v-if="link.url"
                                @click="router.get(link.url)"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                class="min-w-[40px]"
                                v-html="link.label"
                            />
                            <Button
                                v-else
                                variant="ghost"
                                size="sm"
                                disabled
                                class="min-w-[40px]"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>

            <!-- Bottom Navigation -->
            <div class="border-t border-tg-section-separator bg-tg-secondary-bg p-4">
                <div class="grid grid-cols-4 gap-2">
                    <Link :href="'/customer/dashboard'" class="flex flex-col items-center gap-1 rounded-lg p-3 transition-colors hover:bg-tg-section-bg">
                        <ArrowLeft class="h-5 w-5 text-tg-hint" />
                        <span class="text-xs text-tg-hint">{{ t('app.back') }}</span>
                    </Link>
                    
                    <Link :href="'/browse/services'" class="flex flex-col items-center gap-1 rounded-lg p-3 bg-tg-accent/10">
                        <Search class="h-5 w-5 text-tg-accent" />
                        <span class="text-xs font-medium text-tg-accent">{{ t('app.services') }}</span>
                    </Link>
                    
                    <Link :href="'/bookings'" class="flex flex-col items-center gap-1 rounded-lg p-3 transition-colors hover:bg-tg-section-bg">
                        <Clock class="h-5 w-5 text-tg-hint" />
                        <span class="text-xs text-tg-hint">{{ t('app.bookings') }}</span>
                    </Link>
                    
                    <Link :href="'/favorites'" class="flex flex-col items-center gap-1 rounded-lg p-3 transition-colors hover:bg-tg-section-bg">
                        <Star class="h-5 w-5 text-tg-hint" />
                        <span class="text-xs text-tg-hint">{{ t('app.favorites') }}</span>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayoutWithNavigation>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>