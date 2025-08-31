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
import { Carousel, CarouselContent, CarouselItem } from '@/components/ui/carousel';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Stepper, StepperIndicator, StepperItem, StepperTrigger } from '@/components/ui/stepper';
import { Textarea } from '@/components/ui/textarea';

// Icons
import { Building2, CheckCircle, MapPin, Phone, Sparkles, User, UserCheck } from 'lucide-vue-next';

// Onboarding state
const currentStep = ref(1);
const totalSteps = ref(3); // Will be dynamic based on role
const selectedRole = ref<string>('');
const savingRole = ref(false);
const showRoleWarning = ref(false);

// Form states for customer/business info
const customerForm = ref({
    name: '',
    phone: ''
});
const businessForm = ref({
    business_name: '',
    business_phone: '',
    address: ''
});
const savingInfo = ref(false);
const showFormWarning = ref(false);

// Progress calculation
const progress = computed(() => (currentStep.value / totalSteps.value) * 100);

// Carousel API reference
const carouselApi = ref<any>();

// Handle carousel slide changes
function onSlideChanged(index: number) {
    const newStep = index + 1; // Convert 0-based index to 1-based step

    // Prevent advancing past step 2 without role selection
    if (newStep > 2 && !selectedRole.value) {
        showRoleRequiredWarning();
        // Force carousel back to step 2
        setTimeout(() => {
            carouselApi.value?.scrollTo(1); // Go back to role selection step
        }, 100);
        return;
    }

    currentStep.value = newStep;
}

// Handle carousel API initialization
function onInitApi(api: any) {
    carouselApi.value = api;
}

// Navigation functions using carousel API
function goNext() {
    // Validate role selection on step 2
    if (currentStep.value === 2 && !selectedRole.value) {
        showRoleRequiredWarning();
        return;
    }
    carouselApi.value?.scrollNext();
}

// Sync carousel with currentStep
function goToStep(step: number) {
    // Validate role selection when trying to advance past step 2
    if (step > 2 && !selectedRole.value) {
        showRoleRequiredWarning();
        return;
    }
    carouselApi.value?.scrollTo(step - 1);
}

// Show role selection warning
function showRoleRequiredWarning() {
    showRoleWarning.value = true;
    // Auto-hide warning after 3 seconds
    setTimeout(() => {
        showRoleWarning.value = false;
    }, 3000);
}

// Handle stepper step change
function onStepperChange(step: number) {
    // Validate role selection when trying to advance past step 2
    if (step > 2 && !selectedRole.value) {
        showRoleRequiredWarning();
        return;
    }
    goToStep(step);
}

// Removed unused skipOnboarding function to satisfy lint

function selectRole(role: 'business' | 'customer') {
    selectedRole.value = role;
    savingRole.value = true;
    showRoleWarning.value = false; // Clear warning when role is selected

    // Set total steps based on role
    totalSteps.value = role === 'business' ? 5 : 4; // business: welcome, role, info, address, complete | customer: welcome, role, info, complete

    // Immediately save role to backend for dynamic flow
    router.post(
        '/onboarding/role',
        {
            role: role,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                savingRole.value = false;
                // Auto-advance to next step after backend confirms
                setTimeout(() => {
                    goNext();
                }, 300);
            },
            onError: (errors) => {
                console.error('Failed to save role:', errors);
                savingRole.value = false;
                // Still advance on error to prevent blocking UX
                setTimeout(() => {
                    goNext();
                }, 300);
            },
        },
    );
}

// Handle form validation and show warnings
function showFormRequiredWarning() {
    showFormWarning.value = true;
    setTimeout(() => {
        showFormWarning.value = false;
    }, 3000);
}

// Submit customer information
function submitCustomerInfo() {
    if (!customerForm.value.name || !customerForm.value.phone) {
        showFormRequiredWarning();
        return;
    }

    savingInfo.value = true;
    router.post(
        '/onboarding/customer-info',
        customerForm.value,
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                savingInfo.value = false;
                setTimeout(() => {
                    goNext();
                }, 300);
            },
            onError: (errors) => {
                console.error('Failed to save customer info:', errors);
                savingInfo.value = false;
                showFormRequiredWarning();
            },
        },
    );
}

// Submit business information
function submitBusinessInfo() {
    if (!businessForm.value.business_name || !businessForm.value.business_phone) {
        showFormRequiredWarning();
        return;
    }

    savingInfo.value = true;
    router.post(
        '/onboarding/business-info',
        {
            business_name: businessForm.value.business_name,
            business_phone: businessForm.value.business_phone,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                savingInfo.value = false;
                setTimeout(() => {
                    goNext();
                }, 300);
            },
            onError: (errors) => {
                console.error('Failed to save business info:', errors);
                savingInfo.value = false;
                showFormRequiredWarning();
            },
        },
    );
}

// Submit business address
function submitBusinessAddress() {
    if (!businessForm.value.address) {
        showFormRequiredWarning();
        return;
    }

    savingInfo.value = true;
    router.post(
        '/onboarding/business-address',
        {
            address: businessForm.value.address,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                savingInfo.value = false;
                setTimeout(() => {
                    goNext();
                }, 300);
            },
            onError: (errors) => {
                console.error('Failed to save business address:', errors);
                savingInfo.value = false;
                showFormRequiredWarning();
            },
        },
    );
}

function completeOnboarding() {
    // Complete onboarding (role already saved) and redirect
    router.post('/onboarding/complete', {
        // Role already saved in selectRole, just complete the flow
    });
}
</script>

<template>
    <Head title="Welcome to " />

    <TelegramAppLayout class="bg-tg-bg">
        <div class="flex min-h-[--spacing-viewport-h] flex-col">
            <!-- Header with Stepper -->
            <div class="flex justify-center p-4 pb-2">
                <!-- Stepper Navigation -->
                <Stepper :model-value="currentStep" class="max-w-sm" @step-change="onStepperChange">
                    <!-- Step 1: Welcome -->
                    <StepperItem :step="1">
                        <StepperTrigger :step="1" class="p-1">
                            <StepperIndicator :step="1" :icon="Sparkles" />
                        </StepperTrigger>
                    </StepperItem>

                    <div class="h-[2px] w-12 bg-tg-section-separator"></div>

                    <!-- Step 2: Role Selection -->
                    <StepperItem :step="2">
                        <StepperTrigger :step="2" class="p-1">
                            <StepperIndicator :step="2" :icon="UserCheck" />
                        </StepperTrigger>
                    </StepperItem>

                    <!-- Step 3: Info Collection (Customer/Business) -->
                    <template v-if="selectedRole">
                        <div class="h-[2px] w-12 bg-tg-section-separator"></div>
                        <StepperItem :step="3">
                            <StepperTrigger :step="3" class="p-1">
                                <StepperIndicator :step="3" :icon="selectedRole === 'customer' ? User : Building2" />
                            </StepperTrigger>
                        </StepperItem>

                        <!-- Step 4: Business Address (Business only) -->
                        <template v-if="selectedRole === 'business'">
                            <div class="h-[2px] w-12 bg-tg-section-separator"></div>
                            <StepperItem :step="4">
                                <StepperTrigger :step="4" class="p-1">
                                    <StepperIndicator :step="4" :icon="MapPin" />
                                </StepperTrigger>
                            </StepperItem>
                        </template>

                        <!-- Final Step: Completion -->
                        <div class="h-[2px] w-12 bg-tg-section-separator"></div>
                        <StepperItem :step="totalSteps">
                            <StepperTrigger :step="totalSteps" class="p-1">
                                <StepperIndicator :step="totalSteps" :icon="CheckCircle" />
                            </StepperTrigger>
                        </StepperItem>
                    </template>
                    
                    <!-- Fallback final step when no role selected -->
                    <template v-else>
                        <div class="h-[2px] w-12 bg-tg-section-separator"></div>
                        <StepperItem :step="3">
                            <StepperTrigger :step="3" :disabled="!selectedRole" class="p-1">
                                <StepperIndicator :step="3" :icon="CheckCircle" />
                            </StepperTrigger>
                        </StepperItem>
                    </template>
                </Stepper>
            </div>

            <!-- Progress Bar -->
            <div class="px-4 pb-6">
                <div class="h-1 w-full overflow-hidden rounded-full bg-tg-secondary-bg">
                    <div class="h-full rounded-full bg-tg-accent transition-all duration-300 ease-out" :style="`width: ${progress}%`"></div>
                </div>
            </div>

            <!-- Content Area with Carousel -->
            <div class="flex flex-1 flex-col items-center justify-center px-6 pb-8">
                <Carousel class="w-full max-w-sm" @slide-changed="onSlideChanged" @init-api="onInitApi">
                    <CarouselContent class="-ml-4">
                        <!-- Step 1: Welcome -->
                        <CarouselItem class="pl-4">
                            <div class="flex flex-col items-center space-y-8 text-center">
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
                                        Прощавайте,<br />
                                        записник та ручка
                                    </h1>
                                    <div class="space-y-3">
                                        <h2 class="text-xl font-semibold text-tg-text">
                                            Привіт,<br />
                                            Horario
                                        </h2>
                                        <p class="text-sm leading-relaxed text-tg-hint">Ваш розклад та записи в одному місці.</p>
                                    </div>
                                </div>
                            </div>
                        </CarouselItem>

                        <!-- Step 2: Role Selection -->
                        <CarouselItem class="pl-4">
                            <div class="flex flex-col items-center space-y-8 text-center">
                                <div class="space-y-4">
                                    <h1 class="text-2xl font-bold text-tg-text">Оберіть ваш профіль</h1>
                                    <p class="text-sm text-tg-hint">Допоможіть нам організувати ваш досвід</p>

                                    <!-- Warning Message -->
                                    <div
                                        v-if="showRoleWarning"
                                        class="rounded-lg border border-orange-500/20 bg-orange-500/10 p-3 transition-all duration-300"
                                    >
                                        <p class="text-sm font-medium text-orange-600">⚠️ Будь ласка, оберіть свій профіль щоб продовжити</p>
                                    </div>
                                </div>

                                <div class="w-full space-y-3">
                                    <!-- Business Option -->
                                    <Card
                                        class="cursor-pointer border-2 transition-all duration-200"
                                        :class="[
                                            selectedRole === 'business'
                                                ? 'border-tg-accent bg-tg-accent/5'
                                                : 'border-tg-section-separator bg-tg-section-bg hover:bg-tg-secondary-bg',
                                            !savingRole && 'hover:scale-[0.98] active:scale-95',
                                            savingRole && selectedRole === 'business' && 'opacity-75',
                                        ]"
                                        @click="!savingRole && selectRole('business')"
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
                                        class="cursor-pointer border-2 transition-all duration-200"
                                        :class="[
                                            selectedRole === 'customer'
                                                ? 'border-tg-accent bg-tg-accent/5'
                                                : 'border-tg-section-separator bg-tg-section-bg hover:bg-tg-secondary-bg',
                                            !savingRole && 'hover:scale-[0.98] active:scale-95',
                                            savingRole && selectedRole === 'customer' && 'opacity-75',
                                        ]"
                                        @click="!savingRole && selectRole('customer')"
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
                        </CarouselItem>

                        <!-- Step 3: Customer Information -->
                        <CarouselItem v-if="selectedRole === 'customer'" class="pl-4">
                            <div class="flex flex-col items-center space-y-8 text-center">
                                <div
                                    class="flex h-32 w-32 items-center justify-center rounded-3xl border border-tg-section-separator bg-gradient-to-br from-tg-link/20 to-blue-500/20"
                                >
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-tg-link/10">
                                        <User class="size-8 text-tg-link" />
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h1 class="text-2xl font-bold text-tg-text">{{ t('app.tell_us_about_yourself') }}</h1>
                                    <p class="text-sm text-tg-hint">{{ t('app.help_us_know_you_better') }}</p>

                                    <!-- Warning Message -->
                                    <div
                                        v-if="showFormWarning"
                                        class="rounded-lg border border-orange-500/20 bg-orange-500/10 p-3 transition-all duration-300"
                                    >
                                        <p class="text-sm font-medium text-orange-600">⚠️ Будь ласка, заповніть всі поля</p>
                                    </div>
                                </div>

                                <div class="w-full space-y-4">
                                    <div class="space-y-2">
                                        <Label for="customer-name" class="text-sm font-medium text-tg-text">{{ t('app.name') }}</Label>
                                        <Input
                                            id="customer-name"
                                            v-model="customerForm.name"
                                            :placeholder="t('app.full_name')"
                                            class="w-full"
                                            :disabled="savingInfo"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="customer-phone" class="text-sm font-medium text-tg-text">{{ t('app.phone_number') }}</Label>
                                        <Input
                                            id="customer-phone"
                                            v-model="customerForm.phone"
                                            :placeholder="t('app.phone_placeholder')"
                                            class="w-full"
                                            :disabled="savingInfo"
                                        />
                                    </div>
                                </div>
                            </div>
                        </CarouselItem>

                        <!-- Step 3: Business Information -->
                        <CarouselItem v-if="selectedRole === 'business'" class="pl-4">
                            <div class="flex flex-col items-center space-y-8 text-center">
                                <div
                                    class="flex h-32 w-32 items-center justify-center rounded-3xl border border-tg-section-separator bg-gradient-to-br from-tg-accent/20 to-purple-500/20"
                                >
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-tg-accent/10">
                                        <Building2 class="size-8 text-tg-accent" />
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h1 class="text-2xl font-bold text-tg-text">{{ t('app.tell_us_about_business') }}</h1>
                                    <p class="text-sm text-tg-hint">{{ t('app.business_details') }}</p>

                                    <!-- Warning Message -->
                                    <div
                                        v-if="showFormWarning"
                                        class="rounded-lg border border-orange-500/20 bg-orange-500/10 p-3 transition-all duration-300"
                                    >
                                        <p class="text-sm font-medium text-orange-600">⚠️ Будь ласка, заповніть всі поля</p>
                                    </div>
                                </div>

                                <div class="w-full space-y-4">
                                    <div class="space-y-2">
                                        <Label for="business-name" class="text-sm font-medium text-tg-text">{{ t('app.business_name') }}</Label>
                                        <Input
                                            id="business-name"
                                            v-model="businessForm.business_name"
                                            :placeholder="t('app.business_name_placeholder')"
                                            class="w-full"
                                            :disabled="savingInfo"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="business-phone" class="text-sm font-medium text-tg-text">{{ t('app.business_phone') }}</Label>
                                        <Input
                                            id="business-phone"
                                            v-model="businessForm.business_phone"
                                            :placeholder="t('app.phone_placeholder')"
                                            class="w-full"
                                            :disabled="savingInfo"
                                        />
                                    </div>
                                </div>
                            </div>
                        </CarouselItem>

                        <!-- Step 4: Business Address (Business only) -->
                        <CarouselItem v-if="selectedRole === 'business'" class="pl-4">
                            <div class="flex flex-col items-center space-y-8 text-center">
                                <div
                                    class="flex h-32 w-32 items-center justify-center rounded-3xl border border-tg-section-separator bg-gradient-to-br from-green-500/20 to-tg-accent/20"
                                >
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-green-500/10">
                                        <MapPin class="size-8 text-green-600" />
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h1 class="text-2xl font-bold text-tg-text">{{ t('app.location_information') }}</h1>
                                    <p class="text-sm text-tg-hint">{{ t('app.where_located') }}</p>

                                    <!-- Warning Message -->
                                    <div
                                        v-if="showFormWarning"
                                        class="rounded-lg border border-orange-500/20 bg-orange-500/10 p-3 transition-all duration-300"
                                    >
                                        <p class="text-sm font-medium text-orange-600">⚠️ Будь ласка, заповніть адресу</p>
                                    </div>
                                </div>

                                <div class="w-full space-y-4">
                                    <div class="space-y-2">
                                        <Label for="business-address" class="text-sm font-medium text-tg-text">{{ t('app.address') }}</Label>
                                        <Textarea
                                            id="business-address"
                                            v-model="businessForm.address"
                                            :placeholder="t('app.address_placeholder')"
                                            class="w-full"
                                            :disabled="savingInfo"
                                            rows="3"
                                        />
                                    </div>
                                </div>
                            </div>
                        </CarouselItem>

                        <!-- Final Step: Completion -->
                        <CarouselItem class="pl-4">
                            <div class="flex flex-col items-center space-y-8 text-center">
                                <div
                                    class="flex h-32 w-32 items-center justify-center rounded-3xl border border-tg-section-separator bg-gradient-to-br from-green-500/20 to-tg-accent/20"
                                >
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-green-500/10">
                                        <AppLogoIcon class="size-8 text-tg-accent" />
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h1 class="text-2xl font-bold text-tg-text">{{ t('app.profile_setup_complete') }}</h1>
                                    <p class="text-sm leading-relaxed text-tg-hint">
                                        {{ t('app.welcome_horario_organize') }} {{ selectedRole === 'business' ? t('app.schedules') : t('app.appointments') }}.
                                    </p>
                                </div>
                            </div>
                        </CarouselItem>
                    </CarouselContent>
                </Carousel>
            </div>

            <!-- Bottom Actions -->
            <div class="px-6 pb-6">
                <!-- Step 1: Welcome -->
                <div v-if="currentStep === 1" class="flex gap-3">
                    <Button class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90" @click="goNext"> Розпочати </Button>
                </div>

                <!-- Step 3: Customer Info Collection -->
                <div v-if="currentStep === 3 && selectedRole === 'customer'" class="flex gap-3">
                    <Button 
                        class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90" 
                        @click="submitCustomerInfo"
                        :disabled="savingInfo"
                    >
                        <span v-if="savingInfo">Збереження...</span>
                        <span v-else>{{ t('app.continue') }}</span>
                    </Button>
                </div>

                <!-- Step 3: Business Info Collection -->
                <div v-if="currentStep === 3 && selectedRole === 'business'" class="flex gap-3">
                    <Button 
                        class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90" 
                        @click="submitBusinessInfo"
                        :disabled="savingInfo"
                    >
                        <span v-if="savingInfo">Збереження...</span>
                        <span v-else>{{ t('app.continue') }}</span>
                    </Button>
                </div>

                <!-- Step 4: Business Address Collection -->
                <div v-if="currentStep === 4 && selectedRole === 'business'" class="flex gap-3">
                    <Button 
                        class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90" 
                        @click="submitBusinessAddress"
                        :disabled="savingInfo"
                    >
                        <span v-if="savingInfo">Збереження...</span>
                        <span v-else>{{ t('app.continue') }}</span>
                    </Button>
                </div>

                <!-- Final Step: Completion -->
                <div v-if="currentStep === totalSteps" class="flex gap-3">
                    <Button class="flex-1 bg-tg-accent text-tg-accent-foreground hover:opacity-90" @click="completeOnboarding"> В кабінет </Button>
                </div>
            </div>
        </div>
    </TelegramAppLayout>
</template>

<style scoped>
/* Custom styles for carousel-based onboarding */
</style>
