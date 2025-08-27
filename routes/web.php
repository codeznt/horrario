<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
