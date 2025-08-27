<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Onboarding completion
Route::post('/onboarding/complete', function () {
    $validated = request()->validate([
        'role' => 'required|in:business,customer'
    ]);
    
    // Update user role (assuming the user is authenticated via Telegram)
    if (auth()->check()) {
        auth()->user()->update([
            'role' => $validated['role']
        ]);
    }
    
    // Redirect to dashboard after onboarding
    return redirect()->route('dashboard');
})->middleware(['telegram.auth'])->name('onboarding.complete');

// Telegram protected routes
Route::middleware(['telegram.auth'])->group(function () {
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
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
