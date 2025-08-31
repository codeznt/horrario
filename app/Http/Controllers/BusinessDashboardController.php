<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Booking;

class BusinessDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get user's provider profile
        $provider = Provider::where('user_id', $user->id)->first();
        
        // Get services for this provider
        $services = [];
        if ($provider) {
            $services = Service::where('provider_id', $provider->id)
                ->select(['id', 'title', 'display_price'])
                ->get()
                ->toArray();
        }
        
        // Get recent bookings
        $recentBookings = [];
        $todaysBookings = 0;
        $weeklyBookings = 0;
        $monthlyRevenue = '$0.00';
        
        if ($provider) {
            $recentBookings = Booking::whereHas('service', function ($query) use ($provider) {
                $query->where('provider_id', $provider->id);
            })
            ->with(['service:id,title', 'user:id,name'])
            ->orderBy('start_datetime', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
            
            // Get today's bookings count
            $todaysBookings = Booking::whereHas('service', function ($query) use ($provider) {
                $query->where('provider_id', $provider->id);
            })
            ->whereDate('start_datetime', today())
            ->count();
            
            // Get this week's bookings count
            $weeklyBookings = Booking::whereHas('service', function ($query) use ($provider) {
                $query->where('provider_id', $provider->id);
            })
            ->whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
            
            // Calculate monthly revenue (simplified - using display_price since there's no numeric price field)
            // In a real application, you'd have a proper price field or extract numeric value from display_price
            $monthlyBookings = Booking::whereHas('service', function ($query) use ($provider) {
                $query->where('provider_id', $provider->id);
            })
            ->where('status', 'completed')
            ->whereMonth('start_datetime', now()->month)
            ->count();
            
            // For demo purposes, assume average revenue of $50 per booking
            $estimatedRevenue = $monthlyBookings * 50;
            $monthlyRevenue = '$' . number_format($estimatedRevenue, 2);
        }
        
        return Inertia::render('BusinessDashboard', [
            'provider' => $provider,
            'services' => $services,
            'recentBookings' => $recentBookings,
            'todaysBookings' => $todaysBookings,
            'weeklyBookings' => $weeklyBookings,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }
}