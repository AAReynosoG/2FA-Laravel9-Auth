<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * InternalRedirectMiddleware class
 *
 * This middleware is used to handle redirection based on an internal session flag.
 * If the 'internal_redirect' session variable is set, it allows the request to proceed.
 * Otherwise, it redirects the user to the dashboard route.
 */
class InternalRedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This method checks for the presence of an 'internal_redirect' session variable.
     * If it exists, the request is allowed to proceed. If not, the user is redirected
     * to the dashboard route.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next The next middleware in the pipeline.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse The response or redirect after the middleware has been applied.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the 'internal_redirect' session variable is not set
        if (!session('internal_redirect')) {
            // If not set, redirect to the dashboard route
            return redirect()->route('dashboard');
        }

        // Forget the 'internal_redirect' session variable to prevent repeated redirection
        session()->forget('internal_redirect');

        // Proceed with the next middleware or request handling
        return $next($request);
    }
}

