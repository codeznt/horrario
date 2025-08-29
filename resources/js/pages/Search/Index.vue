<template>
    <AppShell>
        <div class="mx-auto max-w-4xl px-4 py-6">
            <Heading :title="t('app.find_services')"></Heading>

            <div class="mt-6">
                <SearchForm />
            </div>

            <div class="mt-8">
                <h2 class="mb-4 text-lg font-medium">{{ t('app.popular_services') }}</h2>

                <!-- Display popular services if available -->
                <div v-if="popularServices && popularServices.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <ServiceCard v-for="service in popularServices" :key="`service-${service.id}`" :service="service" />
                </div>

                <!-- Skeleton loader shown only when loading -->
                <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <div v-for="i in 3" :key="i" class="animate-pulse">
                        <div class="h-32 rounded-t-md bg-tg-section-bg"></div>
                        <div class="space-y-2 rounded-b-md bg-tg-section-bg p-4">
                            <div class="h-4 w-3/4 rounded bg-tg-section-separator"></div>
                            <div class="h-3 w-1/2 rounded bg-tg-section-separator"></div>
                            <div class="mt-3 h-10 w-full rounded bg-tg-section-separator"></div>
                        </div>
                    </div>
                </div>

                <div v-if="!popularServices || popularServices.length === 0" class="mt-8 text-center">
                    <p class="text-tg-subtitle-text">
                        {{ t('app.search_services_prompt') }}
                    </p>
                </div>
            </div>
        </div>
    </AppShell>
</template>

<script setup lang="ts">
import AppShell from '@/components/AppShell.vue';
import Heading from '@/components/Heading.vue';
import SearchForm from '@/components/SearchForm.vue';
import ServiceCard from '@/components/ServiceCard.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

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

interface Props {
    popularServices?: Service[];
}

const props = defineProps<Props>();

// Provide default empty array if popularServices is undefined
const popularServices = props.popularServices || [];
</script>
