<?php

namespace Sahlowle\Larawatch\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorJob extends Model
{
    public function getTable()
    {
        return config('larawatch.table_prefix', 'monitor_').'jobs';
    }

    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration_ms' => 'integer',
    ];

    public function getConnectionName()
    {
        return config('larawatch.connection') ?? parent::getConnectionName();
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function getShortNameAttribute(): string
    {
        $parts = explode('\\', $this->name);

        return end($parts);
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at?->diffForHumans() ?? 'unknown';
    }
}
