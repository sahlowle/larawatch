<?php

namespace Sahlowle\Larawatch\Services;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Sahlowle\Larawatch\Models\MonitorScheduledTask;

class ScheduledTaskMonitorService
{
    /**
     * Record when a scheduled task starts.
     */
    public function recordStarting(Event $event): void
    {
        try {
            MonitorScheduledTask::create([
                'command' => $this->getCommandName($event),
                'description' => $event->description,
                'expression' => $event->expression,
                'timezone' => $event->timezone,
                'without_overlapping' => $event->withoutOverlapping ?? false,
                'on_one_server' => $event->onOneServer ?? false,
                'run_in_background' => $event->runInBackground ?? false,
                'status' => 'running',
                'started_at' => now(),
                'next_run_at' => $this->getNextRunDate($event->expression, $event->timezone),
            ]);
        } catch (\Throwable $e) {
            // Silently ignore to not break the scheduler
        }
    }

    /**
     * Record when a scheduled task finishes successfully.
     */
    public function recordFinished(Event $event): void
    {
        try {
            $task = MonitorScheduledTask::where('command', $this->getCommandName($event))
                ->where('status', 'running')
                ->latest()
                ->first();

            if ($task) {
                $duration = $task->started_at
                    ? (int) (now()->diffInMilliseconds($task->started_at))
                    : null;

                $task->update([
                    'status' => 'done',
                    'duration_ms' => $duration,
                    'finished_at' => now(),
                    'next_run_at' => $this->getNextRunDate($event->expression, $event->timezone),
                ]);
            }
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }

    /**
     * Record when a scheduled task fails.
     */
    public function recordFailed(Event $event): void
    {
        try {
            $task = MonitorScheduledTask::where('command', $this->getCommandName($event))
                ->where('status', 'running')
                ->latest()
                ->first();

            if ($task) {
                $duration = $task->started_at
                    ? (int) (now()->diffInMilliseconds($task->started_at))
                    : null;

                $task->update([
                    'status' => 'failed',
                    'duration_ms' => $duration,
                    'finished_at' => now(),
                ]);
            } else {
                MonitorScheduledTask::create([
                    'command' => $this->getCommandName($event),
                    'description' => $event->description,
                    'expression' => $event->expression,
                    'timezone' => $event->timezone,
                    'status' => 'failed',
                    'started_at' => now(),
                    'finished_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }

    /**
     * Record a skipped task.
     */
    public function recordSkipped(Event $event): void
    {
        try {
            MonitorScheduledTask::create([
                'command' => $this->getCommandName($event),
                'description' => $event->description,
                'expression' => $event->expression,
                'timezone' => $event->timezone,
                'status' => 'skipped',
                'started_at' => now(),
                'finished_at' => now(),
                'next_run_at' => $this->getNextRunDate($event->expression, $event->timezone),
            ]);
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }

    /**
     * Get all registered scheduled tasks with their status.
     */
    public function getRegisteredTasks(): array
    {
        try {
            $schedule = app(Schedule::class);
            $events = $schedule->events();
            $tasks = [];

            foreach ($events as $event) {
                $commandName = $this->getCommandName($event);

                $latestRun = MonitorScheduledTask::where('command', $commandName)
                    ->latest()
                    ->first();

                $tasks[] = [
                    'command' => $commandName,
                    'description' => $event->description,
                    'expression' => $event->expression,
                    'human_readable' => $this->cronToHuman($event->expression),
                    'timezone' => $event->timezone,
                    'without_overlapping' => $event->withoutOverlapping ?? false,
                    'on_one_server' => $event->onOneServer ?? false,
                    'run_in_background' => $event->runInBackground ?? false,
                    'next_run_at' => $this->getNextRunDate($event->expression, $event->timezone),
                    'last_status' => $latestRun?->status ?? 'pending',
                    'last_run_at' => $latestRun?->started_at,
                    'last_duration' => $latestRun?->formatted_duration ?? '—',
                ];
            }

            return $tasks;
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function getCommandName(Event $event): string
    {
        $command = $event->command ?? '';

        if (str_contains($command, "'artisan'")) {
            $command = preg_replace("/^.*?'artisan'\s*/", '', $command);
        } elseif (str_contains($command, 'artisan')) {
            $command = preg_replace('/^.*?artisan\s*/', '', $command);
        }

        if (empty(trim($command))) {
            return $event->description ?: 'Closure';
        }

        return trim($command);
    }

    protected function getNextRunDate(string $expression, ?string $timezone = null): ?\DateTime
    {
        try {
            $cron = new CronExpression($expression);
            $tz = $timezone ? new \DateTimeZone($timezone) : null;

            return $cron->getNextRunDate('now', 0, false, $tz);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function cronToHuman(string $expression): string
    {
        return match ($expression) {
            '* * * * *' => 'Every minute',
            '*/5 * * * *' => 'Every 5 minutes',
            '*/10 * * * *' => 'Every 10 minutes',
            '*/15 * * * *' => 'Every 15 minutes',
            '*/30 * * * *' => 'Every 30 minutes',
            '0 * * * *' => 'Hourly',
            '0 */2 * * *' => 'Every 2 hours',
            '0 */4 * * *' => 'Every 4 hours',
            '0 */6 * * *' => 'Every 6 hours',
            '0 */12 * * *' => 'Every 12 hours',
            '0 0 * * *' => 'Daily at midnight',
            '0 1 * * *' => 'Daily at 1:00 AM',
            '0 0 * * 0' => 'Weekly on Sunday',
            '0 0 * * 1' => 'Weekly on Monday',
            '0 0 1 * *' => 'Monthly',
            '0 0 1 1 *' => 'Yearly',
            default => $expression,
        };
    }
}
