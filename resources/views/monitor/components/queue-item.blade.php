@props([
    'job' => null,
    'name' => '',
    'status' => 'pending',
    'time' => '',
])

@php
    if ($job) {
        $name = $job->short_name;
        $status = $job->status;
        $time = $job->time_ago;
    }

    $statusDot = match ($status) {
        'done' => 'var(--green)',
        'running' => 'var(--blue)',
        'failed' => 'var(--red)',
        default => 'var(--yellow)',
    };
@endphp

<div class="queue-item">
    <span
        style="width:6px;height:6px;border-radius:50%;background:{{ $statusDot }};flex-shrink:0;box-shadow:0 0 6px {{ $statusDot }};"></span>
    <span class="queue-name" title="{{ $name }}">{{ $name }}</span>
    <span class="queue-time">{{ $time }}</span>
    <span class="queue-status {{ $status }}">{{ $status }}</span>
</div>
