<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Provider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerServiceController extends Controller
{
    /**
     * Display a listing of all services for customers to browse.
     */
    public function index(Request $request): Response
    {
        $query = Service::with(['provider'])
            ->select(['id', 'title', 'description', 'duration_minutes', 'display_price', 'provider_id']);

        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('provider', function ($providerQuery) use ($search) {
                      $providerQuery->where('business_name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply category/filter if provided (can be expanded later)
        if ($request->has('category') && !empty($request->category)) {
            // TODO: Implement category filtering when categories are added to services
        }

        $services = $query->paginate(12)->through(function ($service) {
            return [
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'duration_minutes' => $service->duration_minutes,
                'display_price' => $service->display_price,
                'provider' => [
                    'id' => $service->provider->id,
                    'business_name' => $service->provider->business_name,
                ],
            ];
        });

        return Inertia::render('Customer/Services/Index', [
            'services' => $services,
            'filters' => [
                'search' => $request->search ?? '',
                'category' => $request->category ?? '',
            ],
        ]);
    }

    /**
     * Display the specified service for customers to view and potentially book.
     */
    public function show(Service $service): Response
    {
        $service->load(['provider']);

        return Inertia::render('Customer/Services/Show', [
            'service' => [
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'duration_minutes' => $service->duration_minutes,
                'display_price' => $service->display_price,
                'provider' => [
                    'id' => $service->provider->id,
                    'business_name' => $service->provider->business_name,
                    'description' => $service->provider->description,
                    'contact_phone' => $service->provider->contact_phone,
                    'address' => $service->provider->address,
                ],
            ],
        ]);
    }
}