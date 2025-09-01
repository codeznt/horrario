<template>
    <Head :title="t('app.book_service')" />

    <AppLayoutWithNavigation>
        <div class="min-h-full bg-tg-bg">
            <!-- Enhanced Header with Gradient Background -->
            <div class="relative overflow-hidden bg-gradient-to-r from-tg-link to-blue-500 px-4 py-6">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjAuNSIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+')] opacity-10"></div>
                <div class="relative">
                    <Button 
                        variant="ghost" 
                        @click="router.visit('/browse/services')" 
                        class="mb-4 -ml-2 flex items-center gap-2 text-white/90 hover:bg-white/20"
                    >
                        <Icon name="ArrowLeft" class="h-4 w-4" />
                        {{ t('app.back') }}
                    </Button>
                    <h1 class="text-2xl font-bold text-white truncate">{{ service.title }}</h1>
                    <p class="mt-1 text-sm text-white/90">{{ t('app.book_your_appointment') }}</p>
                </div>
            </div>

            <div class="container mx-auto px-4 py-6">
                <!-- Enhanced Service Info Header -->
                <Card class="mb-6 border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm">
                    <CardContent class="p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex-1">
                                <h2 class="mb-2 text-xl font-bold text-tg-text">{{ service.title }}</h2>
                                <p v-if="service.description" class="mb-4 text-tg-hint">
                                    {{ service.description }}
                                </p>
                                <div class="flex flex-wrap items-center gap-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-tg-link/10">
                                            <Icon name="Clock" class="h-4 w-4 text-tg-link" />
                                        </div>
                                        <span class="font-medium text-tg-text">{{ service.duration_minutes }} {{ t('app.minutes') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-tg-link/10">
                                            <Icon name="MapPin" class="h-4 w-4 text-tg-link" />
                                        </div>
                                        <span class="font-medium text-tg-text">{{ provider.business_name }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="rounded-lg bg-tg-link/10 px-4 py-2">
                                    <div class="text-2xl font-bold text-tg-link">{{ service.display_price }}</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Enhanced Date Selection -->
                <Card class="mb-6 border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm">
                    <CardHeader class="border-b border-tg-section-separator pb-4">
                        <CardTitle class="text-lg font-bold text-tg-text">{{ t('app.select_date_time') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6 pt-5">
                        <!-- Date Grid -->
                        <div>
                            <Label class="mb-3 block text-sm font-medium text-tg-text">{{ t('app.available_dates') }}</Label>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                <Button
                                    v-for="dateOption in dates"
                                    :key="dateOption.date"
                                    type="button"
                                    :variant="selectedDate === dateOption.date ? 'default' : 'outline'"
                                    @click="selectDate(dateOption)"
                                    :disabled="dateOption.slots.length === 0"
                                    class="flex h-auto flex-col items-center justify-center gap-1 p-3"
                                    :class="selectedDate === dateOption.date ? 'bg-tg-link text-white hover:bg-tg-link/90' : 'border-tg-section-separator bg-white text-tg-text hover:bg-tg-section-separator/20'"
                                >
                                    <span class="font-medium text-sm">{{ dateOption.day_short }}</span>
                                    <span class="text-lg font-bold">{{ dateOption.day_number }}</span>
                                    <span class="text-xs opacity-75">{{ dateOption.month }}</span>
                                    <span v-if="dateOption.slots.length === 0" class="text-xs text-red-600">{{ t('app.no_slots') }}</span>
                                </Button>
                            </div>
                        </div>

                        <!-- Time Slots -->
                        <div v-if="selectedDate && selectedDateData">
                            <Label class="mb-3 block text-sm font-medium text-tg-text">{{ t('app.available_times') }}</Label>
                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5">
                                <Button
                                    v-for="slot in selectedDateData.slots"
                                    :key="slot.start"
                                    type="button"
                                    :variant="selectedTime === slot.start ? 'default' : 'outline'"
                                    @click="selectedTime = slot.start"
                                    :disabled="!slot.available"
                                    class="text-sm py-2"
                                    :class="selectedTime === slot.start ? 'bg-tg-link text-white hover:bg-tg-link/90' : 'border-tg-section-separator bg-white text-tg-text hover:bg-tg-section-separator/20'"
                                >
                                    {{ formatTime(slot.start) }}
                                </Button>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <Label for="notes" class="mb-2 block text-sm font-medium text-tg-text">{{ t('app.notes_optional') }}</Label>
                            <Textarea 
                                id="notes" 
                                v-model="form.notes" 
                                :placeholder="t('app.special_requests')" 
                                :rows="3" 
                                class="border-tg-section-separator bg-white focus:border-tg-link focus:ring-tg-link"
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <Button 
                                @click="submitBooking" 
                                :disabled="!selectedDate || !selectedTime || processing" 
                                size="lg"
                                class="w-full bg-tg-link text-white hover:bg-tg-link/90"
                            >
                                <Icon v-if="processing" name="Loader2" class="mr-2 h-4 w-4 animate-spin" />
                                <Icon v-else name="Calendar" class="mr-2 h-4 w-4" />
                                {{ processing ? t('app.creating_booking') : t('app.book_appointment') }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayoutWithNavigation>

    <!-- Enhanced Loading Overlay -->
    <LoadingOverlay :show="processing" :message="t('app.creating_your_booking')" />
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import LoadingOverlay from '@/components/LoadingOverlay.vue';
import { Button, Card, CardContent, CardHeader, CardTitle, Label, Textarea } from '@/components/ui';
import { useTranslations } from '@/composables/useTranslations';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { t } = useTranslations();

interface Service {
    id: number;
    title: string;
    description?: string;
    duration_minutes: number;
    display_price: string;
    provider: Provider;
}

interface Provider {
    id: number;
    business_name: string;
    user: {
        name: string;
    };
}

interface TimeSlot {
    start: string;
    end: string;
    datetime: string;
    available: boolean;
}

interface DateSlot {
    date: string;
    day_name: string;
    day_short: string;
    day_number: string;
    month: string;
    slots: TimeSlot[];
}

const props = defineProps<{
    service: Service;
    provider: Provider;
    dates: DateSlot[];
}>();

const selectedDate = ref<string | null>(null);
const selectedTime = ref<string | null>(null);
const processing = ref(false);
const errors = ref<Record<string, string>>({});

const form = useForm({
    service_id: props.service.id,
    date: '',
    start_time: '',
    notes: '',
});

const selectedDateData = computed(() => {
    return selectedDate.value ? props.dates.find((d) => d.date === selectedDate.value) : null;
});

const selectDate = (date: DateSlot) => {
    if (date.slots.length === 0) return;

    selectedDate.value = date.date;
    selectedTime.value = null;
    form.date = date.date;
};

const formatTime = (time: string) => {
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const submitBooking = () => {
    if (!selectedDate.value || !selectedTime.value) return;

    processing.value = true;
    errors.value = {};

    form.date = selectedDate.value;
    form.start_time = selectedTime.value;

    form.post('/bookings', {
        onSuccess: () => {
            // Redirect handled by controller
        },
        onError: (formErrors) => {
            errors.value = formErrors;
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>
