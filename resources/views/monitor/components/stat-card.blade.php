@props([
    'label' => '',
    'value' => '0',
    'unit' => '',
    'color' => 'blue',
    'icon' => 'list',
    'trend' => null,
    'trendDirection' => 'up',
    'trendLabel' => '',
])

@php
    $iconSvg = match ($icon) {
        'list'
            => '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'alert'
            => '<path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        'activity' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
        'mail'
            => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
        'check' => '<polyline points="20 6 9 17 4 12"/>',
        'x-mark' => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
        default
            => '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>',
    };

    $trendClass = $trendDirection === 'up' ? 'trend-up' : 'trend-down';

    $colorGradient = match ($color) {
        'blue' => 'linear-gradient(135deg, rgba(88,166,255,.15), transparent)',
        'green' => 'linear-gradient(135deg, rgba(63,185,80,.15), transparent)',
        'yellow' => 'linear-gradient(135deg, rgba(210,153,34,.15), transparent)',
        'red' => 'linear-gradient(135deg, rgba(248,81,73,.15), transparent)',
        'purple' => 'linear-gradient(135deg, rgba(188,140,255,.15), transparent)',
        default => 'none',
    };
@endphp

<div class="stat-card" style="position:relative;">
    <div
        style="position:absolute;top:0;left:0;right:0;bottom:0;background:{{ $colorGradient }};border-radius:inherit;pointer-events:none;">
    </div>
    <div style="position:relative;z-index:1;">
        <div class="stat-header">
            <span class="stat-label">{{ $label }}</span>
            <div class="stat-icon {{ $color }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    {!! $iconSvg !!}
                </svg>
            </div>
        </div>
        <div class="stat-value">
            {{ $value }}
            @if ($unit)
                <span class="stat-unit">{{ $unit }}</span>
            @endif
        </div>
        @if ($trend !== null)
            <div class="stat-trend">
                <span class="{{ $trendClass }}">{{ $trend }}</span>&nbsp;{{ $trendLabel }}
            </div>
        @endif
    </div>
</div>
