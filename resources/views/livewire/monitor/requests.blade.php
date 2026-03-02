<div>
    {{-- STATS --}}
    <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Total Requests',
            'value' => number_format($requests->total()),
            'icon' => 'list',
            'color' => 'blue',
        ])
        @php
            $successCount = $requests->getCollection()->where('status_code', '<', 400)->count();
            $errorCount = $requests->getCollection()->where('status_code', '>=', 400)->count();
        @endphp
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Successful',
            'value' => $successCount,
            'icon' => 'check',
            'color' => 'green',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Errors',
            'value' => $errorCount,
            'icon' => 'alert',
            'color' => 'red',
        ])
    </div>

    {{-- TABLE --}}
    <div class="card">
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
                All Requests
            </div>
            <span class="tag blue">{{ $requests->total() }} total</span>
        </div>
        <div class="card-body" style="padding:0 22px;">
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width:70px;">Method</th>
                        <th>Route</th>
                        <th style="width:60px;">Status</th>
                        <th style="width:140px;">Controller</th>
                        <th style="width:90px;text-align:right;">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        @include('larawatch::monitor.components.request-row', ['request' => $request])
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px 20px;color:var(--text-faint);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:.3;">
                                    <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    <path d="M9 10h.01M15 10h.01M8 14s1.5 2 4 2 4-2 4-2" />
                                </svg>
                                No requests recorded yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($requests->hasPages())
        <div class="pagination-wrap">
            {{ $requests->links() }}
        </div>
    @endif
</div>
