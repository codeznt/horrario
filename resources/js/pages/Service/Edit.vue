<template>
    <Head :title="t('app.edit_service')" />

    <div class="min-h-screen bg-background">
        <div class="container mx-auto px-4 py-8">
            <div class="mx-auto max-w-2xl">
                <div class="mb-8">
                    <Button variant="ghost" @click="router.visit('/services')" class="mb-4">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        {{ t('app.back_to_services') }}
                    </Button>
                    <h1 class="text-3xl font-bold text-foreground">{{ t('app.edit_service') }}</h1>
                    <p class="mt-2 text-muted-foreground">{{ t('app.update_service_info') }}</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('app.service_details') }}</CardTitle>
                        <CardDescription>
                            {{ t('app.update_service_info') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="title">{{ t('app.service_title_required') }}</Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    :placeholder="t('app.service_title_placeholder')"
                                    :class="{ 'border-destructive': errors.title }"
                                />
                                <p v-if="errors.title" class="text-sm text-destructive">{{ errors.title }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="description">{{ t('app.description') }}</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    :placeholder="t('app.describe_service')"
                                    :rows="3"
                                    :class="{ 'border-destructive': errors.description }"
                                />
                                <p v-if="errors.description" class="text-sm text-destructive">{{ errors.description }}</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="duration_minutes">{{ t('app.duration_minutes') }} *</Label>
                                    <Input
                                        id="duration_minutes"
                                        v-model.number="form.duration_minutes"
                                        type="number"
                                        min="5"
                                        max="480"
                                        :placeholder="t('app.duration_placeholder')"
                                        :class="{ 'border-destructive': errors.duration_minutes }"
                                    />
                                    <p v-if="errors.duration_minutes" class="text-sm text-destructive">{{ errors.duration_minutes }}</p>
                                    <p class="text-xs text-muted-foreground">{{ t('app.duration_range') }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="display_price">{{ t('app.service_price') }} *</Label>
                                    <Input
                                        id="display_price"
                                        v-model="form.display_price"
                                        type="text"
                                        :placeholder="t('app.price_placeholder')"
                                        :class="{ 'border-destructive': errors.display_price }"
                                    />
                                    <p v-if="errors.display_price" class="text-sm text-destructive">{{ errors.display_price }}</p>
                                    <p class="text-xs text-muted-foreground">{{ t('app.pricing_display_note') }}</p>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <Button type="submit" :disabled="processing" class="flex-1">
                                    <Loader2 v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
                                    {{ t('app.update_service') }}
                                </Button>
                                <Button type="button" variant="outline" @click="router.visit('/services')">
                                    {{ t('app.cancel') }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useTranslations } from '@/composables/useTranslations';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Loader2 } from 'lucide-vue-next';
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
