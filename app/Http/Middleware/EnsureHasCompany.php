<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->needsCompanySetup()) {
            // Allow access to company setup routes
            if ($request->routeIs('company.setup*') || $request->routeIs('invite.accept*')) {
                return $next($request);
            }

            return redirect()->route('company.setup');
        }

        return $next($request);
    }
}
