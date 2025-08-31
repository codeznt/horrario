<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

// Icons
import Icon from '@/components/Icon.vue';

const { t } = useTranslations();

// Props interface
interface Provider {
    id: number;
    business_name: string;
    description: string | null;
    address: string | null;
    contact_phone: string | null;
}

interface FavoriteItem {
    id: number;
    provider: Provider;
    favorited_at: string;
}

interface Props {
    favorites: {
        data: FavoriteItem[];
        links: any[];
        meta: any;
    };
}

defineProps<Props>();

// Loading state for favorite removal
const removingFavorite = ref<number | null>(null);

const removeFavorite = (providerId: number, favoriteId: number) => {
    removingFavorite.value = favoriteId;
    
    router.delete(`/favorites/${providerId}`, {
        onFinish: () => {
            removingFavorite.value = null;
        },
    });
};

const viewProvider = (providerId: number) => {
    // Navigate to provider services or profile
    router.get(`/providers/${providerId}`);
};
</script>

<template>
    <Head :title="t('app.my_favorites')" />

    <AppLayoutWithNavigation class="bg-tg-bg">
        <div class="flex min-h-full flex-col">
            <!-- Header -->
            <div class="border-b border-tg-section-separator bg-tg-secondary-bg px-4 py-4">
                <div class="flex items-center gap-3">
                    <Link :href="'/customer/dashboard'" class="rounded-full bg-tg-accent/10 p-2">
                        <Icon name="ArrowLeft" class="h-5 w-5 text-tg-accent" />
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-tg-text">{{ t('app.my_favorites') }}</h1>
                        <p class="text-sm text-tg-hint">{{ t('app.saved_providers') }}</p>
                    </div>
                    <div class="flex items-center gap-1 bg-tg-accent/10 px-2 py-1 rounded-full">
                        <Icon name="Heart" class="h-4 w-4 text-tg-accent" />
                        <span class="text-sm font-medium text-tg-accent">{{ favorites?.meta?.total || 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Favorites List -->
            <div class="flex-1 p-4">
                <div v-if="(favorites?.data?.length || 0) === 0" class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-tg-section-bg">
                        <Icon name="Heart" class="h-8 w-8 text-tg-hint" />
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-tg-text">{{ t('app.no_favorites_yet') }}</h3>
                    <p class="mb-4 text-sm text-tg-hint px-4">{{ t('app.favorite_providers_desc') }}</p>
                    <Button @click="router.visit('/browse/services')" class="bg-tg-accent text-white hover:bg-tg-accent/90">
                        <Icon name="Search" class="mr-2 h-4 w-4" />
                        {{ t('app.browse_services') }}
                    </Button>
                </div>

                <div v-else class="space-y-3">
                    <Card
                        v-for="favorite in (favorites?.data || [])"
                        :key="favorite.id"
                        class="border-tg-section-separator bg-tg-section-bg"
                    >
                        <CardContent class="p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Icon name="Heart" class="h-4 w-4 text-tg-accent" />
                                        <h3 class="font-semibold text-tg-text">{{ favorite.provider.business_name }}</h3>
                                    </div>
                                    
                                    <p v-if="favorite.provider.description" class="text-sm text-tg-hint mb-2 line-clamp-2">
                                        {{ favorite.provider.description }}
                                    </p>
                                    
                                    <div class="space-y-1">
                                        <div v-if="favorite.provider.address" class="flex items-center gap-2 text-xs text-tg-hint">
                                            <Icon name="MapPin" class="h-3 w-3" />
                                            <span>{{ favorite.provider.address }}</span>
                                        </div>
                                        <div v-if="favorite.provider.contact_phone" class="flex items-center gap-2 text-xs text-tg-hint">
                                            <Icon name="Phone" class="h-3 w-3" />
                                            <span>{{ favorite.provider.contact_phone }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-tg-hint">
                                            <Icon name="Calendar" class="h-3 w-3" />
                                            <span>{{ t('app.favorited_on') }} {{ favorite.favorited_at }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <Button @click="viewProvider(favorite.provider.id)" variant="outline" size="sm">
                                        {{ t('app.view') }}
                                    </Button>
                                    <Button 
                                        @click="removeFavorite(favorite.provider.id, favorite.id)"
                                        variant="ghost" 
                                        size="sm"
                                        :disabled="removingFavorite === favorite.id"
                                        class="text-destructive hover:text-destructive hover:bg-destructive/10"
                                    >
                                        <Icon name="Trash2" class="h-3 w-3" />
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Pagination -->
                    <div v-if="(favorites?.links?.length || 0) > 3" class="flex justify-center gap-1 mt-6">
                        <template v-for="link in (favorites?.links || [])" :key="link.label">
                            <Button
                                v-if="link.url"
                                @click="router.get(link.url)"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                class="min-w-[40px]"
                            >
                                {{ link.label }}
                            </Button>
                            <Button
                                v-else
                                variant="ghost"
                                size="sm"
                                disabled
                                class="min-w-[40px]"
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