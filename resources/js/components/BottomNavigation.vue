<template>
    <nav 
        class="sticky bottom-0 left-0 right-0 z-50 border-t border-tg-section-separator bg-tg-secondary-bg/95 backdrop-blur-sm"
    >
        <div class="grid grid-cols-4 gap-1 px-2 py-1" v-if="navigationItems.length === 4">
            <NavLink
                v-for="item in navigationItems"
                :key="item.route"
                :href="item.route"
                :active="isActiveRoute(item.route, item.activePattern)"
                :icon="item.icon"
                :label="item.label"
            />
        </div>
        <div class="grid grid-cols-5 gap-1 px-2 py-1" v-else-if="navigationItems.length === 5">
            <NavLink
                v-for="item in navigationItems"
                :key="item.route"
                :href="item.route"
                :active="isActiveRoute(item.route, item.activePattern)"
                :icon="item.icon"
                :label="item.label"
            />
        </div>
        <div class="flex justify-around px-2 py-1" v-else>
            <NavLink
                v-for="item in navigationItems"
                :key="item.route"
                :href="item.route"
                :active="isActiveRoute(item.route, item.activePattern)"
                :icon="item.icon"
                :label="item.label"
            />
        </div>
    </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import NavLink from '@/components/NavLink.vue';
import { useTranslations } from '@/composables/useTranslations';

interface NavigationItem {
    route: string;
    icon: string;
    label: string;
    activePattern: string | RegExp;
    roles?: string[];
}

const { t } = useTranslations();
const page = usePage();

const user = computed(() => page.props.auth?.user);
const userRole = computed(() => user.value?.role);

// Compute navigation items based on user role
const navigationItems = computed(() => {
    // Base dashboard route based on user role
    const dashboardRoute = (() => {
        if (userRole.value === 'customer') return '/customer/dashboard';
        if (userRole.value === 'business') return '/business/dashboard';
        return '/dashboard';
    })();

    const items: NavigationItem[] = [
        {
            route: dashboardRoute,
            icon: 'Home',
            label: t('app.dashboard'),
            activePattern: /^\/(dashboard|customer\/dashboard|business\/dashboard)/
        }
    ];

    if (userRole.value === 'customer') {
        items.push({
            route: '/browse/services',
            icon: 'Search',
            label: t('app.services'),
            activePattern: /^\/browse\/services/
        });
        items.push({
            route: '/bookings',
            icon: 'Calendar',
            label: t('app.bookings'),
            activePattern: /^\/bookings/
        });
        items.push({
            route: '/favorites',
            icon: 'Heart',
            label: t('app.favorites'),
            activePattern: /^\/favorites/
        });
    } else if (userRole.value === 'business') {
        items.push({
            route: '/services',
            icon: 'Package',
            label: t('app.services'),
            activePattern: /^\/services/
        });
        items.push({
            route: '/provider/bookings',
            icon: 'Calendar',
            label: t('app.bookings'),
            activePattern: /^\/provider\/bookings/
        });
        items.push({
            route: '/schedules',
            icon: 'Clock',
            label: t('app.schedule'),
            activePattern: /^\/schedules/
        });
    } else {
        // Default navigation for users without specific roles
        items.push({
            route: '/browse/services',
            icon: 'Search',
            label: t('app.services'),
            activePattern: /^\/browse\/services/
        });
        items.push({
            route: '/bookings',
            icon: 'Calendar',
            label: t('app.bookings'),
            activePattern: /^\/bookings/
        });
    }

    return items;
});

function isActiveRoute(route: string, activePattern: string | RegExp): boolean {
    const currentPath = page.url;
    
    if (activePattern instanceof RegExp) {
        return activePattern.test(currentPath);
    }
    
    return currentPath.startsWith(activePattern);
}
</script>

<style scoped>
/* Ensure the navigation is always above other content */
nav {
    box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.1);
}

/* Ensure proper spacing for mobile devices */
@media (max-width: 640px) {
    .grid > * {
        min-height: 64px;
    }
}
</style>