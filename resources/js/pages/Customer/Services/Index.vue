<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

// Icons
import Icon from '@/components/Icon.vue';

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
            <!-- Enhanced Header with Gradient Background -->
            <div class="relative overflow-hidden bg-gradient-to-r from-tg-link to-blue-500 px-4 py-5">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjAuNSIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+')] opacity-10"></div>
                <div class="relative flex items-center gap-3 mb-4">
                    <Link :href="'/customer/dashboard'" class="rounded-full bg-white/20 p-2 backdrop-blur-sm transition-all hover:bg-white/30">
                        <Icon name="ArrowLeft" class="h-5 w-5 text-white" />
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-white">
                            {{ t('app.browse_services') }}
                        </h1>
                        <p class="text-sm text-white/90">
                            {{ t('app.discover_local_services') }}
                        </p>
                    </div>
                </div>

                <!-- Enhanced Search Bar -->
                <div class="relative">
                    <Input
                        v-model="searchQuery"
                        :placeholder="t('app.search_services_providers')"
                        class="pl-10 pr-20 bg-white/90 border-transparent text-tg-text placeholder:text-tg-hint focus:ring-2 focus:ring-white/30"
                        @keydown.enter="performSearch"
                    />
                    <Icon name="Search" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-tg-hint" />
                    <Button 
                        @click="performSearch" 
                        size="sm" 
                        class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-tg-link text-white hover:bg-tg-link/90"
                    >
                        {{ t('app.search') }}
                    </Button>
                </div>

                <!-- Active Filters -->
                <div v-if="hasFilters" class="flex flex-wrap gap-2 mt-3">
                    <div v-if="filters.search" class="flex items-center gap-1 bg-white/20 text-white px-3 py-1 rounded-full text-sm">
                        <Icon name="Search" class="h-3 w-3" />
                        <span>{{ filters.search }}</span>
                        <button @click="clearSearch" class="hover:bg-white/20 rounded-full p-0.5">
                            <span class="sr-only">Clear search</span>
                            <Icon name="X" class="h-3 w-3" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Services Grid -->
            <div class="flex-1 p-4">
                <div v-if="!hasServices && hasFilters" class="py-12 text-center">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-tg-link/10">
                        <Icon name="Search" class="h-8 w-8 text-tg-link" />
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-tg-text">{{ t('app.no_results_found') }}</h3>
                    <p class="mb-5 text-tg-hint max-w-md mx-auto">{{ t('app.try_different_search') }}</p>
                    <Button @click="clearSearch" variant="outline" class="border-tg-section-separator text-tg-text hover:bg-tg-section-separator/20">
                        {{ t('app.clear_search') }}
                    </Button>
                </div>

                <div v-else-if="!hasServices" class="py-12 text-center">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-tg-section-separator/20">
                        <Icon name="Star" class="h-8 w-8 text-tg-hint" />
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-tg-text">{{ t('app.no_services_available') }}</h3>
                    <p class="text-tg-hint max-w-md mx-auto">{{ t('app.check_back_later') }}</p>
                </div>

                <div v-else class="space-y-5">
                    <!-- Results Count -->
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-tg-hint">
                            {{ t('app.showing_results', { 
                                count: services?.data?.length || 0, 
                                total: services?.meta?.total || 0 
                            }) }}
                        </p>
                    </div>

                    <!-- Enhanced Services List -->
                    <div class="space-y-4">
                        <Link
                            v-for="service in services.data"
                            :key="service.id"
                            :href="`/browse/services/${service.id}`"
                            class="block transition-all duration-300 hover:-translate-y-0.5"
                        >
                            <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm hover:shadow-md">
                                <CardContent class="p-5">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h3 class="font-bold text-tg-text text-lg mb-1">{{ service.title }}</h3>
                                            <p class="text-sm text-tg-link font-medium">{{ service.provider.business_name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-bold text-tg-link">{{ service.display_price }}</div>
                                        </div>
                                    </div>
                                    
                                    <div v-if="service.description" class="mb-4">
                                        <p class="text-sm text-tg-hint line-clamp-2">{{ service.description }}</p>
                                    </div>
                                    
                                    <div class="flex items-center gap-4 text-xs text-tg-hint">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-tg-link/10">
                                                <Icon name="Clock" class="h-3 w-3 text-tg-link" />
                                            </div>
                                            <span>{{ service.duration_minutes }} {{ t('app.minutes') }}</span>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </Link>
                    </div>

                    <!-- Enhanced Pagination -->
                    <div v-if="services.links.length > 3" class="flex justify-center gap-1 mt-6">
                        <template v-for="link in services.links" :key="link.label">
                            <Button
                                v-if="link.url"
                                @click="router.get(link.url)"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                class="min-w-[40px] border-tg-section-separator"
                                :class="link.active ? 'bg-tg-link text-white hover:bg-tg-link/90' : 'bg-white text-tg-text hover:bg-tg-section-separator/20'"
                            >
                                {{ link.label }}
                            </Button>
                            <Button
                                v-else
                                variant="ghost"
                                size="sm"
                                disabled
                                class="min-w-[40px] text-tg-hint"
                            >
                                {{ link.label }}
                            </Button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayoutWithNavigation>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>