<div>
    {{-- STATS --}}
    <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Total Emails',
            'value' => number_format($mails->total()),
            'icon' => 'mail',
            'color' => 'blue',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Sent Today',
            'value' => $sentToday,
            'icon' => 'check',
            'color' => 'green',
        ])
        @include('larawatch::monitor.components.stat-card', [
            'label' => 'Failed Today',
            'value' => $failedToday,
            'icon' => 'x-mark',
            'color' => 'red',
        ])
    </div>

    {{-- MAIL TABLE --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                </svg>
                All Emails
            </div>
            <span class="tag blue">{{ $mails->total() }} total</span>
        </div>
        <div class="card-body" style="padding:0 22px;">
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width:55px;">Status</th>
                        <th>To</th>
                        <th>Subject</th>
                        <th style="width:80px;">Mailer</th>
                        <th style="width:70px;">Size</th>
                        <th style="width:120px;">User</th>
                        <th style="width:100px;text-align:right;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mails as $mail)
                        <tr>
                            <td>
                                @if ($mail->status === 'sent')
                                    <span class="method-badge method-GET">SENT</span>
                                @else
                                    <span class="method-badge method-DELETE">FAIL</span>
                                @endif
                            </td>
                            <td>
                                <span class="route-path" title="{{ $mail->to }}">{{ $mail->short_to }}</span>
                            </td>
                            <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                title="{{ $mail->subject }}">
                                {{ $mail->subject ?? '(no subject)' }}
                            </td>
                            <td>
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-muted);">{{ $mail->mailer }}</span>
                            </td>
                            <td>
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-muted);">
                                    {{ $mail->size ? number_format($mail->size / 1024, 1) . 'KB' : '—' }}
                                </span>
                            </td>
                            <td>
                                @if ($mail->user_email)
                                    <span
                                        style="font-size:11px;color:var(--blue);display:flex;align-items:center;gap:4px;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            style="width:11px;height:11px;flex-shrink:0;">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        {{ $mail->user_email }}
                                    </span>
                                @else
                                    <span style="font-size:11px;color:var(--text-faint);">system</span>
                                @endif
                            </td>
                            <td class="duration" style="font-size:11px;">{{ $mail->time_ago }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px 20px;color:var(--text-faint);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:.3;">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                No emails recorded yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($mails->hasPages())
        <div class="pagination-wrap">
            {{ $mails->links() }}
        </div>
    @endif
</div>
