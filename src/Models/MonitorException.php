<?php

namespace Sahlowle\Larawatch\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorException extends Model
{
    public function getTable()
    {
        return config('larawatch.table_prefix', 'monitor_').'exceptions';
    }

    protected $guarded = [];

    protected $casts = [
        'count' => 'integer',
        'line' => 'integer',
        'last_occurred_at' => 'datetime',
    ];

    public function getConnectionName()
    {
        return config('larawatch.connection') ?? parent::getConnectionName();
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('last_occurred_at', '>=', now()->subHours($hours));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('last_occurred_at', today());
    }

    public function getShortClassAttribute(): string
    {
        $parts = explode('\\', $this->class);

        return end($parts);
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->last_occurred_at?->diffForHumans() ?? 'unknown';
    }
}
