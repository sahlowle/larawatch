<?php

namespace Sahlowle\Larawatch\Services;

use Illuminate\Support\Facades\Auth;
use Sahlowle\Larawatch\Models\MonitorException;
use Throwable;

class ExceptionMonitorService
{
    /**
     * Record an exception, grouping by hash to increment count.
     */
    public function record(Throwable $exception, ?int $requestId = null): void
    {
        if (! config('larawatch.features.exceptions', true)) {
            return;
        }

        $hash = $this->generateHash($exception);
        $user = Auth::user();

        $existing = MonitorException::where('hash', $hash)->first();

        if ($existing) {
            $existing->update([
                'count' => $existing->count + 1,
                'last_occurred_at' => now(),
                'request_id' => $requestId ?? $existing->request_id,
                'user_id' => $user?->id ?? $existing->user_id,
                'user_email' => $user?->email ?? $existing->user_email,
            ]);
        } else {
            MonitorException::create([
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'request_id' => $requestId,
                'user_id' => $user?->id,
                'user_email' => $user?->email,
                'count' => 1,
                'hash' => $hash,
                'last_occurred_at' => now(),
            ]);
        }
    }

    /**
     * Generate a unique hash for an exception based on class, message, file, and line.
     */
    protected function generateHash(Throwable $exception): string
    {
        return hash('sha256', implode('|', [
            get_class($exception),
            $exception->getFile(),
            $exception->getLine(),
        ]));
    }
}
