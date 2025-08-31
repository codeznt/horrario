<template>
    <Head :title="t('app.book_service')" />

    <AppLayoutWithNavigation :title="service.title" :subtitle="t('app.book_your_appointment')" :show-back-button="true">
        <!-- Service Info Header -->
        <Card class="mb-6 bg-tg-section-bg">
            <CardContent class="p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h2 class="mb-2 text-lg font-semibold">{{ service.title }}</h2>
                        <p v-if="service.description" class="mb-3 text-sm text-tg-subtitle-text">
                            {{ service.description }}
                        </p>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-1 text-tg-hint">
                                <Icon name="Clock" class="h-4 w-4" />
                                {{ service.duration_minutes }} {{ t('app.minutes') }}
                            </div>
                            <div class="flex items-center gap-1 text-tg-hint">
                                <Icon name="MapPin" class="h-4 w-4" />
                                {{ provider.business_name }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-tg-accent">{{ service.display_price }}</div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Date Selection -->
        <Card class="mb-6">
            <CardHeader>
                <CardTitle>{{ t('app.select_date_time') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <!-- Date Grid -->
                <div>
                    <Label class="mb-2 block text-sm font-medium">{{ t('app.available_dates') }}</Label>
                    <div class="grid grid-cols-2 gap-2">
                        <Button
                            v-for="dateOption in dates"
                            :key="dateOption.date"
                            type="button"
                            :variant="selectedDate === dateOption.date ? 'default' : 'outline'"
                            @click="selectDate(dateOption)"
                            :disabled="dateOption.slots.length === 0"
                            class="flex h-auto flex-col items-start p-3"
                        >
                            <span class="font-medium">{{ dateOption.day_name }}</span>
                            <span class="text-sm opacity-75">{{ dateOption.day_number }} {{ dateOption.month }}</span>
                            <span v-if="dateOption.slots.length === 0" class="text-xs text-red-500">{{ t('app.no_slots') }}</span>
                        </Button>
                    </div>
                </div>

                <!-- Time Slots -->
                <div v-if="selectedDate && selectedDateData">
                    <Label class="mb-2 block text-sm font-medium">{{ t('app.available_times') }}</Label>
                    <div class="grid grid-cols-3 gap-2">
                        <Button
                            v-for="slot in selectedDateData.slots"
                            :key="slot.start"
                            type="button"
                            :variant="selectedTime === slot.start ? 'default' : 'outline'"
                            @click="selectedTime = slot.start"
                            :disabled="!slot.available"
                            class="text-sm"
                        >
                            {{ formatTime(slot.start) }}
                        </Button>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <Label for="notes" class="mb-2 block text-sm font-medium">{{ t('app.notes_optional') }}</Label>
                    <Textarea id="notes" v-model="form.notes" :placeholder="t('app.special_requests')" :rows="3" />
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <Button @click="submitBooking" :disabled="!selectedDate || !selectedTime || processing" class="w-full" size="lg">
                        <Icon v-if="processing" name="Loader2" class="mr-2 h-4 w-4 animate-spin" />
                        <Icon v-else name="Calendar" class="mr-2 h-4 w-4" />
                        {{ processing ? t('app.creating_booking') : t('app.book_appointment') }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Loading Overlay -->
        <LoadingOverlay :show="processing" :message="t('app.creating_your_booking')" />
    </AppLayoutWithNavigation>
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
