<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the services for the authenticated provider.
     */
    public function index()
    {
        $provider = auth()->user()->provider;

        if (! $provider) {
            return redirect()->route('provider.create')
                ->with('error', 'You need to create a provider profile first.');
        }

        $services = $provider->services()->orderBy('created_at', 'desc')->get();

        return Inertia::render('Service/Index', [
            'services' => $services,
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $provider = auth()->user()->provider;

        if (! $provider) {
            return redirect()->route('provider.create')
                ->with('error', 'You need to create a provider profile first.');
        }

        return Inertia::render('Service/Create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $provider = auth()->user()->provider;

        if (! $provider) {
            return redirect()->route('provider.create')
                ->with('error', 'You need to create a provider profile first.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'display_price' => 'required|string|max:50',
        ]);

        $service = new Service($validated);
        $service->provider_id = $provider->id;
        $service->save();

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully!');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $this->authorize('update', $service);

        return Inertia::render('Service/Edit', [
            'service' => $service,
        ]);
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'display_price' => 'required|string|max:50',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully!');
    }
}
