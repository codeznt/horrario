<template>
    <Head :title="t('app.edit_service')" />

    <AppLayoutWithNavigation>
        <div class="min-h-full bg-tg-bg">
            <!-- Enhanced Header with Gradient Background -->
            <div class="relative overflow-hidden bg-gradient-to-r from-tg-accent to-purple-600 px-4 py-6">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjAuNSIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+')] opacity-10"></div>
                <div class="relative">
                    <Button variant="ghost" @click="router.visit('/services')" class="mb-4 -ml-2 flex items-center gap-2 text-white/90 hover:bg-white/20">
                        <Icon name="ArrowLeft" class="h-4 w-4" />
                        {{ t('app.back_to_services') }}
                    </Button>
                    <h1 class="text-2xl font-bold text-white">{{ t('app.edit_service') }}</h1>
                    <p class="mt-1 text-sm text-white/90">{{ t('app.update_service_info') }}</p>
                </div>
            </div>

            <div class="container mx-auto px-4 py-6">
                <div class="mx-auto max-w-2xl">
                    <Card class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm">
                        <CardHeader class="border-b border-tg-section-separator pb-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-tg-accent/10">
                                    <Icon name="Package" class="h-5 w-5 text-tg-accent" />
                                </div>
                                <div>
                                    <CardTitle class="text-xl font-bold text-tg-text">{{ t('app.service_details') }}</CardTitle>
                                    <CardDescription class="text-tg-hint">
                                        {{ t('app.update_service_info') }}
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="space-y-2">
                                    <Label for="title" class="text-sm font-medium text-tg-text flex items-center gap-2">
                                        <Icon name="FileText" class="h-4 w-4 text-tg-accent" />
                                        {{ t('app.service_title_required') }}
                                    </Label>
                                    <Input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        :placeholder="t('app.service_title_placeholder')"
                                        :class="{ 'border-red-500': errors.title }"
                                        class="border-tg-section-separator bg-white focus:border-tg-accent focus:ring-tg-accent"
                                    />
                                    <p v-if="errors.title" class="text-sm text-red-600">{{ errors.title }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="description" class="text-sm font-medium text-tg-text flex items-center gap-2">
                                        <Icon name="AlignLeft" class="h-4 w-4 text-tg-accent" />
                                        {{ t('app.description') }}
                                    </Label>
                                    <Textarea
                                        id="description"
                                        v-model="form.description"
                                        :placeholder="t('app.describe_service')"
                                        :rows="4"
                                        :class="errors.description ? 'border-red-500' : ''"
                                        class="border-tg-section-separator bg-white focus:border-tg-accent focus:ring-tg-accent"
                                    />
                                    <p v-if="errors.description" class="text-sm text-red-600">{{ errors.description }}</p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="duration_minutes" class="text-sm font-medium text-tg-text flex items-center gap-2">
                                            <Icon name="Clock" class="h-4 w-4 text-tg-accent" />
                                            {{ t('app.duration_minutes') }} *
                                        </Label>
                                        <div class="relative">
                                            <Input
                                                id="duration_minutes"
                                                v-model.number="form.duration_minutes"
                                                type="number"
                                                min="5"
                                                max="480"
                                                :placeholder="t('app.duration_placeholder')"
                                                :class="{ 'border-red-500': errors.duration_minutes }"
                                                class="border-tg-section-separator bg-white pl-10 focus:border-tg-accent focus:ring-tg-accent"
                                            />
                                            <Icon name="Clock" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-tg-hint" />
                                        </div>
                                        <p v-if="errors.duration_minutes" class="text-sm text-red-600">{{ errors.duration_minutes }}</p>
                                        <p class="text-xs text-tg-hint">{{ t('app.duration_range') }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="display_price" class="text-sm font-medium text-tg-text flex items-center gap-2">
                                            <Icon name="DollarSign" class="h-4 w-4 text-tg-accent" />
                                            {{ t('app.service_price') }} *
                                        </Label>
                                        <div class="relative">
                                            <Input
                                                id="display_price"
                                                v-model="form.display_price"
                                                type="text"
                                                :placeholder="t('app.price_placeholder')"
                                                :class="{ 'border-red-500': errors.display_price }"
                                                class="border-tg-section-separator bg-white pl-10 focus:border-tg-accent focus:ring-tg-accent"
                                            />
                                            <Icon name="DollarSign" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-tg-hint" />
                                        </div>
                                        <p v-if="errors.display_price" class="text-sm text-red-600">{{ errors.display_price }}</p>
                                        <p class="text-xs text-tg-hint">{{ t('app.pricing_display_note') }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 pt-4 sm:flex-row">
                                    <Button 
                                        type="submit" 
                                        :disabled="processing" 
                                        class="flex-1 bg-tg-accent text-white hover:bg-tg-accent/90"
                                    >
                                        <Icon v-if="processing" name="Loader2" class="mr-2 h-4 w-4 animate-spin" />
                                        <Icon v-else name="Save" class="mr-2 h-4 w-4" />
                                        {{ processing ? t('app.updating_service') : t('app.update_service') }}
                                    </Button>
                                    <Button 
                                        type="button" 
                                        variant="outline" 
                                        @click="router.visit('/services')"
                                        class="border-tg-section-separator text-tg-text hover:bg-tg-section-separator/20"
                                    >
                                        {{ t('app.cancel') }}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayoutWithNavigation>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useTranslations } from '@/composables/useTranslations';
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import Icon from '@/components/Icon.vue';
import { ref } from 'vue';

const { t } = useTranslations();

interface Service {
    id: number;
    title: string;
    description?: string;
    duration_minutes: number;
    display_price: string;
}

const props = defineProps<{
    service: Service;
}>();

const form = useForm({
    title: props.service.title,
    description: props.service.description || '',
    duration_minutes: props.service.duration_minutes,
    display_price: props.service.display_price,
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

const submit = () => {
    processing.value = true;
    errors.value = {};

    form.put(`/services/${props.service.id}`, {
        onSuccess: () => {
            // Redirect handled by controller
        },
        onError: (formErrors) => {
            errors.value = formErrors;
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>
