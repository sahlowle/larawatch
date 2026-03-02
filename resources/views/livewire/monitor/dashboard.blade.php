<div>
    {{-- STATUS STRIP --}}
    @php
        $healthChecks = $this->healthChecks;
        $overallStatus = collect($healthChecks)->contains(fn($c) => ($c['status'] ?? 'ok') === 'fail')
            ? 'fail'
            : (collect($healthChecks)->contains(fn($c) => ($c['status'] ?? 'ok') === 'warn')
                ? 'warn'
                : 'ok');
        $overallMessage = match ($overallStatus) {
            'ok' => 'All systems operational',
            'warn' => 'Some systems need attention',
            'fail' => 'System issues detected',
        };
        $stats = $this->stats;
        $cacheStats = $this->cacheStats;
        $healthOkCount = collect($healthChecks)->where('status', 'ok')->count();
        $healthTotal = count($healthChecks);
    @endphp

    @include('larawatch::monitor.components.status-strip', [
        'status' => $overallStatus,
        'message' => $overallMessage,
    ])

    {{-- STAT CARDS --}}
    <div class="stat-grid">
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Requests / min',
            'value' => number_format($stats['requests_per_minute']),
            'icon' => 'list',
            'color' => 'blue',
            'trend' =>
                ($stats['trends']['rpm_change_percent'] >= 0 ? '↑ ' : '↓ ') .
                abs($stats['trends']['rpm_change_percent']) .
                '%',
            'trendDirection' => $stats['trends']['rpm_change_percent'] >= 0 ? 'up' : 'down',
            'trendLabel' => 'vs last hour',
        ])

        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Avg Response',
            'value' => $stats['avg_response_ms'],
            'unit' => 'ms',
            'icon' => 'clock',
            'color' => 'green',
            'trend' =>
                ($stats['trends']['response_change_ms'] >= 0 ? '↓ ' : '↑ ') .
                abs($stats['trends']['response_change_ms']) .
                'ms',
            'trendDirection' => $stats['trends']['response_change_ms'] >= 0 ? 'up' : 'down',
            'trendLabel' => 'faster today',
        ])

        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Error Rate',
            'value' => $stats['error_rate'],
            'unit' => '%',
            'icon' => 'alert',
            'color' => 'yellow',
            'trend' => $stats['trends']['error_rate_15min'] . '%',
            'trendDirection' => $stats['trends']['error_rate_15min'] > $stats['error_rate'] ? 'down' : 'up',
            'trendLabel' => 'last 15 min',
        ])

        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Queue Jobs',
            'value' => $stats['jobs_summary']['total'],
            'icon' => 'activity',
            'color' => 'red',
            'trend' => $stats['jobs_summary']['failed'] . ' failed',
            'trendDirection' => $stats['jobs_summary']['failed'] > 0 ? 'down' : 'up',
            'trendLabel' => '· ' . $stats['jobs_summary']['pending'] . ' pending',
        ])
    </div>

    {{-- CHART + HEALTH --}}
    <div class="main-grid">
        {{-- REQUEST TRAFFIC CHART --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    Request Traffic
                </div>
                <div style="display:flex;gap:12px;align-items:center;">
                    <span class="tag green">● Requests</span>
                    <span class="tag red">● Errors</span>
                    <select wire:change="setChartRange($event.target.value)"
                        style="font-size:12px;border:1px solid var(--border2);background:var(--surface2);padding:4px 8px;border-radius:6px;color:var(--text-muted);font-family:inherit;cursor:pointer;">
                        <option value="30" @selected($chartMinutes == 30)>Last 30 min</option>
                        <option value="60" @selected($chartMinutes == 60)>Last 1 hr</option>
                        <option value="360" @selected($chartMinutes == 360)>Last 6 hrs</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" wire:ignore>
                    <svg class="chart-svg" id="traffic-chart" viewBox="0 0 600 160" preserveAspectRatio="none"></svg>
                </div>
                <div class="chart-labels" id="chart-labels" wire:ignore></div>
            </div>
        </div>

        {{-- HEALTH CHECKS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    Health Checks
                </div>
                <span class="tag green">{{ $healthOkCount }}/{{ $healthTotal }} OK</span>
            </div>
            <div class="health-list">
                @forelse($healthChecks as $check)
                    @include('larawatch::monitor.components.health-item', [
                        'name' => $check['name'],
                        'detail' => $check['detail'],
                        'status' => $check['status'],
                    ])
                @empty
                    <div style="padding:20px;text-align:center;color:var(--text-faint);">No health checks configured
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RECENT REQUESTS --}}
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6" />
                    <line x1="8" y1="12" x2="21" y2="12" />
                    <line x1="8" y1="18" x2="21" y2="18" />
                    <line x1="3" y1="6" x2="3.01" y2="6" />
                    <line x1="3" y1="12" x2="3.01" y2="12" />
                    <line x1="3" y1="18" x2="3.01" y2="18" />
                </svg>
                Recent Requests
            </div>
            <a href="{{ route('monitor.requests') }}" class="card-action">View all →</a>
        </div>
        <div class="card-body" style="padding:0 20px;">
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width:70px;">Method</th>
                        <th>Route</th>
                        <th style="width:60px;">Status</th>
                        <th style="width:70px;">Controller</th>
                        <th style="width:80px;text-align:right;">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->recentRequests as $request)
                        @include('larawatch::monitor.components.request-row', ['request' => $request])
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:20px;color:var(--text-faint);">No
                                requests recorded yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- BOTTOM GRID: JOBS + EXCEPTIONS + CACHE --}}
    <div class="bottom-grid">
        {{-- QUEUE JOBS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    Queue Jobs
                </div>
                <a href="{{ route('monitor.jobs') }}" class="card-action">View all →</a>
            </div>
            <div class="card-body" style="padding:0 20px;">
                @forelse($this->recentJobs as $job)
                    @include('larawatch::monitor.components.queue-item', ['job' => $job])
                @empty
                    <div style="padding:20px;text-align:center;color:var(--text-faint);">No jobs recorded yet</div>
                @endforelse
            </div>
        </div>

        {{-- EXCEPTIONS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    Exceptions
                </div>
                <span class="tag red">{{ $this->exceptionsToday }} today</span>
            </div>
            <div class="card-body" style="padding:0 20px;">
                @forelse($this->recentExceptions as $exception)
                    @include('larawatch::monitor.components.exception-item', ['exception' => $exception])
                @empty
                    <div style="padding:20px;text-align:center;color:var(--text-faint);">No exceptions recorded</div>
                @endforelse
            </div>
        </div>

        {{-- CACHE & MEMORY --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    Cache & Memory
                </div>
            </div>
            <div class="card-body" style="padding:0 20px;">
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
                    'label' => 'Keys stored',
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
                    'label' => ucfirst($cacheStats['driver'] ?? 'file') . ' version',
                    'value' => $cacheStats['driver_version'] ?? 'N/A',
                ])
            </div>
        </div>
    </div>

    @script
        <script>
            // ─── CHART ──────────────────────────────────────────────────────────
            function drawChart(chartData) {
                const svg = document.getElementById('traffic-chart');
                if (!svg || !chartData.length) return;
                const W = 600,
                    H = 160;
                const reqs = chartData.map(d => d.requests || 0);
                const errs = chartData.map(d => d.errors || 0);
                const maxReqs = Math.max(...reqs, 1);
                const maxErrs = Math.max(...errs, 1);

                function toPath(data, max, fill) {
                    const norm = data.map(v => H - 10 - (v / max) * (H - 30));
                    const xStep = W / (data.length - 1 || 1);
                    let d = `M0,${norm[0]}`;
                    for (let i = 1; i < data.length; i++) {
                        const cx = (i - 0.5) * xStep;
                        d += ` C${cx},${norm[i-1]} ${cx},${norm[i]} ${i*xStep},${norm[i]}`;
                    }
                    if (fill) d += ` L${W},${H} L0,${H} Z`;
                    return d;
                }

                const isLight = document.documentElement.getAttribute('data-theme') === 'light';
                const gridColor = isLight ? 'rgba(0,0,0,.06)' : 'rgba(255,255,255,.04)';
                const reqColor = isLight ? '#16a34a' : '#3fb950';
                const errColor = isLight ? '#dc2626' : '#f85149';

                svg.innerHTML = `
                <defs>
                    <linearGradient id="gReq" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="${reqColor}" stop-opacity=".2"/><stop offset="100%" stop-color="${reqColor}" stop-opacity="0"/></linearGradient>
                    <linearGradient id="gErr" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="${errColor}" stop-opacity=".2"/><stop offset="100%" stop-color="${errColor}" stop-opacity="0"/></linearGradient>
                </defs>
                ${[0.25,0.5,0.75,1].map(y => `<line x1="0" y1="${y*H}" x2="${W}" y2="${y*H}" stroke="${gridColor}" stroke-width="1"/>`).join('')}
                <path d="${toPath(reqs, maxReqs, true)}" fill="url(#gReq)"/>
                <path d="${toPath(errs, maxErrs, true)}" fill="url(#gErr)"/>
                <path d="${toPath(reqs, maxReqs, false)}" fill="none" stroke="${reqColor}" stroke-width="2" stroke-linecap="round"/>
                <path d="${toPath(errs, maxErrs, false)}" fill="none" stroke="${errColor}" stroke-width="1.5" stroke-linecap="round"/>
            `;

                const labelsEl = document.getElementById('chart-labels');
                if (labelsEl) {
                    const step = Math.max(1, Math.floor(chartData.length / 6));
                    const labels = [];
                    for (let i = 0; i < chartData.length; i += step) labels.push(chartData[i].label);
                    if (labels[labels.length - 1] !== 'now') labels.push('now');
                    labelsEl.innerHTML = labels.map(l => `<span>${l}</span>`).join('');
                }
            }

            // Load chart on init and on Livewire updates
            function loadChart() {
                fetch(`{{ route('monitor.api.chart') }}?minutes={{ $chartMinutes }}`)
                    .then(r => r.json())
                    .then(data => drawChart(data))
                    .catch(() => {});
            }

            loadChart();

            Livewire.hook('morph.updated', () => {
                setTimeout(loadChart, 100);
            });
        </script>
    @endscript
</div>
