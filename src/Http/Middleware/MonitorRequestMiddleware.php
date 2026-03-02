<?php

namespace Sahlowle\Larawatch\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sahlowle\Larawatch\Services\RequestMonitorService;
use Symfony\Component\HttpFoundation\Response;

class MonitorRequestMiddleware
{
    public function __construct(
        protected RequestMonitorService $monitorService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('monitor_start_time', microtime(true));

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent.
     */
    public function terminate(Request $request, Response $response): void
    {
        $startTime = $request->attributes->get('monitor_start_time');

        if ($startTime) {
            try {
                $this->monitorService->record($request, $response, $startTime);
            } catch (\Throwable $e) {
                // Silently fail — monitoring must never break the app
                report($e);
            }
        }
    }
}
