<?php

namespace Sahlowle\Larawatch\Livewire;

use Livewire\Component;
use Sahlowle\Larawatch\Models\MonitorException;
use Sahlowle\Larawatch\Models\MonitorJob;
use Sahlowle\Larawatch\Models\MonitorRequest;
use Sahlowle\Larawatch\Services\CacheMonitorService;
use Sahlowle\Larawatch\Services\HealthCheckService;
use Sahlowle\Larawatch\Services\StatsAggregatorService;

class Dashboard extends Component
{
    public int $chartMinutes = 30;

    public bool $liveMode = false;

    public function getStatsProperty(): array
    {
        return app(StatsAggregatorService::class)->getDashboardStats();
    }

    public function getHealthChecksProperty(): array
    {
        $checks = app(HealthCheckService::class)->getLatestResults();

        return empty($checks) ? app(HealthCheckService::class)->runChecks() : $checks;
    }

    public function getCacheStatsProperty(): array
    {
        return app(CacheMonitorService::class)->getLatestStats();
    }

    public function getChartDataProperty(): array
    {
        return app(StatsAggregatorService::class)->getChartData($this->chartMinutes);
    }

    public function getRecentRequestsProperty()
    {
        return MonitorRequest::orderByDesc('created_at')->limit(10)->get();
    }

    public function getRecentExceptionsProperty()
    {
        return MonitorException::orderByDesc('last_occurred_at')->limit(5)->get();
    }

    public function getRecentJobsProperty()
    {
        return MonitorJob::orderByDesc('created_at')->limit(6)->get();
    }

    public function getExceptionsTodayProperty(): int
    {
        return (int) MonitorException::whereDate('last_occurred_at', today())->sum('count');
    }

    public function setChartRange(int $minutes): void
    {
        $this->chartMinutes = $minutes;
    }

    public function toggleLiveMode(): void
    {
        $this->liveMode = ! $this->liveMode;
    }

    public function render()
    {
        return view('larawatch::livewire.monitor.dashboard')
            ->layout('larawatch::monitor.layouts.app', ['title' => 'Dashboard', 'hasLiveMode' => true]);
    }
}
