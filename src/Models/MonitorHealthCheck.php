<?php

namespace Sahlowle\Larawatch\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorHealthCheck extends Model
{
    public $timestamps = false;

    public function getTable()
    {
        return config('larawatch.table_prefix', 'monitor_').'health_checks';
    }

    protected $guarded = [];

    protected $casts = [
        'checked_at' => 'datetime',
        'response_time_ms' => 'integer',
        'meta' => 'array',
    ];

    public function getConnectionName()
    {
        return config('larawatch.connection') ?? parent::getConnectionName();
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('checked_at');
    }

    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    public function getIsOkAttribute(): bool
    {
        return $this->status === 'ok';
    }

    public function getIsWarningAttribute(): bool
    {
        return $this->status === 'warn';
    }

    public function getIsFailAttribute(): bool
    {
        return $this->status === 'fail';
    }
}
