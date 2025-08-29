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

    $response = $this->actingAs($user)
        ->post('/onboarding/complete');

    $response->assertRedirect('/dashboard');
    expect($user->fresh()->completed_onboarding)->toBe(true);
});

test('onboarding completion marks user as completed', function () {
    $user = User::factory()->create(['role' => 'customer']);

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
