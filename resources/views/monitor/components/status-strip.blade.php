@props([
    'status' => 'ok',
    'message' => 'All systems operational',
    'uptime' => null,
    'lastIncident' => null,
])

@php
    $colors = match ($status) {
        'ok' => [
            'bg' => 'var(--green-bg)',
            'border' => 'var(--green-border)',
            'color' => 'var(--green)',
            'sub' => '#3d9e74',
        ],
        'warn' => [
            'bg' => 'var(--yellow-bg)',
            'border' => 'var(--yellow-border)',
            'color' => 'var(--yellow)',
            'sub' => '#b5890f',
        ],
        'fail' => [
            'bg' => 'var(--red-bg)',
            'border' => 'var(--red-border)',
            'color' => 'var(--red)',
            'sub' => '#b33030',
        ],
        default => [
            'bg' => 'var(--green-bg)',
            'border' => 'var(--green-border)',
            'color' => 'var(--green)',
            'sub' => '#3d9e74',
        ],
    };
@endphp

<div class="status-strip"
    style="background:{{ $colors['bg'] }};border-color:{{ $colors['border'] }};color:{{ $colors['color'] }};">
    @if ($status === 'ok')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
        </svg>
    @elseif($status === 'warn')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            <line x1="12" y1="9" x2="12" y2="13" />
            <line x1="12" y1="17" x2="12.01" y2="17" />
        </svg>
    @else
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <line x1="15" y1="9" x2="9" y2="15" />
            <line x1="9" y1="9" x2="15" y2="15" />
        </svg>
    @endif

    {{ $message }}

    @if ($uptime || $lastIncident)
        <span class="sub" style="color:{{ $colors['sub'] }}">
            @if ($uptime)
                · {{ $uptime }} uptime this month
            @endif
            @if ($lastIncident)
                · Last incident: {{ $lastIncident }}
            @endif
        </span>
    @endif
</div>
