<template>
    <Head :title="t('create_service')" />

    <AppLayoutWithNavigation :title="t('create_new_service')" :subtitle="t('add_service')">

        <div class="container mx-auto px-4 py-8">
            <div class="mx-auto max-w-2xl">
                <div class="mb-8">
                    <Button variant="ghost" @click="router.visit('/services')" class="mb-4">
                        <Icon name="ArrowLeft" class="mr-2 h-4 w-4" />
                        {{ t('back_to_services') }}
                    </Button>
                    <h1 class="text-3xl font-bold text-foreground">{{ t('create_new_service') }}</h1>
                    <p class="mt-2 text-muted-foreground">{{ t('add_service') }}</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('service_details') }}</CardTitle>
                        <CardDescription>
                            {{ t('service_information') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="title">{{ t('service_title_required') }}</Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    :placeholder="t('service_title_placeholder')"
                                    :class="{ 'border-destructive': errors.title }"
                                />
                                <p v-if="errors.title" class="text-sm text-destructive">{{ errors.title }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="description">{{ t('description') }}</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    :placeholder="t('describe_service')"
                                    :rows="3"
                                    :class="{ 'border-destructive': errors.description }"
                                />
                                <p v-if="errors.description" class="text-sm text-destructive">{{ errors.description }}</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="duration_minutes">{{ t('duration_minutes') }} *</Label>
                                    <Input
                                        id="duration_minutes"
                                        v-model.number="form.duration_minutes"
                                        type="number"
                                        min="5"
                                        max="480"
                                        :placeholder="t('duration_placeholder')"
                                        :class="{ 'border-destructive': errors.duration_minutes }"
                                    />
                                    <p v-if="errors.duration_minutes" class="text-sm text-destructive">{{ errors.duration_minutes }}</p>
                                    <p class="text-xs text-muted-foreground">{{ t('duration_range') }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="display_price">{{ t('service_price') }} *</Label>
                                    <Input
                                        id="display_price"
                                        v-model="form.display_price"
                                        type="text"
                                        :placeholder="t('price_placeholder')"
                                        :class="{ 'border-destructive': errors.display_price }"
                                    />
                                    <p v-if="errors.display_price" class="text-sm text-destructive">{{ errors.display_price }}</p>
                                    <p class="text-xs text-muted-foreground">{{ t('pricing_display_note') }}</p>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <Button type="submit" :disabled="processing" class="flex-1">
                                    <Icon v-if="processing" name="Loader2" class="mr-2 h-4 w-4 animate-spin" />
                                    {{ t('create_service') }}
                                </Button>
                                <Button type="button" variant="outline" @click="router.visit('/services')">
                                    {{ t('cancel') }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
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

const form = useForm({
    title: '',
    description: '',
    duration_minutes: 60,
    display_price: '',
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

const submit = () => {
    processing.value = true;
    errors.value = {};

    form.post('/services', {
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
