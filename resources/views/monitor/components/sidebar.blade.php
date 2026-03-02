@php
    $currentRoute = request()->route()?->getName() ?? '';
    $prefix = config('monitor.path', 'monitor');
    $features = config('monitor.features', []);

    $requestsPerSec = $requestsPerSec ?? null;
    $failedJobsCount = $failedJobsCount ?? 0;
    $exceptionsCount = $exceptionsCount ?? 0;
@endphp

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
        </div>
        <div>
            <div class="logo-text">Monitor</div>
            <div class="logo-sub">for Laravel</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a class="nav-item {{ $currentRoute === 'monitor.dashboard' ? 'active' : '' }}"
            href="{{ route('monitor.dashboard') }}" wire:navigate>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
            </svg>
            Dashboard
        </a>

        @if ($features['requests'] ?? true)
            <a class="nav-item {{ $currentRoute === 'monitor.requests' ? 'active' : '' }}"
                href="{{ route('monitor.requests') }}" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6" />
                    <line x1="8" y1="12" x2="21" y2="12" />
                    <line x1="8" y1="18" x2="21" y2="18" />
                    <line x1="3" y1="6" x2="3.01" y2="6" />
                    <line x1="3" y1="12" x2="3.01" y2="12" />
                    <line x1="3" y1="18" x2="3.01" y2="18" />
                </svg>
                Requests
            </a>
        @endif

        @if (($features['jobs'] ?? true) || ($features['scheduled_tasks'] ?? true))
            <div class="nav-section-label" style="margin-top:8px;">Workers</div>
        @endif

        @if ($features['jobs'] ?? true)
            <a class="nav-item {{ $currentRoute === 'monitor.jobs' ? 'active' : '' }}"
                href="{{ route('monitor.jobs') }}" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                Queue Jobs
            </a>
        @endif

        @if ($features['scheduled_tasks'] ?? true)
            <a class="nav-item {{ $currentRoute === 'monitor.scheduled-tasks' ? 'active' : '' }}"
                href="{{ route('monitor.scheduled-tasks') }}" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                Scheduled Tasks
            </a>
        @endif

        <div class="nav-section-label" style="margin-top:8px;">Observability</div>

        @if ($features['exceptions'] ?? true)
            <a class="nav-item {{ $currentRoute === 'monitor.exceptions' ? 'active' : '' }}"
                href="{{ route('monitor.exceptions') }}" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
                Exceptions
            </a>
        @endif

        @if ($features['mail'] ?? true)
            <a class="nav-item {{ $currentRoute === 'monitor.mail' ? 'active' : '' }}"
                href="{{ route('monitor.mail') }}" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                </svg>
                Mail
            </a>
        @endif

        <a class="nav-item" href="#">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <ellipse cx="12" cy="5" rx="9" ry="3" />
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" />
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
            </svg>
            Database
        </a>

        @if ($features['cache'] ?? true)
            <a class="nav-item {{ $currentRoute === 'monitor.cache' ? 'active' : '' }}"
                href="{{ route('monitor.cache') }}" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Cache
            </a>
        @endif

        <div class="nav-section-label" style="margin-top:8px;">System</div>

        @if ($features['health'] ?? true)
            <a class="nav-item {{ $currentRoute === 'monitor.health' ? 'active' : '' }}"
                href="{{ route('monitor.health') }}" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                </svg>
                Health
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="env-badge">
            <div class="env-dot"></div>
            <span class="env-name">{{ app()->environment() }}</span>
            <span class="env-version">v{{ app()->version() }}</span>
        </div>
    </div>
</aside>
