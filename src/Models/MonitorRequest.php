<?php

namespace Sahlowle\Larawatch\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorRequest extends Model
{
    public $timestamps = false;

    public function getTable()
    {
        return config('larawatch.table_prefix', 'monitor_').'requests';
    }

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'duration_ms' => 'integer',
        'status_code' => 'integer',
        'payload_size' => 'integer',
        'response_size' => 'integer',
    ];

    public function getConnectionName()
    {
        return config('larawatch.connection') ?? parent::getConnectionName();
    }

    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    public function scopeErrors($query)
    {
        return $query->where('status_code', '>=', 400);
    }

    public function scopeServerErrors($query)
    {
        return $query->where('status_code', '>=', 500);
    }

    public function scopeSlow($query)
    {
        return $query->where('duration_ms', '>', config('larawatch.request.slow_threshold_ms', 1000));
    }

    public function getStatusClassAttribute(): string
    {
        if ($this->status_code >= 500) {
            return 'status-5xx';
        }
        if ($this->status_code >= 400) {
            return 'status-4xx';
        }

        return 'status-2xx';
    }

    public function getFormattedDurationAttribute(): string
    {
        return $this->duration_ms > 999
            ? number_format($this->duration_ms / 1000, 2).'s'
            : $this->duration_ms.'ms';
    }
}
