@props([
    'name' => '',
    'detail' => '',
    'status' => 'ok',
])

@php
    $statusIcon = match ($status) {
        'ok' => '<polyline points="20 6 9 17 4 12"/>',
        'warn'
            => '<path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        'fail'
            => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
        default => '<circle cx="12" cy="12" r="10"/>',
    };
@endphp

<div class="health-item">
    <div class="health-dot {{ $status }}"></div>
    <div style="flex:1;min-width:0;">
        <div class="health-name">{{ $name }}</div>
        <div class="health-detail">{{ $detail }}</div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <div class="health-status {{ $status }}">{{ strtoupper($status) }}</div>
    </div>
</div>
