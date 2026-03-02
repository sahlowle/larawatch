<!DOCTYPE html>
<html lang="en" x-data="{ theme: localStorage.getItem('monitor-theme') || '{{ config('monitor.theme', 'light') }}' }" x-init="$watch('theme', val => {
    localStorage.setItem('monitor-theme', val);
    document.documentElement.setAttribute('data-theme', val)
});
document.documentElement.setAttribute('data-theme', theme)" :data-theme="theme">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Laravel Monitor</title>
    <script>
        (function() {
            var t = localStorage.getItem('monitor-theme') || '{{ config('monitor.theme', 'light') }}';
            document.documentElement.setAttribute('data-theme', t);
        })();
        document.addEventListener('livewire:navigated', function() {
            var t = localStorage.getItem('monitor-theme') || '{{ config('monitor.theme', 'light') }}';
            document.documentElement.setAttribute('data-theme', t);
        });
    </script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════════════════════
           DESIGN TOKENS – Dark Premium Theme
           ═══════════════════════════════════════════════════════════ */
        :root {
            --bg: #0b0e14;
            --bg-subtle: #111520;
            --surface: #151a24;
            --surface2: #1c2333;
            --surface3: #232b3d;
            --border: rgba(255, 255, 255, 0.06);
            --border2: rgba(255, 255, 255, 0.1);
            --border-accent: rgba(99, 138, 255, 0.2);
            --text: #e6edf3;
            --text-muted: #8b949e;
            --text-faint: #484f58;
            --accent: #638aff;
            --accent-glow: rgba(99, 138, 255, 0.15);
            --accent-bg: rgba(99, 138, 255, 0.08);
            --accent-border: rgba(99, 138, 255, 0.25);
            --green: #3fb950;
            --green-bg: rgba(63, 185, 80, 0.08);
            --green-border: rgba(63, 185, 80, 0.25);
            --green-glow: rgba(63, 185, 80, 0.12);
            --yellow: #d29922;
            --yellow-bg: rgba(210, 153, 34, 0.08);
            --yellow-border: rgba(210, 153, 34, 0.25);
            --blue: #58a6ff;
            --blue-bg: rgba(88, 166, 255, 0.08);
            --blue-border: rgba(88, 166, 255, 0.25);
            --red: #f85149;
            --red-bg: rgba(248, 81, 73, 0.08);
            --red-border: rgba(248, 81, 73, 0.25);
            --purple: #bc8cff;
            --purple-bg: rgba(188, 140, 255, 0.08);
            --purple-border: rgba(188, 140, 255, 0.25);
            --shadow: 0 1px 3px rgba(0, 0, 0, .3), 0 1px 2px rgba(0, 0, 0, .2);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, .35), 0 2px 8px rgba(0, 0, 0, .2);
            --shadow-lg: 0 12px 40px rgba(0, 0, 0, .4), 0 4px 12px rgba(0, 0, 0, .25);
            --shadow-glow: 0 0 20px rgba(99, 138, 255, 0.1);
            --radius: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
            --radius-lg: 16px;
            --transition: .2s cubic-bezier(.4, 0, .2, 1);
            --transition-spring: .4s cubic-bezier(.34, 1.56, .64, 1);
        }

        /* ═══════════════════════════════════════════════════════════
           LIGHT THEME OVERRIDE
           ═══════════════════════════════════════════════════════════ */
        [data-theme="light"] {
            --bg: #f5f6fa;
            --bg-subtle: #eef0f5;
            --surface: #ffffff;
            --surface2: #f0f1f5;
            --surface3: #e8e9ef;
            --border: rgba(0, 0, 0, 0.08);
            --border2: rgba(0, 0, 0, 0.12);
            --border-accent: rgba(79, 111, 255, 0.2);
            --text: #1a1d26;
            --text-muted: #5c6370;
            --text-faint: #9ca3af;
            --accent: #4f6fff;
            --accent-glow: rgba(79, 111, 255, 0.12);
            --accent-bg: rgba(79, 111, 255, 0.06);
            --accent-border: rgba(79, 111, 255, 0.2);
            --green: #16a34a;
            --green-bg: rgba(22, 163, 74, 0.06);
            --green-border: rgba(22, 163, 74, 0.2);
            --green-glow: rgba(22, 163, 74, 0.1);
            --yellow: #ca8a04;
            --yellow-bg: rgba(202, 138, 4, 0.06);
            --yellow-border: rgba(202, 138, 4, 0.2);
            --blue: #2563eb;
            --blue-bg: rgba(37, 99, 235, 0.06);
            --blue-border: rgba(37, 99, 235, 0.2);
            --red: #dc2626;
            --red-bg: rgba(220, 38, 38, 0.06);
            --red-border: rgba(220, 38, 38, 0.2);
            --purple: #7c3aed;
            --purple-bg: rgba(124, 58, 237, 0.06);
            --purple-border: rgba(124, 58, 237, 0.2);
            --shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, .08), 0 2px 4px rgba(0, 0, 0, .04);
            --shadow-lg: 0 12px 40px rgba(0, 0, 0, .1), 0 4px 12px rgba(0, 0, 0, .06);
            --shadow-glow: 0 0 20px rgba(79, 111, 255, 0.06);
        }

        [data-theme="light"] .topbar {
            background: rgba(255, 255, 255, 0.85);
        }

        [data-theme="light"] .logo-icon {
            box-shadow: 0 4px 12px rgba(79, 111, 255, 0.2);
        }

        [data-theme="light"] .btn-primary {
            box-shadow: 0 2px 8px rgba(79, 111, 255, 0.2);
        }

        [data-theme="light"] .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(79, 111, 255, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 13.5px;
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* ═══════════════════════════════════════════════════════════
           SIDEBAR
           ═══════════════════════════════════════════════════════════ */
        .sidebar {
            width: 240px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 20px 18px;
            border-bottom: 1px solid var(--border);
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), #4f6fff);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(99, 138, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .logo-icon::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, .2), transparent);
            border-radius: inherit;
        }

        .logo-icon svg {
            width: 18px;
            height: 18px;
            position: relative;
            z-index: 1;
        }

        .logo-text {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, var(--text), var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-sub {
            font-size: 11px;
            color: var(--text-faint);
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 14px 10px;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--border2);
            border-radius: 4px;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-faint);
            padding: 12px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            cursor: pointer;
            transition: all var(--transition);
            font-size: 13.5px;
            font-weight: 450;
            text-decoration: none;
            margin-bottom: 2px;
            position: relative;
        }

        .nav-item:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .nav-item.active {
            background: var(--accent-bg);
            color: var(--accent);
            font-weight: 550;
            box-shadow: inset 0 0 0 1px var(--accent-border);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 18px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        .nav-item.active svg {
            color: var(--accent);
            opacity: 1;
        }

        .nav-item svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            opacity: .5;
            transition: opacity var(--transition);
        }

        .nav-item:hover svg {
            opacity: .8;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--red-bg);
            color: var(--red);
            border: 1px solid var(--red-border);
            font-size: 10px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 20px;
            font-family: 'JetBrains Mono', monospace;
        }

        .nav-badge.green {
            background: var(--green-bg);
            color: var(--green);
            border-color: var(--green-border);
        }

        .sidebar-footer {
            padding: 14px 10px;
            border-top: 1px solid var(--border);
        }

        .env-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            background: var(--surface2);
            border-radius: var(--radius-sm);
            font-size: 12px;
            border: 1px solid var(--border);
        }

        .env-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--green);
            flex-shrink: 0;
            box-shadow: 0 0 8px rgba(63, 185, 80, 0.5);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
                box-shadow: 0 0 8px rgba(63, 185, 80, 0.5);
            }

            50% {
                opacity: .6;
                box-shadow: 0 0 4px rgba(63, 185, 80, 0.3);
            }
        }

        .env-name {
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-muted);
            flex: 1;
            font-size: 11.5px;
        }

        .env-version {
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-faint);
            font-size: 11px;
        }

        /* ═══════════════════════════════════════════════════════════
           MAIN AREA
           ═══════════════════════════════════════════════════════════ */
        .main {
            margin-left: 240px;
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: rgba(21, 26, 36, 0.8);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.4px;
            flex: 1;
        }

        .topbar-meta {
            font-size: 12px;
            color: var(--text-faint);
            font-family: 'JetBrains Mono', monospace;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition);
            border: none;
            font-family: inherit;
            letter-spacing: -0.1px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #4f6fff);
            color: white;
            box-shadow: 0 2px 8px rgba(99, 138, 255, 0.25);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(99, 138, 255, 0.4);
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: var(--surface2);
            color: var(--text-muted);
            border: 1px solid var(--border2);
        }

        .btn-ghost:hover {
            background: var(--surface3);
            color: var(--text);
            border-color: var(--border2);
        }

        .btn svg {
            width: 14px;
            height: 14px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinning {
            animation: spin .8s linear infinite;
        }

        .content {
            padding: 32px;
        }

        /* ═══════════════════════════════════════════════════════════
           STATUS STRIP
           ═══════════════════════════════════════════════════════════ */
        .status-strip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            background: var(--green-bg);
            border: 1px solid var(--green-border);
            border-radius: var(--radius);
            margin-bottom: 28px;
            font-size: 13px;
            color: var(--green);
            font-weight: 500;
            backdrop-filter: blur(8px);
        }

        .status-strip.warn {
            background: var(--yellow-bg);
            border-color: var(--yellow-border);
            color: var(--yellow);
        }

        .status-strip.fail {
            background: var(--red-bg);
            border-color: var(--red-border);
            color: var(--red);
        }

        .status-strip svg {
            width: 16px;
            height: 16px;
        }

        .status-strip .sub {
            font-weight: 400;
            opacity: .7;
            margin-left: 4px;
        }

        /* ═══════════════════════════════════════════════════════════
           STAT CARDS
           ═══════════════════════════════════════════════════════════ */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 22px;
            box-shadow: var(--shadow);
            transition: all var(--transition);
            cursor: default;
            animation: fadeUp .4s ease both;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(99, 138, 255, 0.3), transparent);
            opacity: 0;
            transition: opacity var(--transition);
        }

        .stat-card:hover {
            border-color: var(--border2);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:nth-child(1) {
            animation-delay: 0s;
        }

        .stat-card:nth-child(2) {
            animation-delay: .06s;
        }

        .stat-card:nth-child(3) {
            animation-delay: .12s;
        }

        .stat-card:nth-child(4) {
            animation-delay: .18s;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-faint);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .stat-icon {
            width: 34px;
            height: 34px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon svg {
            width: 15px;
            height: 15px;
        }

        .stat-icon.blue {
            background: var(--blue-bg);
            color: var(--blue);
            border: 1px solid var(--blue-border);
        }

        .stat-icon.green {
            background: var(--green-bg);
            color: var(--green);
            border: 1px solid var(--green-border);
        }

        .stat-icon.yellow {
            background: var(--yellow-bg);
            color: var(--yellow);
            border: 1px solid var(--yellow-border);
        }

        .stat-icon.red {
            background: var(--red-bg);
            color: var(--red);
            border: 1px solid var(--red-border);
        }

        .stat-value {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -1.5px;
            line-height: 1;
            margin-bottom: 8px;
            font-family: 'Inter', sans-serif;
        }

        .stat-unit {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
            margin-left: 2px;
        }

        .stat-trend {
            font-size: 12px;
            color: var(--text-faint);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .trend-up {
            color: var(--green);
        }

        .trend-down {
            color: var(--red);
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            font-family: 'JetBrains Mono', monospace;
        }

        .tag.green {
            background: var(--green-bg);
            color: var(--green);
            border: 1px solid var(--green-border);
        }

        .tag.red {
            background: var(--red-bg);
            color: var(--red);
            border: 1px solid var(--red-border);
        }

        .tag.yellow {
            background: var(--yellow-bg);
            color: var(--yellow);
            border: 1px solid var(--yellow-border);
        }

        .tag.blue {
            background: var(--blue-bg);
            color: var(--blue);
            border: 1px solid var(--blue-border);
        }

        /* ═══════════════════════════════════════════════════════════
           MAIN GRID
           ═══════════════════════════════════════════════════════════ */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            margin-bottom: 28px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .card:hover {
            border-color: var(--border2);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 9px;
            letter-spacing: -0.2px;
        }

        .card-title svg {
            width: 16px;
            height: 16px;
            color: var(--text-faint);
        }

        .card-action {
            font-size: 12px;
            color: var(--accent);
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            transition: color var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .card-action:hover {
            color: #8ba9ff;
        }

        .card-body {
            padding: 22px;
        }

        /* ═══════════════════════════════════════════════════════════
           CHART
           ═══════════════════════════════════════════════════════════ */
        .chart-container {
            position: relative;
            height: 170px;
            margin-bottom: 8px;
        }

        .chart-svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .chart-labels {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: var(--text-faint);
            font-family: 'JetBrains Mono', monospace;
            padding: 0 2px;
        }

        /* ═══════════════════════════════════════════════════════════
           REQUESTS TABLE
           ═══════════════════════════════════════════════════════════ */
        .req-table {
            width: 100%;
            border-collapse: collapse;
        }

        .req-table th {
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            color: var(--text-faint);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 0 0 12px;
            border-bottom: 1px solid var(--border);
        }

        .req-table td {
            padding: 11px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }

        .req-table tr {
            transition: background var(--transition);
        }

        .req-table tbody tr:hover {
            background: rgba(255, 255, 255, .02);
        }

        .req-table tr:last-child td {
            border-bottom: none;
        }

        .method-badge {
            display: inline-block;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: var(--radius-xs);
            letter-spacing: 0.3px;
        }

        .method-GET {
            background: var(--blue-bg);
            color: var(--blue);
            border: 1px solid var(--blue-border);
        }

        .method-POST {
            background: var(--green-bg);
            color: var(--green);
            border: 1px solid var(--green-border);
        }

        .method-PUT {
            background: var(--yellow-bg);
            color: var(--yellow);
            border: 1px solid var(--yellow-border);
        }

        .method-DELETE {
            background: var(--red-bg);
            color: var(--red);
            border: 1px solid var(--red-border);
        }

        .method-PATCH {
            background: var(--purple-bg);
            color: var(--purple);
            border: 1px solid var(--purple-border);
        }

        .status-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 600;
        }

        .status-2xx {
            color: var(--green);
        }

        .status-4xx {
            color: var(--yellow);
        }

        .status-5xx {
            color: var(--red);
        }

        .duration {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-muted);
            text-align: right;
        }

        .route-path {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text);
            max-width: 260px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ═══════════════════════════════════════════════════════════
           HEALTH CHECKS
           ═══════════════════════════════════════════════════════════ */
        .health-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px 20px;
        }

        .health-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 16px;
            background: var(--surface2);
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            transition: all var(--transition);
        }

        .health-item:hover {
            background: var(--surface3);
            border-color: var(--border2);
        }

        .health-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .health-dot.ok {
            background: var(--green);
            box-shadow: 0 0 8px rgba(63, 185, 80, 0.4);
        }

        .health-dot.warn {
            background: var(--yellow);
            box-shadow: 0 0 8px rgba(210, 153, 34, 0.4);
        }

        .health-dot.fail {
            background: var(--red);
            box-shadow: 0 0 8px rgba(248, 81, 73, 0.4);
            animation: blink .8s steps(1) infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }

        .health-name {
            flex: 1;
            font-size: 13px;
            font-weight: 500;
        }

        .health-detail {
            font-size: 12px;
            color: var(--text-faint);
            font-family: 'JetBrains Mono', monospace;
        }

        .health-status {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .health-status.ok {
            color: var(--green);
        }

        .health-status.warn {
            color: var(--yellow);
        }

        .health-status.fail {
            color: var(--red);
        }

        /* ═══════════════════════════════════════════════════════════
           BOTTOM GRID
           ═══════════════════════════════════════════════════════════ */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        /* ═══════════════════════════════════════════════════════════
           QUEUE JOBS
           ═══════════════════════════════════════════════════════════ */
        .queue-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
            transition: background var(--transition);
        }

        .queue-item:last-child {
            border-bottom: none;
        }

        .queue-name {
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .queue-time {
            font-size: 11px;
            color: var(--text-faint);
            font-family: 'JetBrains Mono', monospace;
        }

        .queue-status {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 20px;
            flex-shrink: 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .queue-status.done {
            background: var(--green-bg);
            color: var(--green);
            border: 1px solid var(--green-border);
        }

        .queue-status.running {
            background: var(--blue-bg);
            color: var(--blue);
            border: 1px solid var(--blue-border);
        }

        .queue-status.failed {
            background: var(--red-bg);
            color: var(--red);
            border: 1px solid var(--red-border);
        }

        .queue-status.pending {
            background: var(--yellow-bg);
            color: var(--yellow);
            border: 1px solid var(--yellow-border);
        }

        /* ═══════════════════════════════════════════════════════════
           EXCEPTIONS
           ═══════════════════════════════════════════════════════════ */
        .exception-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: all var(--transition);
        }

        .exception-item:last-child {
            border-bottom: none;
        }

        .exception-item:hover .exception-class {
            color: var(--accent);
        }

        .exception-class {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 500;
            color: var(--text);
            transition: color var(--transition);
        }

        .exception-msg {
            font-size: 12px;
            color: var(--text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .exception-meta {
            display: flex;
            gap: 12px;
            font-size: 11px;
            color: var(--text-faint);
            font-family: 'JetBrains Mono', monospace;
            align-items: center;
        }

        .exception-count {
            color: var(--red);
            font-weight: 600;
        }

        .exception-time {
            color: var(--text-faint);
        }

        /* ═══════════════════════════════════════════════════════════
           CACHE STATS
           ═══════════════════════════════════════════════════════════ */
        .cache-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid var(--border);
        }

        .cache-row:last-child {
            border-bottom: none;
        }

        .cache-label {
            font-size: 13px;
            color: var(--text-muted);
            flex: 1;
        }

        .cache-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .progress-bar {
            width: 60px;
            height: 5px;
            background: var(--surface2);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width .6s ease;
        }

        .progress-fill.green {
            background: var(--green);
            box-shadow: 0 0 6px rgba(63, 185, 80, 0.3);
        }

        .progress-fill.yellow {
            background: var(--yellow);
            box-shadow: 0 0 6px rgba(210, 153, 34, 0.3);
        }

        .progress-fill.red {
            background: var(--red);
            box-shadow: 0 0 6px rgba(248, 81, 73, 0.3);
        }

        /* ═══════════════════════════════════════════════════════════
           PAGINATION
           ═══════════════════════════════════════════════════════════ */
        .pagination-wrap {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }

        .pagination-wrap nav {
            display: flex;
            gap: 4px;
        }

        .pagination-wrap nav>div {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .pagination-wrap nav span,
        .pagination-wrap nav a,
        .pagination-wrap nav button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 500;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-muted);
            text-decoration: none;
            transition: all var(--transition);
            font-family: 'JetBrains Mono', monospace;
        }

        .pagination-wrap nav span[aria-current="page"] span {
            background: linear-gradient(135deg, var(--accent), #4f6fff);
            color: white;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(99, 138, 255, 0.25);
        }

        .pagination-wrap nav a:hover {
            background: var(--surface2);
            color: var(--text);
            border-color: var(--border2);
        }

        /* ═══════════════════════════════════════════════════════════
           RESPONSIVE
           ═══════════════════════════════════════════════════════════ */
        @media (max-width: 1200px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

            .content {
                padding: 20px 16px;
            }
        }

        /* ═══════════════════════════════════════════════════════════
           UTILITIES & ANIMATIONS
           ═══════════════════════════════════════════════════════════ */
        .glass {
            background: rgba(21, 26, 36, 0.6);
            backdrop-filter: blur(16px) saturate(200%);
            -webkit-backdrop-filter: blur(16px) saturate(200%);
            border: 1px solid var(--border);
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .skeleton {
            background: linear-gradient(90deg, var(--surface2), var(--surface3), var(--surface2));
            background-size: 200% 100%;
            animation: shimmer 1.5s ease-in-out infinite;
            border-radius: var(--radius-sm);
        }

        /* Livewire loading states */
        [wire\:loading] {
            opacity: .6;
            pointer-events: none;
            transition: opacity .1s;
        }

        [wire\:loading\.class] {
            transition: all .2s;
        }
    </style>
    @livewireStyles
</head>

<body>
    <div class="layout">
        @include('larawatch::monitor.components.sidebar')

        <div class="main">
            @include('larawatch::monitor.components.topbar', [
                'title' => $title ?? 'Dashboard',
                'hasLiveMode' => $hasLiveMode ?? false,
            ])

            <div class="content">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{ $scripts ?? '' }}
    @livewireScripts
</body>

</html>
