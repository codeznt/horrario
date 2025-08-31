<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Provider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorite providers.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        $favorites = Favorite::where('user_id', $user->id)
            ->with(['provider'])
            ->latest()
            ->paginate(10)
            ->through(function ($favorite) {
                return [
                    'id' => $favorite->id,
                    'provider' => [
                        'id' => $favorite->provider->id,
                        'business_name' => $favorite->provider->business_name,
                        'description' => $favorite->provider->description,
                        'address' => $favorite->provider->address,
                        'contact_phone' => $favorite->provider->contact_phone,
                    ],
                    'favorited_at' => $favorite->created_at->format('M j, Y'),
                ];
            });

        return Inertia::render('Customer/Favorites/Index', [
            'favorites' => $favorites,
        ]);
    }

    /**
     * Add a provider to favorites.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
        ]);

        $user = $request->user();
        
        // Check if already favorited
        $existing = Favorite::where('user_id', $user->id)
            ->where('provider_id', $validated['provider_id'])
            ->first();

        if ($existing) {
            return back()->with('flash', [
                'type' => 'info',
                'message' => 'Provider is already in your favorites',
            ]);
        }

        Favorite::create([
            'user_id' => $user->id,
            'provider_id' => $validated['provider_id'],
        ]);

        return back()->with('flash', [
            'type' => 'success',
            'message' => 'Provider added to favorites',
        ]);
    }

    /**
     * Remove a provider from favorites.
     */
    public function destroy(Request $request, Provider $provider)
    {
        $user = $request->user();
        
        $favorite = Favorite::where('user_id', $user->id)
            ->where('provider_id', $provider->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            
            return back()->with('flash', [
                'type' => 'success',
                'message' => 'Provider removed from favorites',
            ]);
        }

        return back()->with('flash', [
            'type' => 'error',
            'message' => 'Provider not found in favorites',
        ]);
    }
}