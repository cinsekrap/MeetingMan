<?php

namespace App\Http\Middleware;

use App\Http\Controllers\InstallController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for install routes
        if ($request->is('install*')) {
            return $next($request);
        }

        // Redirect to installer if not installed
        if (!InstallController::isInstalled()) {
            return redirect()->route('install.requirements');
        }

        return $next($request);
    }
}
