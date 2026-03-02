<?php

namespace Sahlowle\Larawatch\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorScheduledTask extends Model
{
    public function getTable()
    {
        return config('larawatch.table_prefix', 'monitor_').'scheduled_tasks';
    }

    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'next_run_at' => 'datetime',
        'duration_ms' => 'integer',
        'without_overlapping' => 'boolean',
        'on_one_server' => 'boolean',
        'run_in_background' => 'boolean',
    ];

    public function getConnectionName()
    {
        return config('larawatch.connection') ?? parent::getConnectionName();
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function getShortCommandAttribute(): string
    {
        if (str_contains($this->command, 'Closure')) {
            return 'Closure';
        }

        $parts = explode(' ', $this->command);

        return $parts[0] ?? $this->command;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at?->diffForHumans() ?? 'unknown';
    }

    public function getFormattedDurationAttribute(): string
    {
        if (! $this->duration_ms) {
            return '—';
        }

        return $this->duration_ms > 999
            ? number_format($this->duration_ms / 1000, 2).'s'
            : $this->duration_ms.'ms';
    }
}
