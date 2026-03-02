<div>
    {{-- STATS --}}
    @php
        $totalRegistered = count($registeredTasks);
        $doneCount = collect($registeredTasks)->where('last_status', 'done')->count();
        $failedCount = collect($registeredTasks)->where('last_status', 'failed')->count();
        $pendingCount = collect($registeredTasks)
            ->whereIn('last_status', ['pending', 'running'])
            ->count();
    @endphp
    <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Registered Tasks',
            'value' => $totalRegistered,
            'icon' => 'clock',
            'color' => 'blue',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Last Run OK',
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
            'label' => 'Pending / Running',
            'value' => $pendingCount,
            'icon' => 'activity',
            'color' => 'yellow',
        ])
    </div>

    {{-- REGISTERED TASKS --}}
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                Registered Tasks
            </div>
            <span class="tag blue">{{ $totalRegistered }} tasks</span>
        </div>
        <div class="card-body" style="padding:0 22px;">
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width:50px;">Status</th>
                        <th>Command</th>
                        <th style="width:130px;">Schedule</th>
                        <th style="width:130px;">Next Run</th>
                        <th style="width:100px;">Last Run</th>
                        <th style="width:80px;text-align:right;">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registeredTasks as $task)
                        <tr>
                            <td>
                                @php
                                    $statusClass = match ($task['last_status']) {
                                        'done' => 'method-GET',
                                        'running' => 'method-POST',
                                        'failed' => 'method-DELETE',
                                        'skipped' => 'method-PUT',
                                        default => 'method-PATCH',
                                    };
                                    $statusLabel = strtoupper($task['last_status']);
                                @endphp
                                <span class="method-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:12px;"
                                    title="{{ $task['command'] }}">
                                    {{ $task['command'] }}
                                </span>
                                @if ($task['description'])
                                    <div style="font-size:11px;color:var(--text-faint);margin-top:2px;">
                                        {{ $task['description'] }}</div>
                                @endif
                            </td>
                            <td>
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-muted);"
                                    title="{{ $task['expression'] }}">
                                    {{ $task['human_readable'] }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:11px;color:var(--text-muted);">
                                    {{ $task['next_run_at'] ? \Carbon\Carbon::parse($task['next_run_at'])->diffForHumans() : '—' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:11px;color:var(--text-muted);">
                                    {{ $task['last_run_at'] ? $task['last_run_at']->diffForHumans() : 'Never' }}
                                </span>
                            </td>
                            <td class="duration">{{ $task['last_duration'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px 20px;color:var(--text-faint);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:.3;">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                No scheduled tasks registered
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- EXECUTION HISTORY --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                Execution History
            </div>
            <span class="tag green">{{ $taskHistory->total() }} runs</span>
        </div>
        <div class="card-body" style="padding:0 22px;">
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width:50px;">Status</th>
                        <th>Command</th>
                        <th style="width:110px;">Cron</th>
                        <th style="width:80px;text-align:right;">Duration</th>
                        <th style="width:110px;text-align:right;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taskHistory as $run)
                        <tr>
                            <td>
                                @php
                                    $runStatusClass = match ($run->status) {
                                        'done' => 'method-GET',
                                        'running' => 'method-POST',
                                        'failed' => 'method-DELETE',
                                        'skipped' => 'method-PUT',
                                        default => 'method-PATCH',
                                    };
                                @endphp
                                <span class="method-badge {{ $runStatusClass }}">{{ strtoupper($run->status) }}</span>
                            </td>
                            <td>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:12px;"
                                    title="{{ $run->command }}">
                                    {{ $run->short_command }}
                                </span>
                            </td>
                            <td>
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-faint);">
                                    {{ $run->expression }}
                                </span>
                            </td>
                            <td class="duration">{{ $run->formatted_duration }}</td>
                            <td class="duration" style="font-size:11px;">{{ $run->time_ago }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px 20px;color:var(--text-faint);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:.3;">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                </svg>
                                No task executions recorded yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($taskHistory->hasPages())
        <div class="pagination-wrap">
            {{ $taskHistory->links() }}
        </div>
    @endif
</div>
