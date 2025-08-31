<?php

use App\Http\Controllers\LanguageController;
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

// Language switching route
Route::post('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

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

    // Role-specific dashboard routes
    Route::get('/business/dashboard', [App\Http\Controllers\BusinessDashboardController::class, 'index'])
        ->middleware(['auth', 'verified', 'role:business'])
        ->name('business.dashboard');
    
    Route::get('/customer/dashboard', [App\Http\Controllers\CustomerDashboardController::class, 'index'])
        ->middleware(['auth', 'verified', 'role:customer'])
        ->name('customer.dashboard');

    // Provider routes - business role required
    Route::prefix('provider')->name('provider.')->middleware('role:business')->group(function () {
        Route::get('/create', [App\Http\Controllers\ProviderController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ProviderController::class, 'store'])->name('store');
        Route::get('/profile', [App\Http\Controllers\ProviderController::class, 'show'])->name('profile');
        Route::get('/dashboard', [App\Http\Controllers\BookingController::class, 'providerDashboard'])->name('dashboard');
        Route::get('/bookings', [App\Http\Controllers\BookingController::class, 'providerBookings'])->name('bookings');
        Route::get('/edit', [App\Http\Controllers\ProviderController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\ProviderController::class, 'update'])->name('update');
        Route::delete('/', [App\Http\Controllers\ProviderController::class, 'destroy'])->name('destroy');
    });

    // Service routes - business role required (providers manage their services)
    Route::prefix('services')->name('services.')->middleware('role:business')->group(function () {
        Route::get('/', [App\Http\Controllers\ServiceController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ServiceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ServiceController::class, 'store'])->name('store');
        Route::get('/{service}/edit', [App\Http\Controllers\ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [App\Http\Controllers\ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [App\Http\Controllers\ServiceController::class, 'destroy'])->name('destroy');
    });

    // Schedule routes - business role required (providers manage their schedules)
    Route::prefix('schedules')->name('schedules.')->middleware('role:business')->group(function () {
        Route::get('/', [App\Http\Controllers\ScheduleController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\ScheduleController::class, 'store'])->name('store');
        Route::put('/{schedule}', [App\Http\Controllers\ScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [App\Http\Controllers\ScheduleController::class, 'destroy'])->name('destroy');
    });

    // Consumer booking routes - customer role required (consumers view/manage their bookings)
    Route::prefix('bookings')->name('bookings.')->middleware('role:customer')->group(function () {
        Route::get('/', [App\Http\Controllers\BookingController::class, 'index'])->name('index');
        Route::get('/upcoming', [App\Http\Controllers\BookingController::class, 'upcoming'])->name('upcoming');
        Route::get('/past', [App\Http\Controllers\BookingController::class, 'past'])->name('past');
        Route::get('/history', [App\Http\Controllers\BookingController::class, 'history'])->name('history');
        Route::get('/{booking}', [App\Http\Controllers\BookingController::class, 'show'])->name('show');
        Route::post('/{booking}/cancel', [App\Http\Controllers\BookingController::class, 'cancel'])->name('cancel');
    });

    // Customer favorites routes - customer role required
    Route::middleware('role:customer')->group(function () {
        Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites', [App\Http\Controllers\FavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('/favorites/{provider}', [App\Http\Controllers\FavoriteController::class, 'destroy'])->name('favorites.destroy');
    });

    // Provider booking management routes - business role required (providers confirm/decline bookings)
    Route::prefix('bookings')->name('bookings.')->middleware('role:business')->group(function () {
        Route::post('/{booking}/confirm', [App\Http\Controllers\BookingController::class, 'confirm'])->name('confirm');
        Route::post('/{booking}/decline', [App\Http\Controllers\BookingController::class, 'decline'])->name('decline');
        Route::post('/{booking}/status', [App\Http\Controllers\BookingController::class, 'updateStatus'])->name('updateStatus');
    });

    // Service booking routes - customer role required (consumers create bookings)
    Route::middleware('role:customer')->group(function () {
        Route::get('/services/{service}/book', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    });

    // Customer service browsing routes - customer role required  
    // Note: Using /browse/services to avoid conflict with business /services management
    Route::middleware('role:customer')->group(function () {
        Route::get('/browse/services', [App\Http\Controllers\CustomerServiceController::class, 'index'])->name('customer.services.index');
        Route::get('/browse/services/{service}', [App\Http\Controllers\CustomerServiceController::class, 'show'])->name('customer.services.show');
    });

    // Search routes - customer role required (consumers search for services)
    Route::middleware('role:customer')->group(function () {
        Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
        Route::get('/search/results', [App\Http\Controllers\SearchController::class, 'search'])->name('search.results');
        Route::get('/search/provider/{provider}', [App\Http\Controllers\SearchController::class, 'providerServices'])->name('search.provider');
    });

    // Page links for review
    Route::get('/page-links', function () {
        return Inertia::render('ExampleLinks');
    })->name('page-links');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
