<?php

namespace Sahlowle\Larawatch\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorCacheStat extends Model
{
    public $timestamps = false;

    public function getTable()
    {
        return config('larawatch.table_prefix', 'monitor_').'cache_stats';
    }

    protected $guarded = [];

    protected $casts = [
        'recorded_at' => 'datetime',
        'hit_rate' => 'float',
        'memory_usage_percent' => 'float',
        'keys_stored' => 'integer',
        'misses_per_hour' => 'integer',
        'evictions' => 'integer',
        'meta' => 'array',
    ];

    public function getConnectionName()
    {
        return config('larawatch.connection') ?? parent::getConnectionName();
    }

    public function scopeLatestRecord($query)
    {
        return $query->orderByDesc('recorded_at');
    }
}
