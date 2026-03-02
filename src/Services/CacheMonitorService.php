<?php

namespace Sahlowle\Larawatch\Services;

use Illuminate\Support\Facades\Cache;
use Sahlowle\Larawatch\Models\MonitorCacheStat;

class CacheMonitorService
{
    /**
     * Collect and store current cache/memory stats.
     */
    public function collectStats(): array
    {
        $stats = $this->gatherStats();

        MonitorCacheStat::create([
            'hit_rate' => $stats['hit_rate'],
            'memory_usage_percent' => $stats['memory_usage_percent'],
            'keys_stored' => $stats['keys_stored'],
            'misses_per_hour' => $stats['misses_per_hour'],
            'evictions' => $stats['evictions'],
            'driver' => $stats['driver'],
            'driver_version' => $stats['driver_version'],
            'recorded_at' => now(),
        ]);

        return $stats;
    }

    /**
     * Get the latest recorded stats.
     */
    public function getLatestStats(): array
    {
        $latest = MonitorCacheStat::orderByDesc('recorded_at')->first();

        if (! $latest) {
            return $this->defaultStats();
        }

        return [
            'hit_rate' => $latest->hit_rate,
            'memory_usage_percent' => $latest->memory_usage_percent,
            'keys_stored' => $latest->keys_stored,
            'misses_per_hour' => $latest->misses_per_hour,
            'evictions' => $latest->evictions,
            'driver' => $latest->driver,
            'driver_version' => $latest->driver_version,
            'recorded_at' => $latest->recorded_at,
        ];
    }

    /**
     * Gather real-time cache stats.
     */
    protected function gatherStats(): array
    {
        try {
            $driver = config('cache.default', 'file');

            if ($driver === 'redis') {
                return $this->getRedisStats();
            }

            return $this->defaultStats();
        } catch (\Throwable $e) {
            return $this->defaultStats();
        }
    }

    protected function getRedisStats(): array
    {
        try {
            $redis = Cache::store('redis')->getStore()->getRedis();

            if (method_exists($redis, 'connection')) {
                $info = $redis->connection()->info();
            } else {
                return $this->defaultStats();
            }

            $hits = $info['Stats']['keyspace_hits'] ?? $info['keyspace_hits'] ?? 0;
            $misses = $info['Stats']['keyspace_misses'] ?? $info['keyspace_misses'] ?? 0;
            $total = $hits + $misses;
            $hitRate = $total > 0 ? round(($hits / $total) * 100, 2) : 0;

            $usedMemory = $info['Memory']['used_memory'] ?? $info['used_memory'] ?? 0;
            $maxMemory = $info['Memory']['maxmemory'] ?? $info['maxmemory'] ?? 0;
            $memoryPercent = ($maxMemory > 0) ? round(($usedMemory / $maxMemory) * 100, 2) : 0;

            $keys = $info['Keyspace']['db0']['keys'] ?? 0;
            if (is_string($keys) && str_contains($keys, 'keys=')) {
                preg_match('/keys=(\d+)/', $keys, $m);
                $keys = (int) ($m[1] ?? 0);
            }

            $evictions = $info['Stats']['evicted_keys'] ?? $info['evicted_keys'] ?? 0;
            $version = $info['Server']['redis_version'] ?? $info['redis_version'] ?? 'unknown';

            return [
                'hit_rate' => $hitRate,
                'memory_usage_percent' => $memoryPercent,
                'keys_stored' => (int) $keys,
                'misses_per_hour' => (int) $misses,
                'evictions' => (int) $evictions,
                'driver' => 'redis',
                'driver_version' => $version,
            ];
        } catch (\Throwable $e) {
            return $this->defaultStats();
        }
    }

    protected function defaultStats(): array
    {
        return [
            'hit_rate' => 0,
            'memory_usage_percent' => 0,
            'keys_stored' => 0,
            'misses_per_hour' => 0,
            'evictions' => 0,
            'driver' => config('cache.default', 'file'),
            'driver_version' => null,
        ];
    }
}
