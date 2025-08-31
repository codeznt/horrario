<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can select business role during onboarding', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/onboarding/role', [
            'role' => 'business',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('flash.success', true);

    expect($user->fresh()->role)->toBe('business');
});

test('user can select customer role during onboarding', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/onboarding/role', [
            'role' => 'customer',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('flash.success', true);

    expect($user->fresh()->role)->toBe('customer');
});

test('role selection validates required role field', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)
        ->post('/onboarding/role', []);

    $response->assertSessionHasErrors('role');
    expect($user->fresh()->role)->toBe('user'); // Role should remain unchanged
});

test('role selection validates role must be business or customer', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)
        ->post('/onboarding/role', [
            'role' => 'invalid_role',
        ]);

    $response->assertSessionHasErrors('role');
    expect($user->fresh()->role)->toBe('user'); // Role should remain unchanged
});

test('unauthenticated user cannot access onboarding role endpoint', function () {
    $response = $this->post('/onboarding/role', [
        'role' => 'business',
    ]);

    $response->assertStatus(302); // Redirect due to middleware
});

test('user can complete onboarding after selecting role', function () {
    $user = User::factory()->create(['role' => 'business']);

    // Add required business information
    $user->setMeta('business_name', 'Test Business');
    $user->setMeta('business_phone', '+380123456789');
    $user->setMeta('business_address', 'Test Address 123');

    $response = $this->actingAs($user)
        ->post('/onboarding/complete');

    $response->assertRedirect('/dashboard');
    expect($user->fresh()->completed_onboarding)->toBe(true);
});

test('onboarding completion marks user as completed', function () {
    $user = User::factory()->create(['role' => 'customer']);

    // Add required customer information
    $user->setMeta('customer_name', 'Test Customer');
    $user->setMeta('customer_phone', '+380123456789');

    $this->actingAs($user)
        ->post('/onboarding/complete');

    expect($user->fresh())
        ->role->toBe('customer')
        ->completed_onboarding->toBe(true);
});

test('unauthenticated user cannot access onboarding complete endpoint', function () {
    $response = $this->post('/onboarding/complete');

    $response->assertStatus(302); // Redirect due to middleware
});

test('user can submit customer information', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $response = $this->actingAs($user)
        ->post('/onboarding/customer-info', [
            'name' => 'John Doe',
            'phone' => '+380123456789',
        ]);

    $response->assertStatus(302);
    expect($user->fresh()->getMeta('customer_name'))->toBe('John Doe');
    expect($user->fresh()->getMeta('customer_phone'))->toBe('+380123456789');
});

test('customer information validation', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $response = $this->actingAs($user)
        ->post('/onboarding/customer-info', [
            'name' => '',
            'phone' => '',
        ]);

    $response->assertStatus(302)
        ->assertSessionHasErrors(['name', 'phone']);
});

test('user can submit business information', function () {
    $user = User::factory()->create(['role' => 'business']);

    $response = $this->actingAs($user)
        ->post('/onboarding/business-info', [
            'business_name' => 'Test Business Ltd',
            'business_phone' => '+380987654321',
        ]);

    $response->assertStatus(302);
    expect($user->fresh()->getMeta('business_name'))->toBe('Test Business Ltd');
    expect($user->fresh()->getMeta('business_phone'))->toBe('+380987654321');
});

test('business information validation', function () {
    $user = User::factory()->create(['role' => 'business']);

    $response = $this->actingAs($user)
        ->post('/onboarding/business-info', [
            'business_name' => '',
            'business_phone' => '',
        ]);

    $response->assertStatus(302)
        ->assertSessionHasErrors(['business_name', 'business_phone']);
});

test('user can submit business address', function () {
    $user = User::factory()->create(['role' => 'business']);

    $response = $this->actingAs($user)
        ->post('/onboarding/business-address', [
            'address' => '123 Business Street, Kyiv, Ukraine',
        ]);

    $response->assertStatus(302);
    expect($user->fresh()->getMeta('business_address'))->toBe('123 Business Street, Kyiv, Ukraine');
});

test('business address validation', function () {
    $user = User::factory()->create(['role' => 'business']);

    $response = $this->actingAs($user)
        ->post('/onboarding/business-address', [
            'address' => '',
        ]);

    $response->assertStatus(302)
        ->assertSessionHasErrors(['address']);
});

test('onboarding completion fails without required information', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $response = $this->actingAs($user)
        ->post('/onboarding/complete');

    $response->assertStatus(302)
        ->assertSessionHasErrors(['error']);

    expect($user->fresh()->completed_onboarding)->toBe(false);
});

test('hasCompletedRoleInfo returns true for customer with complete info', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $user->setMeta('customer_name', 'Test User');
    $user->setMeta('customer_phone', '+380123456789');

    expect($user->hasCompletedRoleInfo())->toBe(true);
});

test('hasCompletedRoleInfo returns false for customer with incomplete info', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $user->setMeta('customer_name', 'Test User');
    // Missing phone

    expect($user->hasCompletedRoleInfo())->toBe(false);
});

test('hasCompletedRoleInfo returns true for business with complete info', function () {
    $user = User::factory()->create(['role' => 'business']);
    $user->setMeta('business_name', 'Test Business');
    $user->setMeta('business_phone', '+380123456789');
    $user->setMeta('business_address', 'Test Address');

    expect($user->hasCompletedRoleInfo())->toBe(true);
});

test('hasCompletedRoleInfo returns false for business with incomplete info', function () {
    $user = User::factory()->create(['role' => 'business']);
    $user->setMeta('business_name', 'Test Business');
    $user->setMeta('business_phone', '+380123456789');
    // Missing address

    expect($user->hasCompletedRoleInfo())->toBe(false);
});
