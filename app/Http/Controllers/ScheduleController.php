<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the provider's weekly schedule.
     */
    public function index()
    {
        $provider = auth()->user()->provider;
        
        if (!$provider) {
            return redirect()->route('provider.create')
                ->with('error', 'You need to create a provider profile first.');
        }

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

        return Inertia::render('Schedule/Index', [
            'schedules' => $schedules,
            'days' => $days
        ]);
    }

    /**
     * Store a new schedule time slot.
     */
    public function store(Request $request)
    {
        $provider = auth()->user()->provider;
        
        if (!$provider) {
            return redirect()->route('provider.create')
                ->with('error', 'You need to create a provider profile first.');
        }

        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        // Check for overlapping schedules
        $overlapping = $provider->schedules()
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_time', '<=', $validated['start_time'])
                          ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors([
                'time_conflict' => 'This time slot overlaps with an existing schedule.'
            ]);
        }

        $schedule = new Schedule($validated);
        $schedule->provider_id = $provider->id;
        $schedule->save();

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule time slot added successfully!');
    }

    /**
     * Update an existing schedule time slot.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        // Check for overlapping schedules (excluding current one)
        $overlapping = $schedule->provider->schedules()
            ->where('day_of_week', $schedule->day_of_week)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_time', '<=', $validated['start_time'])
                          ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors([
                'time_conflict' => 'This time slot overlaps with an existing schedule.'
            ]);
        }

        $schedule->update($validated);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule time slot updated successfully!');
    }

    /**
     * Remove a schedule time slot.
     */
    public function destroy(Schedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $schedule->delete();

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule time slot deleted successfully!');
    }
}
