<?php

namespace Sahlowle\Larawatch\Services;

use Illuminate\Http\Request;
use Sahlowle\Larawatch\Models\MonitorRequest;
use Symfony\Component\HttpFoundation\Response;

class RequestMonitorService
{
    /**
     * Record a request/response pair.
     */
    public function record(Request $request, Response $response, float $startTime): void
    {
        if (! config('larawatch.features.requests', true)) {
            return;
        }

        if ($this->shouldIgnore($request)) {
            return;
        }

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        MonitorRequest::create([
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'controller_action' => $this->resolveControllerAction($request),
            'duration_ms' => $durationMs,
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
            'payload_size' => strlen($request->getContent()),
            'response_size' => strlen($response->getContent()),
        ]);
    }

    protected function shouldIgnore(Request $request): bool
    {
        $ignorePaths = config('larawatch.request.ignore_paths', []);

        foreach ($ignorePaths as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function resolveControllerAction(Request $request): ?string
    {
        $route = $request->route();

        if (! $route) {
            return null;
        }

        $action = $route->getActionName();

        if ($action === 'Closure') {
            return 'Closure';
        }

        return str_replace('App\\Http\\Controllers\\', '', $action);
    }
}
