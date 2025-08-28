<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Services\TimeSlotService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BookingController extends Controller
{
    use AuthorizesRequests;

    protected $timeSlotService;

    public function __construct(TimeSlotService $timeSlotService)
    {
        $this->timeSlotService = $timeSlotService;
    }

    /**
     * Display a listing of the user's bookings grouped by date.
     */
    public function index()
    {
        $bookings = auth()->user()->bookings()
            ->with(['service', 'provider.user'])
            ->orderBy('start_datetime')
            ->get()
            ->groupBy(function ($booking) {
                return Carbon::parse($booking->start_datetime)->format('Y-m-d');
            });

        return Inertia::render('Booking/Index', [
            'bookingsByDate' => $bookings
        ]);
    }

    /**
     * Display upcoming bookings for the user.
     */
    public function upcoming()
    {
        $bookings = auth()->user()->bookings()
            ->with(['service', 'provider.user'])
            ->where('start_datetime', '>=', Carbon::now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_datetime')
            ->get();

        return Inertia::render('Booking/Upcoming', [
            'bookings' => $bookings
        ]);
    }

    /**
     * Display past bookings for the user.
     */
    public function past()
    {
        $bookings = auth()->user()->bookings()
            ->with(['service', 'provider.user'])
            ->where('start_datetime', '<', Carbon::now())
            ->orderBy('start_datetime', 'desc')
            ->get();

        return Inertia::render('Booking/Past', [
            'bookings' => $bookings
        ]);
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(Service $service)
    {
        $provider = $service->provider;
        
        if (!$provider) {
            return redirect()->back()
                ->with('error', 'Service provider not found.');
        }

        // Get next 7 days with available slots
        $dates = [];
        $startDate = Carbon::today();

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');
            
            // Generate time slots for this date
            $slots = $this->timeSlotService->generateTimeSlots($provider, $dateString, $service->duration_minutes);
            
            // Filter out slots that are already booked
            $bookedSlots = Booking::where('provider_id', $provider->id)
                ->whereDate('start_datetime', $date)
                ->where('status', '!=', 'cancelled')
                ->get()
                ->map(function ($booking) {
                    return [
                        'start' => Carbon::parse($booking->start_datetime)->format('H:i'),
                        'end' => Carbon::parse($booking->end_datetime)->format('H:i')
                    ];
                })->toArray();

            $availableSlots = array_filter($slots, function ($slot) use ($bookedSlots) {
                foreach ($bookedSlots as $bookedSlot) {
                    if ($slot['start'] === $bookedSlot['start']) {
                        return false;
                    }
                }
                return true;
            });

            $dates[] = [
                'date' => $dateString,
                'day_name' => $date->format('l'),
                'day_short' => $date->format('D'),
                'day_number' => $date->format('j'),
                'month' => $date->format('M'),
                'slots' => array_values($availableSlots)
            ];
        }

        return Inertia::render('Booking/Create', [
            'service' => $service->load('provider.user'),
            'provider' => $provider->load('user'),
            'dates' => $dates
        ]);
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500'
        ]);

        $service = Service::with('provider')->findOrFail($validated['service_id']);
        $provider = $service->provider;

        // Calculate start and end datetime
        $startDatetime = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $endDatetime = (clone $startDatetime)->addMinutes($service->duration_minutes);

        // Check if provider is available at this time
        if (!$this->timeSlotService->isProviderAvailable($provider, $startDatetime->toISOString())) {
            return back()->withErrors([
                'start_time' => 'Provider is not available at this time.'
            ]);
        }

        // Use database transaction to prevent race conditions
        try {
            DB::beginTransaction();

            // Check for conflicting bookings
            $conflictingBooking = Booking::where('provider_id', $provider->id)
                ->where(function ($query) use ($startDatetime, $endDatetime) {
                    $query->where(function ($q) use ($startDatetime, $endDatetime) {
                        $q->whereBetween('start_datetime', [$startDatetime, $endDatetime->subSecond()])
                          ->orWhereBetween('end_datetime', [$startDatetime->addSecond(), $endDatetime]);
                    })->orWhere(function ($q) use ($startDatetime, $endDatetime) {
                        $q->where('start_datetime', '<=', $startDatetime)
                          ->where('end_datetime', '>=', $endDatetime);
                    });
                })
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->exists();

            if ($conflictingBooking) {
                DB::rollBack();
                return back()->withErrors([
                    'start_time' => 'This time slot is no longer available.'
                ]);
            }

            $booking = new Booking([
                'user_id' => auth()->id(),
                'provider_id' => $provider->id,
                'service_id' => $service->id,
                'start_datetime' => $startDatetime,
                'end_datetime' => $endDatetime,
                'status' => 'pending',
                'notes' => $validated['notes']
            ]);

            $booking->save();

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'An error occurred while creating the booking. Please try again.'
            ]);
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        return Inertia::render('Booking/Show', [
            'booking' => $booking->load(['service', 'provider.user', 'user'])
        ]);
    }

    /**
     * Cancel the specified booking.
     */
    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        $booking->status = 'cancelled';
        $booking->save();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled successfully!');
    }

    /**
     * Update booking status (for providers).
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $this->authorize('updateStatus', $booking);

        $validated = $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled'
        ]);

        $booking->status = $validated['status'];
        $booking->save();

        return back()->with('success', 'Booking status updated successfully!');
    }

    /**
     * Display provider dashboard with today's bookings.
     */
    public function providerDashboard()
    {
        $provider = auth()->user()->provider;
        
        if (!$provider) {
            return redirect()->route('home');
        }
        
        $today = Carbon::today();
        $bookings = $provider->bookings()
            ->with(['user', 'service'])
            ->whereDate('start_datetime', $today)
            ->orderBy('start_datetime')
            ->get();
        
        return Inertia::render('Provider/Dashboard', [
            'bookings' => $bookings,
            'date' => $today->format('Y-m-d')
        ]);
    }
    
    /**
     * Display provider bookings for a specific date.
     */
    public function providerBookings(Request $request)
    {
        $provider = auth()->user()->provider;
        
        if (!$provider) {
            return redirect()->route('home');
        }
        
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $bookings = $provider->bookings()
            ->with(['user', 'service'])
            ->whereDate('start_datetime', $date)
            ->orderBy('start_datetime')
            ->get();
        
        return Inertia::render('Provider/Bookings', [
            'bookings' => $bookings,
            'date' => $date
        ]);
    }
    
    /**
     * Confirm a booking.
     */
    public function confirm(Booking $booking)
    {
        $this->authorize('manage', $booking);
        
        $booking->status = 'confirmed';
        $booking->save();
        
        return redirect()->back()->with('success', 'Booking confirmed successfully!');
    }
    
    /**
     * Decline a booking.
     */
    public function decline(Booking $booking)
    {
        $this->authorize('manage', $booking);
        
        $booking->status = 'declined';
        $booking->save();
        
        return redirect()->back()->with('success', 'Booking declined successfully!');
    }
}
