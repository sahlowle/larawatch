<?php

namespace Sahlowle\Larawatch\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class MonitorAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (! Gate::allows('viewMonitor')) {
        //     abort(403, 'Unauthorized access to Monitor.');
        // }

        return $next($request);
    }
}
