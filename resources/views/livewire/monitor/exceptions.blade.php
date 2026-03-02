<div>
    {{-- STATS --}}
    <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Total Exceptions',
            'value' => number_format($exceptions->total()),
            'icon' => 'alert',
            'color' => 'red',
        ])
        @php
            $todayCount = $exceptions->getCollection()->filter(fn($e) => $e->created_at?->isToday())->count();
        @endphp
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Today',
            'value' => $todayCount,
            'icon' => 'activity',
            'color' => 'yellow',
        ])
        @php
            $uniqueClasses = $exceptions->getCollection()->unique('class')->count();
        @endphp
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Unique Types',
            'value' => $uniqueClasses,
            'icon' => 'list',
            'color' => 'purple',
        ])
    </div>

    {{-- EXCEPTIONS LIST --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
                All Exceptions
            </div>
            <span class="tag red">{{ $exceptions->total() }} total</span>
        </div>
        <div class="card-body" style="padding:0 22px;">
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width:auto;">Exception</th>
                        <th style="width:auto;">Message</th>
                        <th style="width:100px;">File</th>
                        <th style="width:50px;">Count</th>
                        <th style="width:80px;">User</th>
                        <th style="width:90px;text-align:right;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exceptions as $exception)
                        <tr>
                            <td>
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--red);font-weight:500;">
                                    {{ $exception->short_class }}
                                </span>
                            </td>
                            <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-muted);font-size:12px;"
                                title="{{ $exception->message }}">
                                {{ $exception->message }}
                            </td>
                            <td>
                                @if ($exception->file)
                                    <span
                                        style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-faint);">
                                        {{ basename($exception->file) }}:{{ $exception->line }}
                                    </span>
                                @else
                                    <span style="color:var(--text-faint);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="tag red" style="font-size:10px;">&times;{{ $exception->count }}</span>
                            </td>
                            <td>
                                @if ($exception->user_email)
                                    <span
                                        style="font-size:11px;color:var(--blue);display:flex;align-items:center;gap:4px;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            style="width:11px;height:11px;flex-shrink:0;">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        {{ $exception->user_email }}
                                    </span>
                                @else
                                    <span style="font-size:11px;color:var(--text-faint);">—</span>
                                @endif
                            </td>
                            <td class="duration">{{ $exception->time_ago }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px 20px;color:var(--text-faint);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:.3;">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                                No exceptions recorded — great job!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($exceptions->hasPages())
        <div class="pagination-wrap">
            {{ $exceptions->links() }}
        </div>
    @endif
</div>
