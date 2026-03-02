<div>
    @php $cacheStats = $this->cacheStats; @endphp

    {{-- STATS --}}
    <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Hit Rate',
            'value' => $cacheStats['hit_rate'] ?? 0,
            'unit' => '%',
            'icon' => 'activity',
            'color' => 'green',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Memory Usage',
            'value' => $cacheStats['memory_usage_percent'] ?? 0,
            'unit' => '%',
            'icon' => 'alert',
            'color' =>
                ($cacheStats['memory_usage_percent'] ?? 0) > 80
                    ? 'red'
                    : (($cacheStats['memory_usage_percent'] ?? 0) > 60
                        ? 'yellow'
                        : 'blue'),
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Keys Stored',
            'value' => number_format($cacheStats['keys_stored'] ?? 0),
            'icon' => 'list',
            'color' => 'blue',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Driver',
            'value' => ucfirst($cacheStats['driver'] ?? 'file'),
            'icon' => 'clock',
            'color' => 'purple',
        ])
    </div>

    {{-- DETAIL CARD --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Cache & Memory Details
            </div>
        </div>
        <div class="card-body" style="padding:12px 22px;">
            @include('larawatch::monitor.components.cache-stat', [
                'label' => 'Hit Rate',
                'value' => ($cacheStats['hit_rate'] ?? 0) . '%',
                'progressWidth' => $cacheStats['hit_rate'] ?? 0,
                'progressColor' => 'green',
            ])
            @include('larawatch::monitor.components.cache-stat', [
                'label' => 'Memory Usage',
                'value' => ($cacheStats['memory_usage_percent'] ?? 0) . '%',
                'progressWidth' => $cacheStats['memory_usage_percent'] ?? 0,
                'progressColor' =>
                    ($cacheStats['memory_usage_percent'] ?? 0) > 80
                        ? 'red'
                        : (($cacheStats['memory_usage_percent'] ?? 0) > 60
                            ? 'yellow'
                            : 'green'),
            ])
            @include('larawatch::monitor.components.cache-stat', [
                'label' => 'Keys Stored',
                'value' => number_format($cacheStats['keys_stored'] ?? 0),
            ])
            @include('larawatch::monitor.components.cache-stat', [
                'label' => 'Misses / hr',
                'value' => number_format($cacheStats['misses_per_hour'] ?? 0),
            ])
            @include('larawatch::monitor.components.cache-stat', [
                'label' => 'Evictions',
                'value' => number_format($cacheStats['evictions'] ?? 0),
            ])
            @include('larawatch::monitor.components.cache-stat', [
                'label' => ucfirst($cacheStats['driver'] ?? 'file') . ' Version',
                'value' => $cacheStats['driver_version'] ?? 'N/A',
            ])
        </div>
    </div>
</div>
