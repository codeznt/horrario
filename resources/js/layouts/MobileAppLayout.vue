<template>
    <TelegramAppLayout :padded="true" :fill="true" class="mobile-app-layout">
        <!-- Header -->
        <header class="app-header border-b border-tg-section-separator bg-tg-secondary-bg">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-3">
                    <Button v-if="showBackButton" variant="ghost" size="sm" @click="goBack" class="-ml-2 p-2">
                        <Icon name="ChevronLeft" class="h-5 w-5" />
                    </Button>
                    <div>
                        <h1 class="text-lg font-semibold text-tg-text">{{ title }}</h1>
                        <p v-if="subtitle" class="text-sm text-tg-subtitle-text">{{ subtitle }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <slot name="header-actions" />
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="app-content flex-1 overflow-y-auto">
            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.message" class="p-4">
                <Alert :variant="getFlashVariant($page.props.flash.type)" class="mb-4">
                    <Icon :name="getFlashIcon($page.props.flash.type)" class="h-4 w-4" />
                    <AlertTitle>{{ getFlashTitle($page.props.flash.type) }}</AlertTitle>
                    <AlertDescription>{{ $page.props.flash.message }}</AlertDescription>
                </Alert>
            </div>

            <!-- Page Content -->
            <div class="px-4 pb-4">
                <slot />
            </div>
        </main>

        <!-- Bottom Navigation -->
        <nav v-if="showNavigation" class="app-navigation border-t border-tg-section-separator bg-tg-secondary-bg">
            <div class="flex items-center justify-around py-2">
                <NavLink :href="servicesRoute" :active="isServicesRoute" icon="Search" label="Services" />

                <NavLink :href="bookingsIndex.url()" :active="isCurrentRoute('/bookings')" icon="Calendar" label="Bookings" />

                <NavLink
                    v-if="$page.props.auth.user"
                    :href="providerDashboard.url()"
                    :active="isCurrentRoute('/provider')"
                    icon="Store"
                    label="Provider"
                />

                <NavLink :href="profileShow.url()" :active="isCurrentRoute('/profile')" icon="User" label="Profile" />
            </div>
        </nav>

        <!-- Sticky Actions (if provided) -->
        <template v-if="$slots.actions" #actions>
            <div class="border-t border-tg-section-separator bg-tg-bg p-4">
                <slot name="actions" />
            </div>
        </template>
    </TelegramAppLayout>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import NavLink from '@/components/NavLink.vue';
import { Alert, AlertDescription, AlertTitle, Button } from '@/components/ui';
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue';
import { index as bookingsIndex } from '@/routes/bookings';
import { edit as profileShow } from '@/routes/profile';
import { create as providerDashboard } from '@/routes/provider';
import { index as servicesIndex } from '@/routes/services';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    title: string;
    subtitle?: string;
    showBackButton?: boolean;
    showNavigation?: boolean;
}

interface FlashMessage {
    message?: string;
    type?: string;
}

// Define our app-specific page props
interface AppPageProps {
    flash?: FlashMessage;
    auth: {
        user?: {
            id: number;
            name: string;
            email: string;
            role?: string;
        };
    };
}

// Extend Inertia's Page interface with our custom props
declare module '@inertiajs/core' {
    // eslint-disable-next-line @typescript-eslint/no-empty-object-type
    interface PageProps extends AppPageProps {}
}

withDefaults(defineProps<Props>(), {
    showBackButton: false,
    showNavigation: true,
});

const page = usePage();

// Determine services route based on user role
const servicesRoute = computed(() => {
    const user = page.props.auth?.user;
    if (user?.role === 'customer') {
        return '/browse/services';
    } else {
        // For business users or fallback, use business services route
        return servicesIndex.url();
    }
});

// Determine if current route matches services (checking both possibilities)
const isServicesRoute = computed(() => {
    const currentPath = page.url;
    return currentPath.startsWith('/services') || currentPath.startsWith('/browse/services');
});

function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/');
    }
}

function isCurrentRoute(path: string): boolean {
    return window.location.pathname.startsWith(path);
}

function getFlashVariant(type?: string) {
    switch (type) {
        case 'success':
            return 'default';
        case 'error':
            return 'destructive';
        case 'warning':
            return 'default';
        default:
            return 'default';
    }
}

function getFlashIcon(type?: string) {
    switch (type) {
        case 'success':
            return 'CheckCircle';
        case 'error':
            return 'XCircle';
        case 'warning':
            return 'AlertTriangle';
        default:
            return 'Info';
    }
}

function getFlashTitle(type?: string) {
    switch (type) {
        case 'success':
            return 'Success';
        case 'error':
            return 'Error';
        case 'warning':
            return 'Warning';
        default:
            return 'Information';
    }
}
</script>

<style scoped>
.mobile-app-layout {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.app-content {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
</style>
