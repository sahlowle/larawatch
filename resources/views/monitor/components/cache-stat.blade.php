@props([
    'label' => '',
    'value' => '',
    'progressWidth' => null,
    'progressColor' => 'green',
])

<div class="cache-row">
    <div class="cache-label">{{ $label }}</div>
    @if ($progressWidth !== null)
        <div class="progress-bar">
            <div class="progress-fill {{ $progressColor }}" style="width:{{ $progressWidth }}%"></div>
        </div>
    @endif
    <div class="cache-value">{{ $value }}</div>
</div>
