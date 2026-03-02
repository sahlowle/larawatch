<div>
    @php
        $healthChecks = $this->healthChecks;
        $okCount = collect($healthChecks)->where('status', 'ok')->count();
        $warnCount = collect($healthChecks)->where('status', 'warn')->count();
        $failCount = collect($healthChecks)->where('status', 'fail')->count();
        $total = count($healthChecks);
        $overallStatus = collect($healthChecks)->contains(fn($c) => $c['status'] === 'fail')
            ? 'fail'
            : (collect($healthChecks)->contains(fn($c) => $c['status'] === 'warn')
                ? 'warn'
                : 'ok');
    @endphp

    @include('larawatch::monitor.components.status-strip', [
        'status' => $overallStatus,
        'message' => $okCount . '/' . $total . ' checks passing',
    ])

    {{-- STATS --}}
    <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Passing',
            'value' => $okCount,
            'icon' => 'check',
            'color' => 'green',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Warnings',
            'value' => $warnCount,
            'icon' => 'alert',
            'color' => 'yellow',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Failing',
            'value' => $failCount,
            'icon' => 'x-mark',
            'color' => 'red',
        ])
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Health Checks
            </div>
            <span
                class="tag {{ $overallStatus === 'ok' ? 'green' : ($overallStatus === 'warn' ? 'yellow' : 'red') }}">{{ $okCount }}/{{ $total }}
                OK</span>
        </div>
        <div class="health-list">
            @forelse($healthChecks as $check)
                @include('larawatch::monitor.components.health-item', [
                    'name' => $check['name'],
                    'detail' => $check['detail'],
                    'status' => $check['status'],
                ])
            @empty
                <div style="padding:40px 20px;text-align:center;color:var(--text-faint);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:.3;">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    No health checks configured
                </div>
            @endforelse
        </div>
    </div>
</div>
