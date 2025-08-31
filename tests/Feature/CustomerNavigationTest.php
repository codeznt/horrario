<?php

use App\Models\User;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Favorite;

it('customer can access services browsing page', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    
    $response = $this->actingAs($customer)->get('/browse/services');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Customer/Services/Index'));
});

it('customer services page displays available services correctly', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $businessUser = User::factory()->create(['role' => 'business']);
    $provider = Provider::factory()->create(['user_id' => $businessUser->id]);
    $service = Service::factory()->create(['provider_id' => $provider->id]);
    
    $response = $this->actingAs($customer)->get('/browse/services');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Customer/Services/Index')
            ->has('services.data', 1)
            ->where('services.data.0.title', $service->title)
        );
});

it('customer can access service detail page', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $businessUser = User::factory()->create(['role' => 'business']);
    $provider = Provider::factory()->create(['user_id' => $businessUser->id]);
    $service = Service::factory()->create(['provider_id' => $provider->id]);
    
    $response = $this->actingAs($customer)->get("/browse/services/{$service->id}");
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Customer/Services/Show')
            ->where('service.title', $service->title)
        );
});

it('customer can access favorites page', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    
    $response = $this->actingAs($customer)->get('/favorites');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Customer/Favorites/Index'));
});

it('customer can access booking history page', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    
    $response = $this->actingAs($customer)->get('/bookings/history');
    
    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Customer/Bookings/History'));
});

it('customer dashboard navigation links are accessible', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    
    // Test all main navigation routes from customer dashboard
    $routes = [
        '/browse/services' => 'Customer/Services/Index',
        '/bookings' => 'Booking/Index', 
        '/favorites' => 'Customer/Favorites/Index',
        '/bookings/history' => 'Customer/Bookings/History',
    ];
    
    foreach ($routes as $route => $expectedComponent) {
        $response = $this->actingAs($customer)->get($route);
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page->component($expectedComponent));
    }
});

it('business users cannot access customer-specific routes', function () {
    $businessUser = User::factory()->create(['role' => 'business']);
    
    $customerRoutes = [
        '/browse/services',
        '/favorites',
        '/bookings/history',
    ];
    
    foreach ($customerRoutes as $route) {
        $response = $this->actingAs($businessUser)->get($route);
        $response->assertInertia(fn ($page) => $page->component('Errors/AccessDenied'));
    }
});
