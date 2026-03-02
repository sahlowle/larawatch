<div>
    {{-- STATS --}}
    @php
        $doneCount = $jobs->getCollection()->where('status', 'done')->count();
        $failedCount = $jobs->getCollection()->where('status', 'failed')->count();
        $pendingCount = $jobs
            ->getCollection()
            ->whereIn('status', ['pending', 'running'])
            ->count();
    @endphp
    <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Total Jobs',
            'value' => number_format($jobs->total()),
            'icon' => 'activity',
            'color' => 'blue',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Completed',
            'value' => $doneCount,
            'icon' => 'check',
            'color' => 'green',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Failed',
            'value' => $failedCount,
            'icon' => 'x-mark',
            'color' => 'red',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'In Queue',
            'value' => $pendingCount,
            'icon' => 'clock',
            'color' => 'yellow',
        ])
    </div>

    {{-- JOBS TABLE --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                All Queue Jobs
            </div>
            <span class="tag green">{{ $jobs->total() }} total</span>
        </div>
        <div class="card-body" style="padding:0 22px;">
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width:50px;">Status</th>
                        <th>Job Name</th>
                        <th style="width:90px;">Queue</th>
                        <th style="width:90px;text-align:right;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            <td>
                                <span class="queue-status {{ $job->status }}">{{ $job->status }}</span>
                            </td>
                            <td>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:12px;"
                                    title="{{ $job->name }}">
                                    {{ $job->short_name }}
                                </span>
                            </td>
                            <td>
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-faint);">
                                    {{ $job->queue ?? 'default' }}
                                </span>
                            </td>
                            <td class="duration">{{ $job->time_ago }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:40px 20px;color:var(--text-faint);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:.3;">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                </svg>
                                No jobs recorded yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($jobs->hasPages())
        <div class="pagination-wrap">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
