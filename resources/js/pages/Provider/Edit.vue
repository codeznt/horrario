<script setup lang="ts">
import { Input, Label, Textarea } from '@/components/ui';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';
import { computed, ref } from 'vue';

const { t } = useTranslations();

interface Provider {
    id: number;
    business_name: string;
    address: string | null;
    contact_phone: string | null;
    description: string | null;
    profile_image: string | null;
}

const props = defineProps<{
    provider: Provider;
}>();

const fileInput = ref<HTMLInputElement>();
const imagePreview = ref<string>();

const form = useForm({
    business_name: props.provider.business_name,
    address: props.provider.address || '',
    contact_phone: props.provider.contact_phone || '',
    description: props.provider.description || '',
    profile_image: null as File | null,
});

const currentImageUrl = computed(() => {
    if (props.provider.profile_image) {
        return `/storage/${props.provider.profile_image}`;
    }
    return null;
});

const displayImage = computed(() => {
    return imagePreview.value || currentImageUrl.value;
});

const handleImageSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.profile_image = file;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.profile_image = null;
    imagePreview.value = undefined;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const submit = () => {
    form.put('/provider', {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="t('edit_provider')" />

    <AppLayoutWithNavigation>
        <div class="container mx-auto max-w-2xl px-4 py-6">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Icon name="Building2" class="h-5 w-5" />
                        {{ t('edit_provider') }}
                    </CardTitle>
                    <CardDescription>
                        {{ t('update_business_profile') }}
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Business Name -->
                        <div class="space-y-2">
                            <Label for="business_name" class="flex items-center gap-2">
                                <Icon name="Building2" class="h-4 w-4" />
                                {{ t('business_name') }} *
                            </Label>
                            <Input
                                id="business_name"
                                v-model="form.business_name"
                                type="text"
                                :placeholder="t('enter_business_name')"
                                :class="{ 'border-red-500': form.errors.business_name }"
                                required
                            />
                            <p v-if="form.errors.business_name" class="text-sm text-red-600">
                                {{ form.errors.business_name }}
                            </p>
                        </div>

                        <!-- Address -->
                        <div class="space-y-2">
                            <Label for="address" class="flex items-center gap-2">
                                <Icon name="MapPin" class="h-4 w-4" />
                                {{ t('address') }}
                            </Label>
                            <Textarea
                                id="address"
                                v-model="form.address"
                                :class="form.errors.address ? 'border-red-500' : ''"
                                :placeholder="t('business_address_placeholder')"
                                :rows="3"
                            />
                            <p v-if="form.errors.address" class="text-sm text-red-600">
                                {{ form.errors.address }}
                            </p>
                        </div>

                        <!-- Contact Phone -->
                        <div class="space-y-2">
                            <Label for="contact_phone" class="flex items-center gap-2">
                                <Icon name="Phone" class="h-4 w-4" />
                                {{ t('contact_phone') }}
                            </Label>
                            <Input
                                id="contact_phone"
                                v-model="form.contact_phone"
                                type="tel"
                                :placeholder="t('enter_contact_phone')"
                                :class="{ 'border-red-500': form.errors.contact_phone }"
                            />
                            <p v-if="form.errors.contact_phone" class="text-sm text-red-600">
                                {{ form.errors.contact_phone }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description" class="flex items-center gap-2">
                                <Icon name="FileText" class="h-4 w-4" />
                                {{ t('description') }}
                            </Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                :class="form.errors.description ? 'border-red-500' : ''"
                                :placeholder="t('business_description_placeholder')"
                                :rows="4"
                            />
                            <p v-if="form.errors.description" class="text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Profile Image -->
                        <div class="space-y-2">
                            <Label class="flex items-center gap-2">
                                <Icon name="User" class="h-4 w-4" />
                                {{ t('profile_image') }}
                            </Label>

                            <div v-if="displayImage" class="space-y-2">
                                <div class="relative inline-block">
                                    <img :src="displayImage" alt="Profile" class="h-24 w-24 rounded-lg border object-cover" />
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        class="absolute -top-2 -right-2 h-6 w-6 rounded-full p-0"
                                        @click="removeImage"
                                    >
                                        ×
                                    </Button>
                                </div>
                                <div>
                                    <Button type="button" variant="outline" size="sm" @click="fileInput?.click()">
                                        {{ t('change_image') }}
                                    </Button>
                                </div>
                            </div>

                            <div v-else class="rounded-lg border-2 border-dashed border-gray-300 p-6 text-center">
                                <Icon name="Upload" class="mx-auto mb-2 h-8 w-8 text-gray-400" />
                                <p class="mb-2 text-sm text-gray-600">{{ t('upload_profile_image') }}</p>
                                <Button type="button" variant="outline" size="sm" @click="fileInput?.click()">
                                    {{ t('choose_file') }}
                                </Button>
                            </div>

                            <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleImageSelect" />

                            <p v-if="form.errors.profile_image" class="text-sm text-red-600">
                                {{ form.errors.profile_image }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 pt-4">
                            <Button type="submit" class="flex-1" :disabled="form.processing">
                                <Icon name="Save" class="mr-2 h-4 w-4" />
                                {{ form.processing ? t('saving') : t('save_changes') }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayoutWithNavigation>
</template>
