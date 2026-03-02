<?php

namespace Sahlowle\Larawatch\Services;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Sahlowle\Larawatch\Models\MonitorJob;

class JobMonitorService
{
    /**
     * Record a job that has started processing.
     */
    public function recordProcessing(JobProcessing $event): void
    {
        if (! config('larawatch.features.jobs', true)) {
            return;
        }

        MonitorJob::updateOrCreate(
            ['name' => $event->job->resolveName(), 'status' => 'pending'],
            [
                'queue' => $event->job->getQueue() ?? 'default',
                'status' => 'running',
                'payload' => $event->job->getRawBody(),
                'started_at' => now(),
            ]
        );
    }

    /**
     * Record a job that completed successfully.
     */
    public function recordProcessed(JobProcessed $event): void
    {
        if (! config('larawatch.features.jobs', true)) {
            return;
        }

        $job = MonitorJob::where('name', $event->job->resolveName())
            ->where('status', 'running')
            ->latest()
            ->first();

        if ($job) {
            $duration = $job->started_at
                ? (int) round($job->started_at->diffInMilliseconds(now()))
                : null;

            $job->update([
                'status' => 'done',
                'finished_at' => now(),
                'duration_ms' => $duration,
            ]);
        } else {
            MonitorJob::create([
                'name' => $event->job->resolveName(),
                'queue' => $event->job->getQueue() ?? 'default',
                'status' => 'done',
                'finished_at' => now(),
            ]);
        }
    }

    /**
     * Record a failed job.
     */
    public function recordFailed(JobFailed $event): void
    {
        if (! config('larawatch.features.jobs', true)) {
            return;
        }

        $job = MonitorJob::where('name', $event->job->resolveName())
            ->where('status', 'running')
            ->latest()
            ->first();

        if ($job) {
            $duration = $job->started_at
                ? (int) round($job->started_at->diffInMilliseconds(now()))
                : null;

            $job->update([
                'status' => 'failed',
                'finished_at' => now(),
                'duration_ms' => $duration,
                'exception' => $event->exception?->getMessage(),
            ]);
        } else {
            MonitorJob::create([
                'name' => $event->job->resolveName(),
                'queue' => $event->job->getQueue() ?? 'default',
                'status' => 'failed',
                'finished_at' => now(),
                'exception' => $event->exception?->getMessage(),
            ]);
        }
    }
}
