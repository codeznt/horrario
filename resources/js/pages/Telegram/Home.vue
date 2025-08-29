<template>
    <TelegramAppLayout>
        <div class="mx-auto max-w-2xl p-4">
            <div class="mb-6 text-center">
                <h1 class="mb-2 text-2xl font-bold text-tg-text">
                    {{ message }}
                </h1>
        <p class="text-tg-hint">
          {{ t('app.welcome_user', { name: user.first_name }) }}
        </p>
            </div>

            <!-- User Info Card -->
            <div class="mb-6 rounded-lg bg-tg-section-bg p-4">
                <h2 class="mb-3 text-lg font-semibold text-tg-text">
          {{ t('app.profile') }}
        </h2>

                <div class="grid grid-cols-1 gap-3">
                    <div class="flex justify-between">
            <span class="text-tg-hint">{{ t('app.name') }}:</span>
                        <span class="text-tg-text">{{ user.name }}</span>
                    </div>

                    <div class="flex justify-between" v-if="user.username">
            <span class="text-tg-hint">{{ t('app.username') }}:</span>
                        <span class="text-tg-text">@{{ user.username }}</span>
                    </div>

                    <div class="flex justify-between">
            <span class="text-tg-hint">{{ t('app.language') }}:</span>
            <span class="text-tg-text">{{ user.language_code || t('app.unknown') }}</span>
                    </div>

                    <div class="flex justify-between">
            <span class="text-tg-hint">{{ t('app.role') }}:</span>
                        <span class="text-tg-text">{{ user.role }}</span>
                    </div>

                    <div class="flex justify-between">
            <span class="text-tg-hint">{{ t('app.premium') }}:</span>
                        <span :class="user.is_premium ? 'text-tg-link' : 'text-tg-hint'">
                            {{ user.is_premium ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <button
                    @click="openMainBot"
                    class="w-full rounded-lg bg-tg-accent px-4 py-3 font-medium text-tg-accent-foreground transition-opacity hover:opacity-90"
                >
          {{ t('app.open_main_bot') }}
        </button>

                <button
                    @click="shareApp"
                    class="w-full rounded-lg border border-tg-section-separator bg-tg-secondary-bg px-4 py-3 font-medium text-tg-text transition-opacity hover:opacity-90"
                >
          {{ t('app.share_app') }}
        </button>
            </div>

            <!-- Debug Info (Development only) -->
            <div v-if="isDev && telegramData" class="mt-8 rounded-lg bg-tg-section-bg p-4">
                <h3 class="mb-2 text-sm font-semibold text-tg-text">
          {{ t('app.debug_info') }}
        </h3>
                <pre class="overflow-auto text-xs text-tg-hint">{{ JSON.stringify(telegramData, null, 2) }}</pre>
            </div>
        </div>
    </TelegramAppLayout>
</template>

<script setup lang="ts">
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue';
import { computed, onMounted } from 'vue';

interface User {
    id: number;
    name: string;
    first_name: string;
    last_name?: string;
    username?: string;
    language_code?: string;
    role: string;
    is_premium: boolean;
    allows_write_to_pm: boolean;
    telegram_id: number;
}

defineProps<{
    message: string;
    user: User;
    telegramData?: Record<string, any>;
}>();

import { useTranslations } from '@/composables/useTranslations';
const isDev = computed(() => import.meta.env.DEV);
const { t } = useTranslations();

const openMainBot = () => {
    // Open the main bot chat
    if (window.Telegram?.WebApp?.openTelegramLink) {
        window.Telegram.WebApp.openTelegramLink('https://t.me/your_bot_username');
    }
};

const shareApp = () => {
    // Share the app
    if (window.Telegram?.WebApp?.openTelegramLink) {
    const shareUrl = `https://t.me/share/url?url=${encodeURIComponent(window.location.origin)}&text=${encodeURIComponent(t('app.share_app_text'))}`;
        window.Telegram.WebApp.openTelegramLink(shareUrl);
    }
};

onMounted(() => {
    // Initialize Telegram WebApp if available
    if (window.Telegram?.WebApp) {
        window.Telegram.WebApp.ready();
        window.Telegram.WebApp.expand();

        // Set main button if needed
        window.Telegram.WebApp.MainButton.text = t('app.main_action');
        window.Telegram.WebApp.MainButton.show();
    }
});
</script>
