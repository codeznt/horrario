<template>
    <Head :title="t('app.schedule')" />

    <AppLayoutWithNavigation>
        <div class="min-h-full bg-tg-bg">
            <!-- Enhanced Header with Gradient Background -->
            <div class="relative overflow-hidden bg-gradient-to-r from-tg-accent to-purple-600 px-4 py-6">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjAuNSIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+')] opacity-10"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ t('app.my_schedule') }}</h1>
                        <p class="mt-1 text-sm text-white/90">{{ t('app.working_hours') }}</p>
                    </div>
                    <Button @click="showAddDialog = true" class="flex items-center gap-2 bg-white text-tg-accent hover:bg-white/90 shadow-lg">
                        <Icon name="Plus" class="h-4 w-4" />
                        {{ t('app.add_time_slot') }}
                    </Button>
                </div>
            </div>

            <div class="container mx-auto px-4 py-6">
                <div class="mb-6 grid gap-5">
                    <Card 
                        v-for="(dayName, dayNumber) in days" 
                        :key="dayNumber" 
                        class="border-tg-section-separator bg-gradient-to-br from-white to-tg-section-bg shadow-sm"
                    >
                        <CardHeader class="pb-4">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-lg font-semibold text-tg-text">{{ dayName }}</CardTitle>
                                <Button 
                                    variant="outline" 
                                    size="sm" 
                                    @click="openAddDialog(dayNumber)" 
                                    class="flex items-center gap-1 border-tg-section-separator text-tg-text hover:bg-tg-section-separator/20"
                                >
                                    <Icon name="Plus" class="h-3 w-3" />
                                    {{ t('app.add') }}
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div v-if="!schedules[dayNumber] || schedules[dayNumber].length === 0" class="py-8 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-tg-section-separator/20">
                                    <Icon name="Clock" class="h-6 w-6 text-tg-hint" />
                                </div>
                                <p class="text-tg-hint">{{ t('app.no_working_hours_set') }}</p>
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="schedule in schedules[dayNumber]"
                                    :key="schedule.id"
                                    class="flex items-center justify-between rounded-lg border border-tg-section-separator bg-white p-4 shadow-sm"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-tg-accent/10">
                                            <Icon name="Clock" class="h-5 w-5 text-tg-accent" />
                                        </div>
                                        <div>
                                            <span class="font-medium text-tg-text"> {{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }} </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Button 
                                            variant="ghost" 
                                            size="sm" 
                                            @click="editSchedule(schedule)" 
                                            class="h-8 w-8 p-0 hover:bg-tg-section-separator/20"
                                        >
                                            <Icon name="Edit" class="h-4 w-4 text-tg-hint" />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            @click="deleteSchedule(schedule)"
                                            class="h-8 w-8 p-0 text-red-600 hover:bg-red-500/10 hover:text-red-700"
                                        >
                                            <Icon name="Trash2" class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayoutWithNavigation>

    <!-- Enhanced Add/Edit Schedule Dialog -->
    <Dialog :open="showAddDialog" @update:open="showAddDialog = $event">
        <DialogContent class="sm:max-w-md rounded-xl border-tg-section-separator bg-white shadow-xl">
            <DialogHeader class="text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-tg-accent/10">
                    <Icon :name="editingSchedule ? 'Edit' : 'Plus'" class="h-6 w-6 text-tg-accent" />
                </div>
                <DialogTitle class="text-xl font-bold text-tg-text">
                    {{ editingSchedule ? t('app.edit_time_slot') : t('app.add_time_slot') }}
                </DialogTitle>
                <DialogDescription class="text-tg-hint">
                    {{ t('app.set_working_hours_for', { day: selectedDay ? days[selectedDay] : '' }) }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitSchedule" class="space-y-5">
                <div v-if="!editingSchedule" class="space-y-2">
                    <Label for="day" class="text-sm font-medium text-tg-text">{{ t('app.day_of_week') }}</Label>
                    <select
                        id="day"
                        v-model="form.day_of_week"
                        class="flex h-11 w-full rounded-lg border border-tg-section-separator bg-white px-3 py-2 text-sm focus:border-tg-accent focus:ring-tg-accent focus:ring-1 focus:ring-offset-0 disabled:cursor-not-allowed disabled:opacity-50"
                        required
                    >
                        <option value="">{{ t('app.select_a_day') }}</option>
                        <option v-for="(dayName, dayNumber) in days" :key="dayNumber" :value="dayNumber">
                            {{ dayName }}
                        </option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="start_time" class="text-sm font-medium text-tg-text">{{ t('app.start_time') }}</Label>
                        <div class="relative">
                            <Input 
                                id="start_time" 
                                v-model="form.start_time" 
                                type="time" 
                                required 
                                :class="{ 'border-red-500': errors.start_time }"
                                class="border-tg-section-separator bg-white focus:border-tg-accent focus:ring-tg-accent"
                            />
                            <Icon name="Clock" class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-tg-hint" />
                        </div>
                        <p v-if="errors.start_time" class="text-sm text-red-600">{{ errors.start_time }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="end_time" class="text-sm font-medium text-tg-text">{{ t('app.end_time') }}</Label>
                        <div class="relative">
                            <Input 
                                id="end_time" 
                                v-model="form.end_time" 
                                type="time" 
                                required 
                                :class="{ 'border-red-500': errors.end_time }"
                                class="border-tg-section-separator bg-white focus:border-tg-accent focus:ring-tg-accent"
                            />
                            <Icon name="Clock" class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-tg-hint" />
                        </div>
                        <p v-if="errors.end_time" class="text-sm text-red-600">{{ errors.end_time }}</p>
                    </div>
                </div>

                <p v-if="errors.time_conflict" class="text-sm text-red-600">{{ errors.time_conflict }}</p>

                <DialogFooter class="flex-col sm:flex-col gap-3">
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="closeDialog"
                        class="w-full border-tg-section-separator text-tg-text hover:bg-tg-section-separator/20"
                    >
                        {{ t('app.cancel') }}
                    </Button>
                    <Button 
                        type="submit" 
                        :disabled="processing"
                        class="w-full bg-tg-accent text-white hover:bg-tg-accent/90"
                    >
                        <Icon v-if="processing" name="Loader2" class="mr-2 h-4 w-4 animate-spin" />
                        {{ editingSchedule ? t('app.update_time_slot') : t('app.add_time_slot') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Enhanced Delete Confirmation Dialog -->
    <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
        <AlertDialogContent class="max-w-md rounded-xl border-tg-section-separator bg-white shadow-xl">
            <AlertDialogHeader class="text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10">
                    <Icon name="AlertTriangle" class="h-6 w-6 text-red-600" />
                </div>
                <AlertDialogTitle class="text-xl font-bold text-tg-text">{{ t('app.delete_time_slot') }}</AlertDialogTitle>
                <AlertDialogDescription class="text-tg-hint">
                    {{ t('app.delete_time_slot_confirmation') }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter class="flex-col sm:flex-col gap-3">
                <AlertDialogCancel class="w-full border-tg-section-separator bg-tg-section-bg text-tg-text hover:bg-tg-section-separator/20">
                    {{ t('app.cancel') }}
                </AlertDialogCancel>
                <AlertDialogAction 
                    @click="confirmDelete" 
                    class="w-full bg-red-600 text-white hover:bg-red-700"
                >
                    {{ t('app.delete') }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslations } from '@/composables/useTranslations';
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayoutWithNavigation from '@/layouts/AppLayoutWithNavigation.vue';
import Icon from '@/components/Icon.vue';
import { ref } from 'vue';

interface Schedule {
    id: number;
    day_of_week: number;
    start_time: string;
    end_time: string;
}

defineProps<{
    schedules: Record<number, Schedule[]>;
    days: Record<number, string>;
}>();
const { t } = useTranslations();

const showAddDialog = ref(false);
const showDeleteDialog = ref(false);
const editingSchedule = ref<Schedule | null>(null);
const scheduleToDelete = ref<Schedule | null>(null);
const selectedDay = ref<number | null>(null);
const processing = ref(false);
const errors = ref<Record<string, string>>({});

const form = useForm({
    day_of_week: '',
    start_time: '',
    end_time: '',
});

const formatTime = (time: string) => {
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const openAddDialog = (dayNumber: number) => {
    selectedDay.value = dayNumber;
    form.day_of_week = dayNumber.toString();
    showAddDialog.value = true;
};

const editSchedule = (schedule: Schedule) => {
    editingSchedule.value = schedule;
    selectedDay.value = schedule.day_of_week;
    form.day_of_week = schedule.day_of_week.toString();
    form.start_time = schedule.start_time;
    form.end_time = schedule.end_time;
    showAddDialog.value = true;
};

const deleteSchedule = (schedule: Schedule) => {
    scheduleToDelete.value = schedule;
    showDeleteDialog.value = true;
};

const submitSchedule = () => {
    processing.value = true;
    errors.value = {};

    if (editingSchedule.value) {
        form.put(`/schedules/${editingSchedule.value.id}`, {
            onSuccess: () => {
                closeDialog();
            },
            onError: (formErrors) => {
                errors.value = formErrors;
                processing.value = false;
            },
            onFinish: () => {
                processing.value = false;
            },
        });
    } else {
        form.post('/schedules', {
            onSuccess: () => {
                closeDialog();
            },
            onError: (formErrors) => {
                errors.value = formErrors;
                processing.value = false;
            },
            onFinish: () => {
                processing.value = false;
            },
        });
    }
};

const confirmDelete = () => {
    if (scheduleToDelete.value) {
        router.delete(`/schedules/${scheduleToDelete.value.id}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                scheduleToDelete.value = null;
            },
        });
    }
};

const closeDialog = () => {
    showAddDialog.value = false;
    editingSchedule.value = null;
    selectedDay.value = null;
    form.reset();
    errors.value = {};
};
</script>
