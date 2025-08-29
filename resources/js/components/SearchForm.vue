<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="flex flex-col gap-3 md:flex-row">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <Input v-model="form.query" :placeholder="t('app.search_placeholder')" class="pr-4 pl-10" />
                    <Icon name="Search" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-tg-hint" />
                </div>
            </div>

            <!-- Sort Dropdown -->
            <div class="w-full md:w-48">
                <Select v-model="form.sort">
                    <SelectTrigger>
                        <SelectValue :placeholder="t('app.sort_by')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="relevance">{{ t('app.search_relevance') }}</SelectItem>
                        <SelectItem value="price_low">{{ t('app.search_price_low_to_high') }}</SelectItem>
                        <SelectItem value="price_high">{{ t('app.search_price_high_to_low') }}</SelectItem>
                        <SelectItem value="rating">{{ t('app.rating') }}</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Search Button -->
            <Button type="submit" class="w-full md:w-auto">
                <Icon name="Search" class="mr-2 h-4 w-4" />
                {{ t('app.search') }}
            </Button>
        </div>

        <!-- Additional Filters (Optional) -->
        <div class="flex flex-wrap gap-2">
            <Badge
                v-for="category in popularCategories"
                :key="category"
                :variant="form.category === category ? 'default' : 'outline'"
                class="cursor-pointer"
                @click="selectCategory(category)"
            >
                {{ category }}
            </Badge>
            <Badge
                v-if="form.category && !popularCategories.includes(form.category)"
                variant="default"
                class="cursor-pointer"
                @click="form.category = ''"
            >
                {{ form.category }} ×
            </Badge>
        </div>
    </form>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Badge, Button, Input, Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    initialQuery?: string;
    initialCategory?: string;
    initialSort?: string;
}

const props = withDefaults(defineProps<Props>(), {
    initialQuery: '',
    initialCategory: '',
    initialSort: 'relevance',
});

const form = ref({
    query: props.initialQuery,
    category: props.initialCategory,
    sort: props.initialSort,
});

// Example categories - these would typically come from your backend
const popularCategories = [
    t('app.categories.haircut'),
    t('app.categories.massage'),
    t('app.categories.nails'),
    t('app.categories.fitness'),
    t('app.categories.consulting'),
];

function selectCategory(category: string) {
    form.value.category = form.value.category === category ? '' : category;
}

function handleSubmit() {
    // Use Inertia router to submit the search form
    router.get(
        '/search',
        {
            query: form.value.query,
            category: form.value.category,
            sort: form.value.sort,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['services', 'providers', 'filters'],
        },
    );
}

const { t } = useTranslations();
</script>
