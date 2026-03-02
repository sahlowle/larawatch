<?php

namespace Sahlowle\Larawatch\Commands;

use Illuminate\Console\Command;
use Sahlowle\Larawatch\Models\MonitorCacheStat;
use Sahlowle\Larawatch\Models\MonitorException;
use Sahlowle\Larawatch\Models\MonitorHealthCheck;
use Sahlowle\Larawatch\Models\MonitorJob;
use Sahlowle\Larawatch\Models\MonitorMail;
use Sahlowle\Larawatch\Models\MonitorRequest;
use Sahlowle\Larawatch\Models\MonitorScheduledTask;

class PruneMonitorDataCommand extends Command
{
    protected $signature = 'larawatch:prune
                            {--type= : Prune a specific type only (requests, exceptions, jobs, health_checks, cache_stats, mails, scheduled_tasks)}
                            {--force : Skip confirmation}';

    protected $description = 'Prune old monitoring data based on retention config';

    protected array $models = [
        'requests' => MonitorRequest::class,
        'exceptions' => MonitorException::class,
        'jobs' => MonitorJob::class,
        'health_checks' => MonitorHealthCheck::class,
        'cache_stats' => MonitorCacheStat::class,
        'mails' => MonitorMail::class,
        'scheduled_tasks' => MonitorScheduledTask::class,
    ];

    protected array $dateColumns = [
        'requests' => 'created_at',
        'exceptions' => 'last_occurred_at',
        'jobs' => 'created_at',
        'health_checks' => 'checked_at',
        'cache_stats' => 'recorded_at',
        'mails' => 'created_at',
        'scheduled_tasks' => 'created_at',
    ];

    public function handle(): int
    {
        $type = $this->option('type');
        $retention = config('larawatch.data_retention', []);

        if ($type) {
            if (! isset($this->models[$type])) {
                $this->error("Unknown type: {$type}. Valid: ".implode(', ', array_keys($this->models)));

                return self::FAILURE;
            }
            $this->pruneType($type, $retention[$type] ?? []);
        } else {
            foreach ($this->models as $key => $model) {
                $this->pruneType($key, $retention[$key] ?? []);
            }
        }

        return self::SUCCESS;
    }

    protected function pruneType(string $type, array $config): void
    {
        $model = $this->models[$type];
        $dateColumn = $this->dateColumns[$type];

        if (isset($config['keep_hours']) && $config['keep_hours'] > 0) {
            $cutoff = now()->subHours($config['keep_hours']);
        } elseif (isset($config['keep_days']) && $config['keep_days'] > 0) {
            $cutoff = now()->subDays($config['keep_days']);
        } else {
            $this->line("  ⊘ <comment>{$type}</comment>: pruning disabled (keep = 0)");

            return;
        }

        $count = $model::where($dateColumn, '<', $cutoff)->count();

        if ($count === 0) {
            $this->line("  ✓ <info>{$type}</info>: nothing to prune");

            return;
        }

        $model::where($dateColumn, '<', $cutoff)->delete();
        $this->line("  ✓ <info>{$type}</info>: pruned <comment>{$count}</comment> records older than <comment>{$cutoff->diffForHumans()}</comment>");
    }
}
