<?php

namespace Sahlowle\Larawatch\Services;

use Sahlowle\Larawatch\Models\MonitorJob;
use Sahlowle\Larawatch\Models\MonitorRequest;

class StatsAggregatorService
{
    /**
     * Get the main dashboard statistics.
     */
    public function getDashboardStats(): array
    {
        return [
            'requests_per_minute' => $this->getRequestsPerMinute(),
            'avg_response_ms' => $this->getAvgResponseTime(),
            'error_rate' => $this->getErrorRate(),
            'jobs_summary' => $this->getJobsSummary(),
            'trends' => $this->getTrends(),
        ];
    }

    public function getRequestsPerMinute(): int
    {
        $count = MonitorRequest::where('created_at', '>=', now()->subMinutes(5))->count();

        return (int) round($count / 5);
    }

    public function getAvgResponseTime(): int
    {
        return (int) (MonitorRequest::where('created_at', '>=', now()->subHour())
            ->avg('duration_ms') ?? 0);
    }

    public function getErrorRate(): float
    {
        $total = MonitorRequest::where('created_at', '>=', now()->subHour())->count();

        if ($total === 0) {
            return 0;
        }

        $errors = MonitorRequest::where('created_at', '>=', now()->subHour())
            ->where('status_code', '>=', 400)
            ->count();

        return round(($errors / $total) * 100, 1);
    }

    public function getJobsSummary(): array
    {
        $counts = MonitorJob::where('created_at', '>=', now()->subDay())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'total' => array_sum($counts),
            'pending' => $counts['pending'] ?? 0,
            'running' => $counts['running'] ?? 0,
            'done' => $counts['done'] ?? 0,
            'failed' => $counts['failed'] ?? 0,
        ];
    }

    public function getTrends(): array
    {
        $currentHourRpm = $this->getRequestsPerMinute();
        $previousHourCount = MonitorRequest::whereBetween('created_at', [
            now()->subHours(2), now()->subHour(),
        ])->count();
        $previousHourRpm = (int) round($previousHourCount / 60);

        $rpmChange = $previousHourRpm > 0
            ? round((($currentHourRpm - $previousHourRpm) / $previousHourRpm) * 100, 1)
            : 0;

        $currentResp = $this->getAvgResponseTime();
        $previousResp = (int) (MonitorRequest::whereBetween('created_at', [
            now()->subHours(2), now()->subHour(),
        ])->avg('duration_ms') ?? 0);

        $respChange = $previousResp - $currentResp;

        return [
            'rpm_change_percent' => $rpmChange,
            'response_change_ms' => $respChange,
            'error_rate_15min' => $this->getErrorRate15Min(),
        ];
    }

    protected function getErrorRate15Min(): float
    {
        $total = MonitorRequest::where('created_at', '>=', now()->subMinutes(15))->count();

        if ($total === 0) {
            return 0;
        }

        $errors = MonitorRequest::where('created_at', '>=', now()->subMinutes(15))
            ->where('status_code', '>=', 400)
            ->count();

        return round(($errors / $total) * 100, 1);
    }

    /**
     * Get time-series data for the traffic chart.
     */
    public function getChartData(int $minutes = 30): array
    {
        $data = [];

        for ($i = $minutes; $i >= 0; $i--) {
            $start = now()->subMinutes($i + 1);
            $end = now()->subMinutes($i);

            $requests = MonitorRequest::whereBetween('created_at', [$start, $end])->count();
            $errors = MonitorRequest::whereBetween('created_at', [$start, $end])
                ->where('status_code', '>=', 400)
                ->count();

            $data[] = [
                'label' => $i === 0 ? 'now' : '-'.$i.'m',
                'requests' => $requests,
                'errors' => $errors,
            ];
        }

        return $data;
    }
}
