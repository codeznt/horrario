<?php

namespace App\Services;

use App\Models\Provider;
use Carbon\Carbon;

class TimeSlotService
{
    /**
     * Generate available time slots for a provider on a specific date.
     */
    /**
     * Helper method to safely parse a date and time without double date specification issues
     */
    private function safelyParseDateTime(string $date, string $time): Carbon
    {
        try {
            // First, try to extract just the date part from the date string
            // This handles cases where $date might already contain a time component
            $dateObj = null;

            // Check if the date string contains multiple dates
            if (preg_match('/\d{4}-\d{2}-\d{2}.*\d{4}-\d{2}-\d{2}/', $date)) {
                // Extract just the first date if there are multiple dates
                preg_match('/^(\d{4}-\d{2}-\d{2})/', $date, $matches);
                if (! empty($matches[1])) {
                    $dateObj = $matches[1];
                }
            } else {
                // Normal case - just extract the date part
                $dateObj = Carbon::parse($date)->format('Y-m-d');
            }

            // If we couldn't extract a date, use today's date as fallback
            if (! $dateObj) {
                $dateObj = Carbon::today()->format('Y-m-d');
            }

            // Extract only the time part from the time string
            $timeObj = Carbon::parse($time)->format('H:i:s');

            // Combine them safely
            return Carbon::parse($dateObj.' '.$timeObj);
        } catch (\Exception $e) {
            // Fallback to current date with the provided time
            $dateObj = Carbon::today()->format('Y-m-d');
            try {
                $timeObj = Carbon::parse($time)->format('H:i:s');

                return Carbon::parse($dateObj.' '.$timeObj);
            } catch (\Exception $e2) {
                // Ultimate fallback
                return Carbon::now();
            }
        }
    }

    public function generateTimeSlots(Provider $provider, string $date, int $slotDurationMinutes = 30): array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedules = $provider->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $slots = [];

        foreach ($schedules as $schedule) {
            // Use the helper method to safely parse dates and times
            $start = $this->safelyParseDateTime($date, $schedule->start_time);
            $end = $this->safelyParseDateTime($date, $schedule->end_time);

            $current = clone $start;

            // Generate slots based on duration
            while ($current->lt($end)) {
                $slotEnd = (clone $current)->addMinutes($slotDurationMinutes);

                if ($slotEnd->lte($end)) {
                    $slots[] = [
                        'start' => $current->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'datetime' => $current->toISOString(),
                        'available' => true, // This can be updated based on bookings
                    ];
                }

                $current->addMinutes($slotDurationMinutes);
            }
        }

        return $slots;
    }

    /**
     * Get all time slots for a provider for the next 7 days.
     */
    public function getWeeklyTimeSlots(Provider $provider, int $slotDurationMinutes = 30): array
    {
        $weeklySlots = [];
        $today = Carbon::today();

        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');

            $weeklySlots[$dateString] = [
                'date' => $dateString,
                'day_name' => $date->format('l'),
                'slots' => $this->generateTimeSlots($provider, $dateString, $slotDurationMinutes),
            ];
        }

        return $weeklySlots;
    }

    /**
     * Check if a provider is available at a specific time.
     */
    public function isProviderAvailable(Provider $provider, string $datetime): bool
    {
        // Safely parse the datetime to avoid double date specification issues
        try {
            $carbon = Carbon::parse($datetime);
            $dayOfWeek = $carbon->dayOfWeek;
            $time = $carbon->format('H:i');

            $schedule = $provider->schedules()
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $time)
                ->where('end_time', '>', $time)
                ->first();

            return $schedule !== null;
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            return false;
        }
    }

    /**
     * Get formatted schedule for display.
     */
    public function getFormattedSchedule(Provider $provider): array
    {
        $schedules = $provider->schedules()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $formattedSchedule = [];

        foreach ($days as $dayNumber => $dayName) {
            $daySchedules = $schedules->get($dayNumber, collect());

            $formattedSchedule[$dayNumber] = [
                'day_name' => $dayName,
                'day_number' => $dayNumber,
                'time_slots' => $daySchedules->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'formatted' => $this->safelyParseDateTime('2000-01-01', $schedule->start_time)->format('g:i A').
                                     ' - '.
                                     $this->safelyParseDateTime('2000-01-01', $schedule->end_time)->format('g:i A'),
                    ];
                })->toArray(),
            ];
        }

        return $formattedSchedule;
    }
}
