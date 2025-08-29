<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
const { t } = useTranslations();

// shadcn-vue components
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

// Icons
import { Building2, ChevronLeft, Sparkles, User } from 'lucide-vue-next';

// Onboarding state
const currentStep = ref(1);
const totalSteps = 3;
const selectedRole = ref<string>('');

// Progress calculation
const progress = computed(() => (currentStep.value / totalSteps) * 100);

// (Removed unused touch/mouse drag handlers to satisfy lint)

// Navigation functions
function goBack() {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
}

function goNext() {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    }
}

function skipOnboarding() {
    // Skip to dashboard or home
    router.get('/dashboard');
}

function selectRole(role: 'business' | 'customer') {
    selectedRole.value = role;
    // Auto-advance to next step after role selection
    setTimeout(() => {
        goNext();
    }, 300);
}

function completeOnboarding() {
    // Save user role and redirect
    router.post('/onboarding/complete', {
        role: selectedRole.value,
    });
}
</script>

<template>
    <Head title="Welcome to " />

    <TelegramAppLayout class="bg-tg-bg">
        <div class="flex min-h-[--spacing-viewport-h] flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 pb-2">
                <Button v-if="currentStep > 1" variant="ghost" size="sm" class="text-tg-text hover:bg-tg-secondary-bg" @click="goBack">
                    <ChevronLeft class="size-4" />
                    {{ t('app.back') }}
                </Button>
                <div v-else></div>

                <div class="text-sm font-medium text-tg-subtitle-text">
                    {{ currentStep }}
                </div>

                <Button variant="ghost" size="sm" class="text-tg-hint hover:bg-tg-secondary-bg" @click="skipOnboarding"> {{ t('app.skip') }} </Button>
            </div>

            <!-- Progress Bar -->
            <div class="px-4 pb-6">
                <div class="h-1 w-full overflow-hidden rounded-full bg-tg-secondary-bg">
                    <div class="h-full rounded-full bg-tg-accent transition-all duration-300 ease-out" :style="`width: ${progress}%`"></div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex flex-1 flex-col items-center justify-center px-6 pb-8">
                <!-- Step 1: Welcome -->
                <div
                    v-if="currentStep === 1"
                    class="flex max-w-sm animate-in flex-col items-center space-y-8 text-center duration-300 fade-in slide-in-from-right-4"
                >
                    <!-- Illustration placeholder -->
                    <div
                        class="flex h-32 w-32 items-center justify-center rounded-3xl border border-tg-section-separator bg-gradient-to-br from-tg-accent/20 to-tg-link/20"
                    >
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-tg-accent/10">
                            <Sparkles class="size-8 text-tg-accent" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-2xl font-bold text-tg-text">
                            {{ t('app.goodbye_notebook') }}
                        </h1>
                        <div class="space-y-3">
                            <h2 class="text-xl font-semibold text-tg-text">{{ t('app.hello_horario') }}</h2>
                            <p class="text-sm leading-relaxed text-tg-hint">{{ t('app.schedule_description') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Role Selection -->
                <div
                    v-if="currentStep === 2"
                    class="flex max-w-sm animate-in flex-col items-center space-y-8 text-center duration-300 fade-in slide-in-from-right-4"
                >
                    <div class="space-y-4">
                        <h1 class="text-2xl font-bold text-tg-text">
                            {{ t('app.choose_profile') }}
                        </h1>
                        <p class="text-sm text-tg-hint">
                            {{ t('app.help_organize_experience') }}
                        </p>
                    </div>

                    <div class="w-full space-y-3">
                        <!-- Business Option -->
                        <Card
                            class="cursor-pointer border-2 transition-all duration-200 hover:scale-[0.98] active:scale-95"
                            :class="
                                selectedRole === 'business'
                                    ? 'border-tg-accent bg-tg-accent/5'
                                    : 'border-tg-section-separator bg-tg-section-bg hover:bg-tg-secondary-bg'
                            "
                            @click="selectRole('business')"
                        >
                            <CardContent class="flex items-center gap-4 p-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-tg-accent/10">
                                    <Building2 class="size-6 text-tg-accent" />
                                </div>
                                <div class="flex-1 text-left">
                                    <h3 class="font-semibold text-tg-text">{{ t('app.business') }}</h3>
                                    <p class="text-sm text-tg-hint">{{ t('app.provide_services') }}</p>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Customer Option -->
                        <Card
                            class="cursor-pointer border-2 transition-all duration-200 hover:scale-[0.98] active:scale-95"
                            :class="
                                selectedRole === 'customer'
                                    ? 'border-tg-accent bg-tg-accent/5'
                                    : 'border-tg-section-separator bg-tg-section-bg hover:bg-tg-secondary-bg'
                            "
                            @click="selectRole('customer')"
                        >
                            <CardContent class="flex items-center gap-4 p-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-tg-link/10">
                                    <User class="size-6 text-tg-link" />
                                </div>
                                <div class="flex-1 text-left">
                                    <h3 class="font-semibold text-tg-text">{{ t('app.customer') }}</h3>
                                    <p class="text-sm text-tg-hint">{{ t('app.use_services') }}</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Step 3: Completion -->
                <div
                    v-if="currentStep === 3"
                    class="flex max-w-sm animate-in flex-col items-center space-y-8 text-center duration-300 fade-in slide-in-from-right-4"
                >
                    <div
                        class="flex h-32 w-32 items-center justify-center rounded-3xl border border-tg-section-separator bg-gradient-to-br from-green-500/20 to-tg-accent/20"
                    >
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-green-500/10">
                            <AppLogoIcon class="size-8 text-tg-accent" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-2xl font-bold text-tg-text">
                            {{ t('app.all_ready') }}
                        </h1>
                        <p class="text-sm leading-relaxed text-tg-hint">
                            {{ t('app.welcome_horario_organize') }} {{ selectedRole === 'business' ? t('app.schedules') : t('app.appointments') }}.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="px-6 pb-6">
                <div v-if="currentStep === 1" class="flex gap-3">
                    <Button class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90" @click="goNext">
                        {{ t('app.get_started') }}
                    </Button>
                </div>

                <div v-if="currentStep === 3" class="flex gap-3">
                    <Button class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90" @click="completeOnboarding">
                        {{ t('app.to_dashboard') }}
                    </Button>
                </div>
            </div>
        </div>
    </TelegramAppLayout>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slide-in-from-right-4 {
    from {
        transform: translateX(1rem);
    }
    to {
        transform: translateX(0);
    }
}

.animate-in {
    animation:
        fade-in 300ms ease-out,
        slide-in-from-right-4 300ms ease-out;
}
</style>
