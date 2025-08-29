<template>
    <div class="min-h-screen bg-tg-bg text-tg-text">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div class="text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-tg-link" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                    </div>

                    <h1 class="mb-4 text-2xl font-bold text-tg-text">
                        {{ t('app.unauthorized_access') }}
                    </h1>

                    <p class="mb-6 text-tg-hint">
                        {{ message || t('app.telegram_auth_required') }}
                    </p>

                    <div class="mb-6 rounded-lg bg-tg-section-bg p-4">
                        <p class="text-sm text-tg-hint">
                            <strong>{{ t('app.for_developers') }}:</strong><br />
                            {{ t('app.dev_telegram_note') }}
                        </p>
                    </div>

                    <button
                        @click="closeTelegramApp"
                        class="w-full rounded-lg bg-tg-accent px-4 py-2 font-medium text-tg-accent-foreground transition-opacity hover:opacity-90"
                    >
                        {{ t('app.close_app') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import { onMounted } from 'vue';

defineProps<{
    message?: string;
    error?: string;
}>();

const { t } = useTranslations();

const closeTelegramApp = () => {
    // Try to close the Telegram WebApp if available
    if (window.Telegram?.WebApp?.close) {
        window.Telegram.WebApp.close();
    } else {
        // Fallback for development
        console.log('Telegram WebApp not available');
    }
};

onMounted(() => {
    // Initialize Telegram WebApp if available
    if (window.Telegram?.WebApp) {
        window.Telegram.WebApp.ready();
        window.Telegram.WebApp.expand();
    }
});
</script>
