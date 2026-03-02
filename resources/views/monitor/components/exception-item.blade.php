@props([
    'exception' => null,
    'class' => '',
    'message' => '',
    'count' => 0,
    'time' => '',
])

@php
    if ($exception) {
        $class = $exception->short_class;
        $message = $exception->message;
        $count = $exception->count;
        $time = $exception->time_ago;
    }
@endphp

<div class="exception-item">
    <div style="display:flex;align-items:center;gap:8px;">
        <span
            style="width:6px;height:6px;border-radius:50%;background:var(--red);flex-shrink:0;box-shadow:0 0 6px rgba(248,81,73,.4);"></span>
        <div class="exception-class">{{ $class }}</div>
    </div>
    <div class="exception-msg">{{ $message }}</div>
    <div class="exception-meta">
        <span class="exception-count">&times;{{ $count }}</span>
        <span class="exception-time">{{ $time }}</span>
        @if ($exception?->user_email)
            <span style="color:var(--blue);display:inline-flex;align-items:center;gap:4px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" style="width:11px;height:11px;">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                {{ $exception->user_email }}
            </span>
        @endif
    </div>
</div>
