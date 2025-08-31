<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;

class CustomerDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get upcoming bookings for this user
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('start_datetime', '>', now())
            ->with(['service.provider'])
            ->orderBy('start_datetime', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'start_datetime' => $booking->start_datetime,
                    'service' => [
                        'title' => $booking->service->title,
                    ],
                    'provider' => [
                        'business_name' => $booking->service->provider->business_name,
                    ],
                ];
            })
            ->toArray();
        
        // Get recently viewed services (simplified - in real app this might be tracked separately)
        $recentServices = Service::with(['provider:id,business_name'])
            ->select(['id', 'title', 'display_price', 'provider_id'])
            ->limit(4)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'display_price' => $service->display_price,
                    'provider' => [
                        'id' => $service->provider->id,
                        'business_name' => $service->provider->business_name,
                    ],
                ];
            })
            ->toArray();
        
        // Get total bookings count for this user
        $bookingsCount = Booking::where('user_id', $user->id)->count();
        
        // Get favorite providers count
        $favoriteProvidersCount = \App\Models\Favorite::where('user_id', $user->id)->count();
        
        return Inertia::render('CustomerDashboard', [
            'upcomingBookings' => $upcomingBookings,
            'recentServices' => $recentServices,
            'bookingsCount' => $bookingsCount,
            'favoriteProvidersCount' => $favoriteProvidersCount,
        ]);
    }
}