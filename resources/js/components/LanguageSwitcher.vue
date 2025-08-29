<template>
    <div class="relative">
        <button
            @click="isOpen = !isOpen"
            class="flex items-center gap-2 rounded-md border border-tg-section-separator bg-tg-section-bg px-3 py-2 text-sm transition-colors hover:bg-tg-bg"
        >
            <span class="font-medium">{{ getCurrentLanguage().name }}</span>
            <Icon name="ChevronDown" class="h-4 w-4" />
        </button>

        <div v-if="isOpen" class="absolute right-0 z-50 mt-2 w-32 rounded-md border border-tg-section-separator bg-tg-section-bg shadow-lg">
            <div class="py-1">
                <button
                    v-for="language in languages"
                    :key="language.code"
                    @click="switchLanguage(language.code)"
                    :class="[
                        'flex w-full items-center px-3 py-2 text-left text-sm transition-colors',
                        locale === language.code ? 'bg-tg-accent text-tg-accent-foreground' : 'text-tg-text hover:bg-tg-bg',
                    ]"
                >
                    <span class="mr-2">{{ language.flag }}</span>
                    {{ language.name }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { useTranslations } from '@/composables/useTranslations';
import { router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const { getLocale } = useTranslations();

const isOpen = ref(false);
const locale = computed(() => getLocale());

const languages = [
    { code: 'en', name: 'English', flag: '🇺🇸' },
    { code: 'uk', name: 'Українська', flag: '🇺🇦' },
];

const getCurrentLanguage = () => {
    return languages.find((lang) => lang.code === locale.value) || languages[0];
};

const switchLanguage = (newLocale: string) => {
    if (newLocale !== locale.value) {
        router.post(
            `/language/${newLocale}`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    isOpen.value = false;
                },
            },
        );
    } else {
        isOpen.value = false;
    }
};

// Close dropdown when clicking outside
const closeDropdown = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.relative')) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});
</script>
