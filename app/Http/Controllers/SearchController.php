<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    /**
     * Display the search form.
     */
    public function index()
    {
        // Fetch popular services (newest services for now)
        $popularServices = Service::with('provider.user')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return Inertia::render('Search/Index', [
            'popularServices' => $popularServices,
        ]);
    }

    /**
     * Search for services and providers.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:50',
            'sort' => 'nullable|string|in:relevance,price_low,price_high,rating',
        ]);

        $query = $validated['query'] ?? '';
        $category = $validated['category'] ?? '';
        $sort = $validated['sort'] ?? 'relevance';

        // Base query for services with provider relationship
        $servicesQuery = Service::with('provider.user');

        // Apply search filters
        if (! empty($query)) {
            $servicesQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('provider', function ($pq) use ($query) {
                        $pq->where('business_name', 'like', "%{$query}%")
                            ->orWhere('description', 'like', "%{$query}%");
                    });
            });
        }

        // Apply category filter if provided
        if (! empty($category)) {
            $servicesQuery->where('category', $category);
        }

        // Apply sorting
        switch ($sort) {
            case 'price_low':
                // This is a simplified sort as display_price is a string
                // In a real app, you'd have a numeric price field
                $servicesQuery->orderBy('display_price', 'asc');
                break;
            case 'price_high':
                $servicesQuery->orderBy('display_price', 'desc');
                break;
            case 'rating':
                // If you had a rating system, you'd sort by it here
                $servicesQuery->orderBy('created_at', 'desc'); // Fallback to newest
                break;
            default:
                // Default relevance sort (or newest if no query)
                $servicesQuery->orderBy('created_at', 'desc');
                break;
        }

        $services = $servicesQuery->paginate(10)->withQueryString();

        // Get providers that match the search query
        $providers = collect();
        if (! empty($query)) {
            $providers = Provider::where('business_name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with('user')
                ->take(5)
                ->get();
        }

        return Inertia::render('Search/Results', [
            'services' => $services,
            'providers' => $providers,
            'filters' => [
                'query' => $query,
                'category' => $category,
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Display services for a specific provider.
     */
    public function providerServices(Provider $provider)
    {
        $services = $provider->services()->paginate(10);

        return Inertia::render('Search/ProviderServices', [
            'provider' => $provider->load('user'),
            'services' => $services,
        ]);
    }
}
