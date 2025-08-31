<?php

use App\Models\User;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Booking;

it('redirects business users to business dashboard', function () {
    $user = User::factory()->create(['role' => 'business']);
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertRedirect('/business/dashboard');
});

it('redirects customer users to customer dashboard', function () {
    $user = User::factory()->create(['role' => 'customer']);
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertRedirect('/customer/dashboard');
});


it('business dashboard displays correctly for business users', function () {
    $user = User::factory()->create(['role' => 'business']);
    $provider = Provider::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->get('/business/dashboard');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('BusinessDashboard')
            ->has('provider')
            ->has('services')
            ->has('recentBookings')
            ->has('todaysBookings')
            ->has('weeklyBookings')
            ->has('monthlyRevenue')
        );
});

it('customer dashboard displays correctly for customer users', function () {
    $user = User::factory()->create(['role' => 'customer']);
    
    $response = $this->actingAs($user)->get('/customer/dashboard');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('CustomerDashboard')
            ->has('upcomingBookings')
            ->has('recentServices')
            ->has('bookingsCount')
            ->has('favoriteProvidersCount')
        );
});

it('business dashboard shows correct data for provider with services and bookings', function () {
    $user = User::factory()->create(['role' => 'business']);
    $provider = Provider::factory()->create(['user_id' => $user->id]);
    $service = Service::factory()->create(['provider_id' => $provider->id]);
    $customer = User::factory()->create(['role' => 'customer']);
    $booking = Booking::factory()->create([
        'user_id' => $customer->id,
        'provider_id' => $provider->id,
        'service_id' => $service->id,
        'start_datetime' => now()->addHour(),
        'status' => 'confirmed',
    ]);
    
    $response = $this->actingAs($user)->get('/business/dashboard');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('provider.id', $provider->id)
            ->has('services', 1)
            ->has('recentBookings', 1)
        );
});

it('customer dashboard shows correct data for user with bookings', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $businessUser = User::factory()->create(['role' => 'business']);
    $provider = Provider::factory()->create(['user_id' => $businessUser->id]);
    $service = Service::factory()->create(['provider_id' => $provider->id]);
    $booking = Booking::factory()->create([
        'user_id' => $customer->id,
        'provider_id' => $provider->id,
        'service_id' => $service->id,
        'start_datetime' => now()->addHour(),
        'status' => 'confirmed',
    ]);
    
    $response = $this->actingAs($customer)->get('/customer/dashboard');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('bookingsCount', 1)
            ->has('upcomingBookings', 1)
        );
});

it('prevents customers from accessing business dashboard', function () {
    $user = User::factory()->create(['role' => 'customer']);
    
    $response = $this->actingAs($user)->get('/business/dashboard');
    
    $response->assertInertia(fn ($page) => $page->component('Errors/AccessDenied'));
});

it('prevents business users from accessing customer dashboard', function () {
    $user = User::factory()->create(['role' => 'business']);
    
    $response = $this->actingAs($user)->get('/customer/dashboard');
    
    $response->assertInertia(fn ($page) => $page->component('Errors/AccessDenied'));
});

it('business dashboard handles provider without services correctly', function () {
    $user = User::factory()->create(['role' => 'business']);
    $provider = Provider::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->get('/business/dashboard');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('provider.id', $provider->id)
            ->has('services', 0)
            ->has('recentBookings', 0)
            ->where('todaysBookings', 0)
            ->where('weeklyBookings', 0)
            ->where('monthlyRevenue', '$0.00')
        );
});

it('business dashboard handles user without provider profile', function () {
    $user = User::factory()->create(['role' => 'business']);
    
    $response = $this->actingAs($user)->get('/business/dashboard');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('provider', null)
            ->has('services', 0)
            ->has('recentBookings', 0)
            ->where('todaysBookings', 0)
            ->where('weeklyBookings', 0)
            ->where('monthlyRevenue', '$0.00')
        );
});

it('customer dashboard handles user with no bookings correctly', function () {
    $user = User::factory()->create(['role' => 'customer']);
    
    $response = $this->actingAs($user)->get('/customer/dashboard');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->where('bookingsCount', 0)
            ->has('upcomingBookings', 0)
            ->where('favoriteProvidersCount', 0)
        );
});