<?php

namespace Sahlowle\Larawatch\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Sahlowle\Larawatch\Services\CacheMonitorService;
use Sahlowle\Larawatch\Services\HealthCheckService;
use Sahlowle\Larawatch\Services\StatsAggregatorService;

class DashboardController extends Controller
{
    public function __construct(
        protected StatsAggregatorService $statsService,
        protected HealthCheckService $healthService,
        protected CacheMonitorService $cacheService,
    ) {}

    /**
     * API: Return chart time-series data.
     */
    public function chartData(): JsonResponse
    {
        $minutes = (int) request()->get('minutes', 30);

        return response()->json($this->statsService->getChartData($minutes));
    }
}
