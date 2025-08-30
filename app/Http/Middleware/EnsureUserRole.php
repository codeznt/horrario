<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The required user role (business or customer)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        $user = Auth::user();

        if (! $user->role || $user->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied. Insufficient role permissions.',
                    'required_role' => $role,
                    'user_role' => $user->role,
                ], 403);
            }

            // For Inertia requests, render an access denied page
            return \Inertia\Inertia::render('Errors/AccessDenied', [
                'message' => 'Access denied. This section is only available for '.($role === 'business' ? 'service providers' : 'customers').'.',
                'required_role' => $role === 'business' ? 'Service Provider' : 'Customer',
                'user_role' => $user->role ? ($user->role === 'business' ? 'Service Provider' : 'Customer') : 'Not set',
                'dashboard_url' => route('dashboard'),
            ])->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}
