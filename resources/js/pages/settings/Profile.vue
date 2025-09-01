<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, ref } from 'vue';
import type { AppPageProps } from '@/types';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import InputError from '@/components/InputError.vue';
import Icon from '@/components/Icon.vue';

const { t } = useTranslations();
const page = usePage<AppPageProps & {
    mustVerifyEmail: boolean;
    status: string;
}>();

const user = page.props.auth?.user;

// Profile update form
const form = useForm({
    name: user?.name || '',
    email: user?.email || '',
});

// Account deletion form
const deleteForm = useForm({
    password: '',
});

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement>();

const updateProfile = () => {
    form.patch('/settings/profile', {
        preserveScroll: true,
        onSuccess: () => {
            // Optionally show success message
        },
        onError: () => {
            // Form errors will be handled by Inertia automatically
        }
    });
};

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    deleteForm.delete('/settings/profile', {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
        onError: () => {
            passwordInput.value?.focus();
        },
        onFinish: () => {
            deleteForm.reset();
        }
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    deleteForm.reset();
};

const isEmailVerificationPending = computed(() => 
    page.props.mustVerifyEmail && !user?.email_verified_at
);
</script>

<template>
    <Head :title="t('settings.profile.title')" />

    <AppLayoutWithNavigation 
        :title="t('settings.profile.title')"
        :subtitle="t('settings.profile.subtitle')"
        :show-back-button="true"
    >
        <div class="space-y-6 px-4">
            <!-- Profile Information Card -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Icon name="User" class="h-5 w-5" />
                        {{ t('settings.profile.information.title') }}
                    </CardTitle>
                    <CardDescription>
                        {{ t('settings.profile.information.description') }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <form @submit.prevent="updateProfile" class="space-y-4">
                        <!-- Name Field -->
                        <div class="space-y-2">
                            <Label for="name">{{ t('settings.profile.name') }}</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                :disabled="form.processing"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <Label for="email">{{ t('settings.profile.email') }}</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                :disabled="form.processing"
                                required
                                autocomplete="email"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Email Verification Notice -->
                        <div 
                            v-if="isEmailVerificationPending"
                            class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-700 dark:bg-yellow-900/20"
                        >
                            <div class="flex items-start gap-3">
                                <Icon name="AlertTriangle" class="h-5 w-5 text-yellow-600 dark:text-yellow-500" />
                                <div class="flex-1 text-sm">
                                    <p class="font-medium text-yellow-800 dark:text-yellow-200">
                                        {{ t('settings.profile.email_verification_required') }}
                                    </p>
                                    <p class="mt-1 text-yellow-700 dark:text-yellow-300">
                                        {{ t('settings.profile.email_verification_notice') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Success Status -->
                        <div
                            v-if="page.props.status === 'profile-updated'"
                            class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700 dark:bg-green-900/20"
                        >
                            <div class="flex items-center gap-3">
                                <Icon name="CheckCircle" class="h-5 w-5 text-green-600 dark:text-green-500" />
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                    {{ t('settings.profile.saved') }}
                                </p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full"
                        >
                            <Icon 
                                v-if="form.processing" 
                                name="Loader2" 
                                class="mr-2 h-4 w-4 animate-spin" 
                            />
                            {{ form.processing ? t('common.saving') : t('common.save_changes') }}
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <Separator />

            <!-- Danger Zone Card -->
            <Card class="border-red-200 dark:border-red-800">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-red-600 dark:text-red-400">
                        <Icon name="AlertTriangle" class="h-5 w-5" />
                        {{ t('settings.profile.danger_zone.title') }}
                    </CardTitle>
                    <CardDescription>
                        {{ t('settings.profile.danger_zone.description') }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Dialog :open="confirmingUserDeletion" @update:open="confirmingUserDeletion = $event">
                        <DialogTrigger as-child>
                            <Button
                                variant="destructive"
                                @click="confirmUserDeletion"
                            >
                                <Icon name="Trash2" class="mr-2 h-4 w-4" />
                                {{ t('settings.profile.delete_account') }}
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-md">
                            <DialogHeader>
                                <DialogTitle class="flex items-center gap-2">
                                    <Icon name="AlertTriangle" class="h-5 w-5 text-red-500" />
                                    {{ t('settings.profile.delete_confirmation.title') }}
                                </DialogTitle>
                                <DialogDescription class="text-left">
                                    {{ t('settings.profile.delete_confirmation.description') }}
                                </DialogDescription>
                            </DialogHeader>
                            <form @submit.prevent="deleteUser" class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="password">{{ t('settings.profile.password') }}</Label>
                                    <Input
                                        id="password"
                                        ref="passwordInput"
                                        v-model="deleteForm.password"
                                        type="password"
                                        :placeholder="t('settings.profile.password_placeholder')"
                                        :disabled="deleteForm.processing"
                                        required
                                    />
                                    <InputError :message="deleteForm.errors.password" />
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="closeModal"
                                        :disabled="deleteForm.processing"
                                    >
                                        {{ t('common.cancel') }}
                                    </Button>
                                    <Button
                                        type="submit"
                                        variant="destructive"
                                        :disabled="deleteForm.processing"
                                    >
                                        <Icon 
                                            v-if="deleteForm.processing" 
                                            name="Loader2" 
                                            class="mr-2 h-4 w-4 animate-spin" 
                                        />
                                        {{ deleteForm.processing ? t('common.deleting') : t('settings.profile.delete_account') }}
                                    </Button>
                                </div>
                            </form>
                        </DialogContent>
                    </Dialog>
                </CardContent>
            </Card>
        </div>
    </AppLayoutWithNavigation>
</template>