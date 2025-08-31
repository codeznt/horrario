<template>
    <Head :title="t('app.schedule')" />

    <AppLayoutWithNavigation :title="t('app.my_schedule')" :subtitle="t('app.working_hours')">

        <div class="container mx-auto px-4 py-4">
            <div class="mb-6 flex items-center justify-end">
                <Button @click="showAddDialog = true" class="flex items-center gap-2">
                    <Icon name="Plus" class="h-4 w-4" />
                    {{ t('app.add_time_slot') }}
                </Button>
            </div>

            <div class="grid gap-6">
                <Card v-for="(dayName, dayNumber) in days" :key="dayNumber" class="overflow-hidden">
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-lg">{{ dayName }}</CardTitle>
                            <Button variant="outline" size="sm" @click="openAddDialog(dayNumber)" class="flex items-center gap-1">
                                <Icon name="Plus" class="h-3 w-3" />
                                {{ t('app.add') }}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="!schedules[dayNumber] || schedules[dayNumber].length === 0" class="py-8 text-center text-muted-foreground">
                            <Icon name="Clock" class="mx-auto mb-2 h-8 w-8 opacity-50" />
                            <p>{{ t('app.no_working_hours_set') }}</p>
                        </div>
                        <div v-else class="space-y-2">
                            <div
                                v-for="schedule in schedules[dayNumber]"
                                :key="schedule.id"
                                class="flex items-center justify-between rounded-lg bg-muted p-3"
                            >
                                <div class="flex items-center gap-2">
                                    <Icon name="Clock" class="h-4 w-4 text-muted-foreground" />
                                    <span class="font-medium"> {{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }} </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="sm" @click="editSchedule(schedule)" class="h-8 w-8 p-0">
                                        <Icon name="Edit" class="h-3 w-3" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="deleteSchedule(schedule)"
                                        class="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                    >
                                        <Icon name="Trash2" class="h-3 w-3" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    <!-- Add/Edit Schedule Dialog -->
    </AppLayoutWithNavigation>

    <Dialog :open="showAddDialog" @update:open="showAddDialog = $event">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>
                    {{ editingSchedule ? t('app.edit_time_slot') : t('app.add_time_slot') }}
                </DialogTitle>
                <DialogDescription>
                    {{ t('app.set_working_hours_for', { day: selectedDay ? days[selectedDay] : '' }) }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitSchedule" class="space-y-4">
                <div v-if="!editingSchedule" class="space-y-2">
                    <Label for="day">{{ t('app.day_of_week') }}</Label>
                    <select
                        id="day"
                        v-model="form.day_of_week"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
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
                        <Label for="start_time">{{ t('app.start_time') }}</Label>
                        <Input id="start_time" v-model="form.start_time" type="time" required :class="{ 'border-destructive': errors.start_time }" />
                        <p v-if="errors.start_time" class="text-sm text-destructive">{{ errors.start_time }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="end_time">{{ t('app.end_time') }}</Label>
                        <Input id="end_time" v-model="form.end_time" type="time" required :class="{ 'border-destructive': errors.end_time }" />
                        <p v-if="errors.end_time" class="text-sm text-destructive">{{ errors.end_time }}</p>
                    </div>
                </div>

                <p v-if="errors.time_conflict" class="text-sm text-destructive">{{ errors.time_conflict }}</p>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="closeDialog">
                        {{ t('app.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="processing">
                        <Icon v-if="processing" name="Loader2" class="mr-2 h-4 w-4 animate-spin" />
                        {{ editingSchedule ? t('app.update_time_slot') : t('app.add_time_slot') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
        <AlertDialogContent>
            <AlertDialogHeader>
        <AlertDialogTitle>{{ t('app.delete_time_slot') }}</AlertDialogTitle>
                <AlertDialogDescription>
          {{ t('app.delete_time_slot_confirmation') }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
        <AlertDialogCancel>{{ t('app.cancel') }}</AlertDialogCancel>
                <AlertDialogAction @click="confirmDelete" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
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
