<?php

use App\Models\Provider;
use App\Models\User;

beforeEach(function () {
    $this->businessUser = User::factory()->create(['role' => 'business']);
    // Create a provider profile for the business user to access services/schedules
    Provider::factory()->create(['user_id' => $this->businessUser->id]);

    $this->customerUser = User::factory()->create(['role' => 'customer']);
    $this->unauthorizedUser = User::factory()->create(['role' => 'user']); // Different role
});

describe('Role-based access control', function () {
    it('allows business users to access provider routes', function () {
        $this->actingAs($this->businessUser);

        // Test /provider/profile route instead since /provider/create redirects when provider exists
        $response = $this->get('/provider/profile');

        $response->assertSuccessful();
    });

    it('denies customer users access to provider routes', function () {
        $this->actingAs($this->customerUser);

        $response = $this->get('/provider/profile');

        $response->assertForbidden();
    });

    it('denies users with different roles access to provider routes', function () {
        $this->actingAs($this->unauthorizedUser);

        $response = $this->get('/provider/profile');

        $response->assertForbidden();
    });

    it('allows customer users to access search routes', function () {
        $this->actingAs($this->customerUser);

        $response = $this->get('/search');

        $response->assertSuccessful();
    });

    it('denies business users access to search routes', function () {
        $this->actingAs($this->businessUser);

        $response = $this->get('/search');

        $response->assertForbidden();
    });

    it('allows customer users to access booking routes', function () {
        $this->actingAs($this->customerUser);

        $response = $this->get('/bookings');

        $response->assertSuccessful();
    });

    it('denies business users access to customer booking routes', function () {
        $this->actingAs($this->businessUser);

        $response = $this->get('/bookings');

        $response->assertForbidden();
    });

    it('allows business users to access service management routes', function () {
        $this->actingAs($this->businessUser);

        $response = $this->get('/services');

        $response->assertSuccessful();
    });

    it('denies customer users access to service management routes', function () {
        $this->actingAs($this->customerUser);

        $response = $this->get('/services');

        $response->assertForbidden();
    });

    it('allows business users to access schedule management routes', function () {
        $this->actingAs($this->businessUser);

        $response = $this->get('/schedules');

        $response->assertSuccessful();
    });

    it('denies customer users access to schedule management routes', function () {
        $this->actingAs($this->customerUser);

        $response = $this->get('/schedules');

        $response->assertForbidden();
    });

    it('allows any authenticated user to access shared routes like dashboard', function () {
        $this->actingAs($this->businessUser);
        $response = $this->get('/dashboard');
        $response->assertSuccessful();

        $this->actingAs($this->customerUser);
        $response = $this->get('/dashboard');
        $response->assertSuccessful();

        $this->actingAs($this->unauthorizedUser);
        $response = $this->get('/dashboard');
        $response->assertSuccessful();
    });

    it('allows any authenticated user to access page-links debug route', function () {
        $this->actingAs($this->businessUser);
        $response = $this->get('/page-links');
        $response->assertSuccessful();

        $this->actingAs($this->customerUser);
        $response = $this->get('/page-links');
        $response->assertSuccessful();

        $this->actingAs($this->unauthorizedUser);
        $response = $this->get('/page-links');
        $response->assertSuccessful();
    });

    it('returns proper JSON response for API requests when access is denied', function () {
        $this->actingAs($this->customerUser);

        $response = $this->getJson('/provider/profile');

        $response->assertForbidden()
            ->assertJson([
                'message' => 'Access denied. Insufficient role permissions.',
                'required_role' => 'business',
                'user_role' => 'customer',
            ]);
    });

    it('renders access denied page for web requests when access is denied', function () {
        $this->actingAs($this->customerUser);

        $response = $this->get('/provider/profile');

        $response->assertForbidden();
        // The response should be an Inertia page response with AccessDenied component
        $this->assertStringContainsString('Access denied', $response->getContent());
    });
});
