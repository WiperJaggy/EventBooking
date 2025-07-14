<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException; // Make sure this is imported

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, we don't want to redirect to a named route.
        // Returning null here tells Laravel's exception handler to instead
        // return a 401 Unauthorized JSON response.
        if ($request->expectsJson()) {
            return null;
        }

        // This part is for traditional web applications that would
        // redirect to a 'login' named route if not authenticated.
        // Since you are building an API, this part likely won't be hit
        // if your requests correctly send an 'Accept: application/json' header.
        // If you were to build a mixed web/API app and had a named 'login'
        // route for web views, you would use: return route('login');
        return null; // Explicitly returning null for all cases for API-only focus.
    }

    /**
     * Handle an unauthenticated user.
     * Overriding this allows more control over the 401 response.
     */
    protected function unauthenticated($request, array $guards)
    {
        // Throw an AuthenticationException. Laravel's default handler
        // will convert this into a 401 Unauthorized JSON response for API requests.
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $request->expectsJson() ? null : route('login')
        );
    }
}