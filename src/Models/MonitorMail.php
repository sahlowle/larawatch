<?php

namespace Sahlowle\Larawatch\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorMail extends Model
{
    public function getTable()
    {
        return config('larawatch.table_prefix', 'monitor_').'mails';
    }

    protected $guarded = [];

    protected $casts = [
        'cc' => 'array',
        'bcc' => 'array',
        'size' => 'integer',
    ];

    public function getConnectionName()
    {
        return config('larawatch.connection') ?? parent::getConnectionName();
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at?->diffForHumans() ?? 'unknown';
    }

    public function getShortToAttribute(): string
    {
        $to = $this->to;
        if (strlen($to) > 40) {
            return substr($to, 0, 37).'...';
        }

        return $to;
    }
}
