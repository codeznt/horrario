<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProviderController extends Controller
{
    public function create()
    {
        // Check if user already has a provider profile
        if (auth()->user()->provider) {
            return redirect()->route('provider.edit');
        }

        return Inertia::render('Provider/Create');
    }

    public function store(Request $request)
    {
        // Check if user already has a provider profile
        if (auth()->user()->provider) {
            return redirect()->route('provider.edit')
                ->with('error', 'You already have a provider profile.');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|max:2048'
        ]);

        $provider = new Provider($validated);
        $provider->user_id = auth()->id();

        if ($request->hasFile('profile_image')) {
            $provider->profile_image = $request->file('profile_image')
                ->store('profile-images', 'public');
        }

        $provider->save();

        return redirect()->route('provider.dashboard')
            ->with('success', 'Provider profile created successfully!');
    }

    public function show()
    {
        $provider = auth()->user()->provider;

        if (!$provider) {
            return redirect()->route('provider.create');
        }

        return Inertia::render('Provider/Dashboard', [
            'provider' => $provider
        ]);
    }

    public function edit()
    {
        $provider = auth()->user()->provider;

        if (!$provider) {
            return redirect()->route('provider.create');
        }

        return Inertia::render('Provider/Edit', [
            'provider' => $provider
        ]);
    }

    public function update(Request $request)
    {
        $provider = auth()->user()->provider;

        if (!$provider) {
            return redirect()->route('provider.create');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($provider->profile_image) {
                Storage::disk('public')->delete($provider->profile_image);
            }

            $validated['profile_image'] = $request->file('profile_image')
                ->store('profile-images', 'public');
        }

        $provider->update($validated);

        return redirect()->route('provider.dashboard')
            ->with('success', 'Provider profile updated successfully!');
    }

    public function destroy()
    {
        $provider = auth()->user()->provider;

        if (!$provider) {
            return redirect()->route('dashboard');
        }

        // Delete profile image if exists
        if ($provider->profile_image) {
            Storage::disk('public')->delete($provider->profile_image);
        }

        $provider->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Provider profile deleted successfully!');
    }
}
