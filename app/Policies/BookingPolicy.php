<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view any bookings.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own bookings
    }

    /**
     * Determine whether the user can view the booking.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id ||
               ($user->provider && $user->provider->id === $booking->provider_id);
    }

    /**
     * Determine whether the user can create bookings.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create bookings
    }

    /**
     * Determine whether the user can cancel the booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id &&
               in_array($booking->status, ['pending', 'confirmed']);
    }

    /**
     * Determine whether the user can update booking status (for providers).
     */
    public function updateStatus(User $user, Booking $booking): bool
    {
        return $user->provider &&
               $user->provider->id === $booking->provider_id &&
               $booking->status !== 'cancelled';
    }

    /**
     * Determine whether the user can delete the booking.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->provider &&
               $user->provider->id === $booking->provider_id;
    }

    /**
     * Determine whether the user can manage the booking (confirm/decline).
     */
    public function manage(User $user, Booking $booking): bool
    {
        return $user->provider && $user->provider->id === $booking->provider_id;
    }
}
