<?php

namespace Sahlowle\Larawatch\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Sahlowle\Larawatch\Models\MonitorHealthCheck;

class HealthCheckService
{
    /**
     * Run all configured health checks and return results.
     *
     * @return array<int, array{name: string, status: string, detail: string, response_time_ms: int}>
     */
    public function runChecks(): array
    {
        $checks = config('larawatch.health.checks', []);
        $results = [];

        foreach ($checks as $check) {
            $result = match ($check) {
                'database' => $this->checkDatabase(),
                'redis' => $this->checkRedis(),
                'queue' => $this->checkQueue(),
                'storage' => $this->checkStorage(),
                'mail' => $this->checkMail(),
                'scheduler' => $this->checkScheduler(),
                default => ['name' => $check, 'status' => 'warn', 'detail' => 'Unknown check', 'response_time_ms' => 0],
            };

            $results[] = $result;

            MonitorHealthCheck::create([
                'name' => $result['name'],
                'status' => $result['status'],
                'detail' => $result['detail'],
                'response_time_ms' => $result['response_time_ms'],
                'checked_at' => now(),
            ]);
        }

        return $results;
    }

    /**
     * Get the latest check results without running new ones.
     */
    public function getLatestResults(): array
    {
        $checks = config('larawatch.health.checks', []);
        $results = [];

        foreach ($checks as $check) {
            $latest = MonitorHealthCheck::where('name', $check)
                ->orderByDesc('checked_at')
                ->first();

            if ($latest) {
                $results[] = [
                    'name' => $latest->name,
                    'status' => $latest->status,
                    'detail' => $latest->detail,
                    'response_time_ms' => $latest->response_time_ms,
                    'checked_at' => $latest->checked_at,
                ];
            }
        }

        return $results;
    }

    protected function checkDatabase(): array
    {
        $start = microtime(true);
        try {
            DB::connection()->getPdo();
            $ms = (int) round((microtime(true) - $start) * 1000);
            $driver = DB::connection()->getDriverName();

            return [
                'name' => 'Database',
                'status' => 'ok',
                'detail' => ucfirst($driver).' · '.$ms.'ms',
                'response_time_ms' => $ms,
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Database',
                'status' => 'fail',
                'detail' => $e->getMessage(),
                'response_time_ms' => 0,
            ];
        }
    }

    protected function checkRedis(): array
    {
        $start = microtime(true);
        try {
            $redis = Cache::store('redis')->getStore()->getRedis();

            if (method_exists($redis, 'connection')) {
                $redis->connection()->ping();
            }

            $ms = (int) round((microtime(true) - $start) * 1000);
            $host = config('database.redis.default.host', '127.0.0.1');
            $port = config('database.redis.default.port', 6379);

            return [
                'name' => 'Redis Cache',
                'status' => 'ok',
                'detail' => $host.':'.$port.' · '.$ms.'ms',
                'response_time_ms' => $ms,
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Redis Cache',
                'status' => 'warn',
                'detail' => 'Not available: '.class_basename($e),
                'response_time_ms' => 0,
            ];
        }
    }

    protected function checkQueue(): array
    {
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            $detail = $pendingJobs.' pending · '.$failedJobs.' failed';
            $status = $failedJobs > 10 ? 'warn' : 'ok';

            return [
                'name' => 'Queue Worker',
                'status' => $status,
                'detail' => $detail,
                'response_time_ms' => 0,
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Queue Worker',
                'status' => 'warn',
                'detail' => 'Unable to check: '.class_basename($e),
                'response_time_ms' => 0,
            ];
        }
    }

    protected function checkStorage(): array
    {
        try {
            $path = storage_path();
            $totalSpace = disk_total_space($path);
            $freeSpace = disk_free_space($path);
            $usedPercent = (int) round((1 - $freeSpace / $totalSpace) * 100);
            $freeGB = (int) round($freeSpace / (1024 * 1024 * 1024));

            $status = $usedPercent > 90 ? 'fail' : ($usedPercent > 75 ? 'warn' : 'ok');

            return [
                'name' => 'Storage',
                'status' => $status,
                'detail' => $usedPercent.'% used · '.$freeGB.'GB free',
                'response_time_ms' => 0,
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Storage',
                'status' => 'warn',
                'detail' => 'Unable to check',
                'response_time_ms' => 0,
            ];
        }
    }

    protected function checkMail(): array
    {
        try {
            $driver = config('mail.default', 'smtp');

            return [
                'name' => 'Mail ('.strtoupper($driver).')',
                'status' => 'ok',
                'detail' => $driver.' · configured',
                'response_time_ms' => 0,
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Mail',
                'status' => 'fail',
                'detail' => 'Not configured',
                'response_time_ms' => 0,
            ];
        }
    }

    protected function checkScheduler(): array
    {
        try {
            $cacheKey = 'larawatch:scheduler:last_run';
            $lastRun = Cache::get($cacheKey);

            if ($lastRun) {
                $ago = now()->diffForHumans($lastRun, true);

                return [
                    'name' => 'Scheduler',
                    'status' => 'ok',
                    'detail' => 'Last: '.$ago.' ago',
                    'response_time_ms' => 0,
                ];
            }

            return [
                'name' => 'Scheduler',
                'status' => 'ok',
                'detail' => 'No heartbeat recorded yet',
                'response_time_ms' => 0,
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Scheduler',
                'status' => 'warn',
                'detail' => 'Unable to check',
                'response_time_ms' => 0,
            ];
        }
    }
}
