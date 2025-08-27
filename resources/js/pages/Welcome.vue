<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { backButton, miniApp } from '@telegram-apps/sdk';
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue';
import { ref, computed } from 'vue';

// shadcn-vue components
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

// Icons
import { ChevronLeft, Building2, User, Sparkles } from 'lucide-vue-next';

// Onboarding state
const currentStep = ref(1);
const totalSteps = 3;
const selectedRole = ref<string>('');

// Progress calculation
const progress = computed(() => (currentStep.value / totalSteps) * 100);

// Touch/drag handling
const containerRef = ref<HTMLElement>();
const isDragging = ref(false);
const startX = ref(0);
const currentX = ref(0);
const translateX = ref(0);
const threshold = 80; // Minimum distance to trigger slide

// Touch event handlers
function handleTouchStart(e: TouchEvent) {
    isDragging.value = true;
    startX.value = e.touches[0].clientX;
    currentX.value = e.touches[0].clientX;
}

function handleTouchMove(e: TouchEvent) {
    if (!isDragging.value) return;

    currentX.value = e.touches[0].clientX;
    const deltaX = currentX.value - startX.value;

    // Limit drag distance
    const maxDrag = 100;
    translateX.value = Math.max(-maxDrag, Math.min(maxDrag, deltaX));
}

function handleTouchEnd() {
    if (!isDragging.value) return;

    const deltaX = currentX.value - startX.value;

    // Determine slide direction and threshold
    if (Math.abs(deltaX) > threshold) {
        if (deltaX > 0 && currentStep.value > 1) {
            // Swipe right - go back
            goBack();
        } else if (deltaX < 0 && currentStep.value < totalSteps) {
            // Swipe left - go forward (only if role is selected on step 2)
            if (currentStep.value === 2 && selectedRole.value) {
                goNext();
            } else if (currentStep.value !== 2) {
                goNext();
            }
        }
    }

    // Reset drag state
    isDragging.value = false;
    translateX.value = 0;
}

// Mouse event handlers for desktop
function handleMouseDown(e: MouseEvent) {
    isDragging.value = true;
    startX.value = e.clientX;
    currentX.value = e.clientX;
}

function handleMouseMove(e: MouseEvent) {
    if (!isDragging.value) return;

    currentX.value = e.clientX;
    const deltaX = currentX.value - startX.value;

    const maxDrag = 100;
    translateX.value = Math.max(-maxDrag, Math.min(maxDrag, deltaX));
}

function handleMouseUp() {
    if (!isDragging.value) return;

    const deltaX = currentX.value - startX.value;

    if (Math.abs(deltaX) > threshold) {
        if (deltaX > 0 && currentStep.value > 1) {
            goBack();
        } else if (deltaX < 0 && currentStep.value < totalSteps) {
            if (currentStep.value === 2 && selectedRole.value) {
                goNext();
            } else if (currentStep.value !== 2) {
                goNext();
            }
        }
    }

    isDragging.value = false;
    translateX.value = 0;
}

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
        role: selectedRole.value
    });
}

</script>

<template>
    <Head title="Welcome to " />

    <TelegramAppLayout class="bg-tg-bg">
        <div class="flex min-h-[--spacing-viewport-h] flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 pb-2">
                <Button
                    v-if="currentStep > 1"
                    variant="ghost"
                    size="sm"
                    class="text-tg-text hover:bg-tg-secondary-bg"
                    @click="goBack"
                >
                    <ChevronLeft class="size-4" />
                    Назад
                </Button>
                <div v-else></div>

                <div class="text-sm font-medium text-tg-subtitle-text">
                    {{ currentStep }}
                </div>

                <Button
                    variant="ghost"
                    size="sm"
                    class="text-tg-hint hover:bg-tg-secondary-bg"
                    @click="skipOnboarding"
                >
                    Пропустити
                </Button>
            </div>

            <!-- Progress Bar -->
            <div class="px-4 pb-6">
                <div class="h-1 w-full bg-tg-secondary-bg rounded-full overflow-hidden">
                    <div
                        class="h-full bg-tg-accent transition-all duration-300 ease-out rounded-full"
                        :style="`width: ${progress}%`"
                    ></div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 flex flex-col items-center justify-center px-6 pb-8">
                <!-- Step 1: Welcome -->
                <div v-if="currentStep === 1" class="flex flex-col items-center text-center space-y-8 max-w-sm animate-in fade-in slide-in-from-right-4 duration-300">
                    <!-- Illustration placeholder -->
                    <div class="flex items-center justify-center w-32 h-32 bg-gradient-to-br from-tg-accent/20 to-tg-link/20 rounded-3xl border border-tg-section-separator">
                        <div class="flex items-center justify-center w-16 h-16 bg-tg-accent/10 rounded-2xl">
                            <Sparkles class="size-8 text-tg-accent" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-2xl font-bold text-tg-text">
                            Прощавайте,<br>
                            записник та ручка
                        </h1>
                        <div class="space-y-3">
                            <h2 class="text-xl font-semibold text-tg-text">
                                Привіт,<br>
                                Horario
                            </h2>
                            <p class="text-tg-hint text-sm leading-relaxed">
                                Ваш розклад та записи в одному місці.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Role Selection -->
                <div v-if="currentStep === 2" class="flex flex-col items-center text-center space-y-8 max-w-sm animate-in fade-in slide-in-from-right-4 duration-300">
                    <div class="space-y-4">
                        <h1 class="text-2xl font-bold text-tg-text">
                            Оберіть ваш профіль
                        </h1>
                        <p class="text-tg-hint text-sm">
                            Допоможіть нам організувати ваш досвід
                        </p>
                    </div>

                    <div class="w-full space-y-3">
                        <!-- Business Option -->
                        <Card
                            class="cursor-pointer border-2 transition-all duration-200 hover:scale-[0.98] active:scale-95"
                            :class="selectedRole === 'business'
                                ? 'border-tg-accent bg-tg-accent/5'
                                : 'border-tg-section-separator bg-tg-section-bg hover:bg-tg-secondary-bg'"
                            @click="selectRole('business')"
                        >
                            <CardContent class="flex items-center gap-4 p-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-tg-accent/10">
                                    <Building2 class="size-6 text-tg-accent" />
                                </div>
                                <div class="flex-1 text-left">
                                    <h3 class="font-semibold text-tg-text">Бізнес</h3>
                                    <p class="text-sm text-tg-hint">Надаєте послуги</p>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Customer Option -->
                        <Card
                            class="cursor-pointer border-2 transition-all duration-200 hover:scale-[0.98] active:scale-95"
                            :class="selectedRole === 'customer'
                                ? 'border-tg-accent bg-tg-accent/5'
                                : 'border-tg-section-separator bg-tg-section-bg hover:bg-tg-secondary-bg'"
                            @click="selectRole('customer')"
                        >
                            <CardContent class="flex items-center gap-4 p-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-tg-link/10">
                                    <User class="size-6 text-tg-link" />
                                </div>
                                <div class="flex-1 text-left">
                                    <h3 class="font-semibold text-tg-text">Клієнт</h3>
                                    <p class="text-sm text-tg-hint">Користуєтесь послугами</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Step 3: Completion -->
                <div v-if="currentStep === 3" class="flex flex-col items-center text-center space-y-8 max-w-sm animate-in fade-in slide-in-from-right-4 duration-300">
                    <div class="flex items-center justify-center w-32 h-32 bg-gradient-to-br from-green-500/20 to-tg-accent/20 rounded-3xl border border-tg-section-separator">
                        <div class="flex items-center justify-center w-16 h-16 bg-green-500/10 rounded-2xl">
                            <AppLogoIcon class="size-8 text-tg-accent" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-2xl font-bold text-tg-text">
                            Все готово!
                        </h1>
                        <p class="text-tg-hint text-sm leading-relaxed">
                            Вітаємо в Horario. Організацію ваших {{ selectedRole === 'business' ? 'розкладів' : 'записів' }}.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="px-6 pb-6">
                <div v-if="currentStep === 1" class="flex gap-3">
                    <Button
                        class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90"
                        @click="goNext"
                    >
                        Розпочати
                    </Button>
                </div>

                <div v-if="currentStep === 3" class="flex gap-3">
                    <Button
                        class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90"
                        @click="completeOnboarding"
                    >
                        В кабінет
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
    animation: fade-in 300ms ease-out, slide-in-from-right-4 300ms ease-out;
}
</style>
