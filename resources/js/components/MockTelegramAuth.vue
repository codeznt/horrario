<template>
    <div v-if="showMockAuth" class="mock-auth-panel">
        <div class="mock-auth-content">
            <h3 class="mb-2 text-sm font-semibold text-gray-800">🔧 {{ t('app.mock_telegram_auth') }}</h3>

            <div class="mb-2 flex items-center gap-2">
                <select v-model="selectedUserId" @change="switchUser" class="rounded border bg-white px-2 py-1 text-xs">
                    <option value="">{{ t('app.select_user') }}</option>
                    <option v-for="user in mockUsers" :key="user.id" :value="user.id">{{ user.name }} (@{{ user.username }})</option>
                </select>

                <button @click="createNewUser" class="rounded bg-blue-500 px-2 py-1 text-xs text-white hover:bg-blue-600">
                    {{ t('app.new_user') }}
                </button>
            </div>

            <div class="text-xs text-gray-600">{{ t('app.current') }}: {{ currentUser?.name || t('app.not_authenticated') }}</div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import { router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

interface MockUser {
    id: number;
    name: string;
    username: string;
    first_name: string;
    last_name?: string;
    language_code: string;
    is_premium: boolean;
}

const props = defineProps<{
    currentUser?: {
        id: number;
        name: string;
        telegram_id: number;
    };
}>();

const selectedUserId = ref<number | ''>('');
const { t } = useTranslations();

const mockUsers: MockUser[] = [
    {
        id: 99281932,
        name: 'Andrew Rogue',
        username: 'rogue',
        first_name: 'Andrew',
        last_name: 'Rogue',
        language_code: 'en',
        is_premium: true,
    },
    {
        id: 12345678,
        name: 'John Doe',
        username: 'johndoe',
        first_name: 'John',
        last_name: 'Doe',
        language_code: 'en',
        is_premium: false,
    },
    {
        id: 87654321,
        name: 'Jane Smith',
        username: 'janesmith',
        first_name: 'Jane',
        last_name: 'Smith',
        language_code: 'es',
        is_premium: true,
    },
    {
        id: 11111111,
        name: 'Test Provider',
        username: 'testprovider',
        first_name: 'Test',
        last_name: 'Provider',
        language_code: 'en',
        is_premium: false,
    },
];

const showMockAuth = computed(() => {
    return import.meta.env.DEV || import.meta.env.MODE === 'development';
});

onMounted(() => {
    if (props.currentUser) {
        // Find matching mock user by telegram_id
        const mockUser = mockUsers.find((u) => u.id === props.currentUser?.telegram_id);
        if (mockUser) {
            selectedUserId.value = mockUser.id;
        }
    }
});

function switchUser() {
    if (!selectedUserId.value) return;

    const user = mockUsers.find((u) => u.id === selectedUserId.value);
    if (!user) return;

    // Create mock Telegram init data
    const mockInitData = createMockInitData(user);

    // Navigate with mock auth data
    router.visit(window.location.pathname, {
        method: 'get',
        headers: {
            'X-Telegram-Init-Data': mockInitData,
        },
        preserveState: false,
        preserveScroll: false,
    });
}

function createNewUser() {
    const randomId = Math.floor(Math.random() * 1000000000);
    const newUser: MockUser = {
        id: randomId,
        name: `User ${randomId}`,
        username: `user${randomId}`,
        first_name: `User`,
        last_name: `${randomId}`,
        language_code: 'en',
        is_premium: Math.random() > 0.5,
    };

    mockUsers.push(newUser);
    selectedUserId.value = newUser.id;
    switchUser();
}

function createMockInitData(user: MockUser): string {
    const authDate = Math.floor(Date.now() / 1000);

    const data = {
        user: JSON.stringify(user),
        auth_date: authDate.toString(),
        start_param: 'debug',
        chat_type: 'sender',
        chat_instance: '8428209589180549439',
        hash: 'mock_hash_' + Date.now(), // Mock hash for development
    };

    return new URLSearchParams(data).toString();
}
</script>

<style scoped>
.mock-auth-panel {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to right, #f3f4f6, #e5e7eb);
    border-top: 2px solid #d1d5db;
    z-index: 9999;
    box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
}

.mock-auth-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 8px 16px;
}

@media (max-width: 640px) {
    .mock-auth-content {
        padding: 6px 12px;
    }

    .mock-auth-content h3 {
        font-size: 0.75rem;
    }
}
</style>
