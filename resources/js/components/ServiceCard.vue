<template>
    <Card class="overflow-hidden transition-shadow duration-200 hover:shadow-md">
        <div class="relative">
            <!-- Service Image or Placeholder -->
            <div class="flex h-32 items-center justify-center bg-tg-section-bg">
                <div v-if="!service.profile_image" class="text-tg-hint">
                    <PlaceholderPattern :seed="service.id" class="h-full w-full" />
                </div>
                <img v-else :src="service.profile_image" :alt="service.title" class="h-full w-full object-cover" />
            </div>

            <!-- Price Badge -->
            <Badge class="absolute top-2 right-2 font-medium">
                {{ service.display_price }}
            </Badge>
        </div>

        <CardContent class="p-4">
            <!-- Service Title -->
            <h3 class="mb-1 line-clamp-1 text-base font-medium">{{ service.title }}</h3>

            <!-- Provider Info -->
            <div class="mb-2 flex items-center text-sm text-tg-subtitle-text">
                <Icon name="Store" class="mr-1 h-3.5 w-3.5" />
                <span class="line-clamp-1">{{ service.provider?.business_name }}</span>
            </div>

            <!-- Service Description -->
            <p class="mb-3 line-clamp-2 text-sm text-tg-text">
                {{ service.description || t('app.no_description_provided') }}
            </p>

            <!-- Duration -->
            <div class="mb-3 flex items-center text-xs text-tg-subtitle-text">
                <Icon name="Clock" class="mr-1 h-3.5 w-3.5" />
                <span>{{ service.duration_minutes }} {{ t('app.minutes') }}</span>
            </div>

            <!-- Action Button -->
            <div class="mt-2">
                <Button :href="`/services/${service.id}/book`" class="w-full" size="sm">
                    {{ t('app.book_now') }}
                </Button>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { Badge, Button, Card, CardContent } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';

interface Provider {
    id: number;
    business_name: string;
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

defineProps<{
    service: Service;
}>();

const { t } = useTranslations();
</script>
