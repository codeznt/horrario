<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Onboarding role selection
Route::post('/onboarding/role', function () {
    $validated = request()->validate([
        'role' => 'required|in:business,customer',
    ]);

    // Update user role immediately for dynamic flow
    if (auth()->check()) {
        auth()->user()->update([
            'role' => $validated['role'],
        ]);
    }

    // Return proper Inertia response - redirect back to maintain state
    return back()->with('flash', [
        'success' => true,
        'message' => 'Role saved successfully',
    ]);
})->middleware(['telegram.auth'])->name('onboarding.role');

// Onboarding completion
Route::post('/onboarding/complete', function () {
    // Role already saved, just complete the onboarding flow

    // Mark onboarding as completed using meta
    if (auth()->check()) {
        auth()->user()->markOnboardingCompleted();
    }

    // Redirect to dashboard after onboarding
    return redirect()->route('dashboard');
})->middleware(['telegram.auth'])->name('onboarding.complete');

// Telegram protected routes
Route::middleware(['telegram.auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Welcome');
    })->name('home');

    Route::get('/start', function () {
        return Inertia::render('Start');
    })->name('start');

    Route::get('/tg', function () {
        return Inertia::render('Telegram/Home', [
            'message' => 'Welcome to Telegram Mini App!',
            'user' => auth()->user(),
            'telegramData' => request('telegram_data'),
        ]);
    })->name('telegram.home');

    // Simple test route for tests
    Route::get('/tg-test', function () {
        return response()->json([
            'success' => true,
            'user' => auth()->user(),
            'telegram_data' => request('telegram_data'),
        ]);
    })->name('telegram.test');

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
