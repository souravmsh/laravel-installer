<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Setup') — {{ config('laravel_installer.app_name', 'System Setup') }}</title>

    <link href="{{ url('installer-assets/fonts/Inter.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ url('installer-assets/icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ url('installer-assets/css/bootstrap.min.css') }}">

    @stack('styles')

    <style>
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url('{{ url('installer-assets/fonts/Inter.woff2') }}') format('woff2');
        }

        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --sidebar-w: 220px;
            --accent: #6366f1;
            --accent-dark: #4f46e5;
            --accent-glow: rgba(99,102,241,.18);
            --sidebar-bg: #0f1117;
            --sidebar-text: #9ca3b0;
            --sidebar-active: #e8e9ff;
            --done-color: #34d399;
            --panel-bg: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --radius: 14px;
            --input-radius: 8px;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f0fdf4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }

        /* ── Shell ─────────────────────────────────────── */
        .installer-shell {
            display: flex;
            width: 100%;
            max-width: 860px;
            min-height: 520px;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0,0,0,.15), 0 4px 16px rgba(0,0,0,.08);
        }

        /* ── Sidebar ───────────────────────────────────── */
        .installer-sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 1.25rem;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 30px;
            height: 30px;
            background: var(--accent);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            color: #fff;
            flex-shrink: 0;
        }

        .brand-name {
            font-size: .8rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            letter-spacing: -.01em;
        }

        /* Steps */
        .step-list { list-style: none; padding: 0; margin: 0; flex: 1; }

        .step-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .55rem .6rem;
            border-radius: 8px;
            margin-bottom: .15rem;
            cursor: default;
            transition: background .15s;
        }

        .step-item.is-active  { background: rgba(255,255,255,.06); }

        /* Connector line */
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: calc(.6rem + 11px);
            top: calc(100% + .15rem);
            height: .15rem + .35rem;
            width: 2px;
            background: rgba(255,255,255,.08);
            height: 100%;
            top: 100%;
            height: .3rem;
        }

        .step-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: .65rem;
            transition: all .2s;
            background: transparent;
        }

        .step-item.is-done .step-dot {
            background: var(--done-color);
            border-color: var(--done-color);
            color: #fff;
        }

        .step-item.is-active .step-dot {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-glow);
        }

        .step-inner-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
            display: none;
        }

        .step-item.is-active .step-inner-dot { display: block; }

        .step-label {
            font-size: .73rem;
            font-weight: 500;
            color: var(--sidebar-text);
            transition: color .2s;
        }

        .step-item.is-active .step-label  { color: var(--sidebar-active); }
        .step-item.is-done  .step-label  { color: rgba(255,255,255,.5); }

        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,.07);
            padding-top: .85rem;
            font-size: .67rem;
            color: rgba(255,255,255,.2);
        }

        /* ── Content panel ─────────────────────────────── */
        .installer-content {
            flex: 1;
            background: var(--panel-bg);
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .content-header {
            padding: 1.1rem 1.5rem .9rem;
            border-bottom: 1px solid var(--border);
        }

        .content-header h4 {
            font-size: .95rem;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 .15rem;
        }

        .content-header p {
            font-size: .72rem;
            color: var(--muted);
            margin: 0;
        }

        .content-body {
            flex: 1;
            padding: 1.25rem 1.5rem;
            overflow-y: auto;
        }

        .content-footer {
            padding: .85rem 1.5rem;
            border-top: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ── Inputs ────────────────────────────────────── */
        .form-label {
            font-size: .7rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: .3rem;
        }

        .form-control {
            font-size: .825rem;
            padding: .45rem .7rem;
            border-radius: var(--input-radius);
            border: 1.5px solid var(--border);
            color: var(--text);
            transition: border-color .15s, box-shadow .15s;
            height: auto;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            outline: none;
        }

        .form-text { font-size: .68rem; color: var(--muted); }

        /* ── Buttons ───────────────────────────────────── */
        .btn-primary-custom {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .45rem 1.2rem;
            background: var(--accent);
            color: #fff;
            font-size: .78rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
            text-decoration: none;
        }

        .btn-primary-custom:hover {
            background: var(--accent-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(99,102,241,.35);
        }

        .btn-primary-custom:active { transform: none; }
        .btn-primary-custom:disabled { opacity: .55; cursor: not-allowed; transform: none; box-shadow: none; }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .45rem .9rem;
            background: transparent;
            color: var(--muted);
            font-size: .78rem;
            font-weight: 500;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }

        .btn-ghost:hover { border-color: #9ca3af; color: var(--text); background: #f9fafb; }

        /* ── Status pills ──────────────────────────────── */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .67rem;
            font-weight: 600;
            padding: .2rem .55rem;
            border-radius: 99px;
        }

        .pill-ok  { background: #ecfdf5; color: #059669; }
        .pill-bad { background: #fef2f2; color: #dc2626; }

        /* ── Check table ───────────────────────────────── */
        .check-table { width: 100%; border-collapse: collapse; font-size: .78rem; }
        .check-table th {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--muted);
            font-weight: 600;
            padding: .4rem .6rem;
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
        }

        .check-table td {
            padding: .45rem .6rem;
            border-bottom: 1px solid #f3f4f6;
            color: var(--text);
            vertical-align: middle;
        }

        .check-table tr:last-child td { border-bottom: none; }
        .check-table tr:hover td { background: #fafafa; }

        .group-heading {
            font-size: .68rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: .7rem .6rem .3rem;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        /* ── Alert ─────────────────────────────────────── */
        .installer-alert {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            padding: .7rem .9rem;
            border-radius: 8px;
            font-size: .76rem;
            margin-top: .9rem;
        }

        .installer-alert.danger  { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .installer-alert.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .installer-alert.warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

        .installer-alert i { margin-top: .05rem; flex-shrink: 0; }

        /* ── Terminal ──────────────────────────────────── */
        .terminal {
            background: #0d1117;
            border-radius: 8px;
            padding: .7rem .9rem;
            font-family: 'Fira Mono', 'Consolas', monospace;
            font-size: .7rem;
            color: #8b949e;
            height: 130px;
            overflow-y: auto;
            line-height: 1.7;
        }

        .terminal .t-ok   { color: #3fb950; }
        .terminal .t-info { color: #58a6ff; }
        .terminal .t-err  { color: #f85149; }
        .terminal .t-dim  { color: #484f58; }

        /* ── Progress ──────────────────────────────────── */
        .progress-track {
            height: 5px;
            background: #e5e7eb;
            border-radius: 99px;
            overflow: hidden;
            margin: .5rem 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), #a78bfa);
            border-radius: 99px;
            transition: width .5s ease;
        }

        /* ── Complete icon ─────────────────────────────── */
        .complete-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
        }

        .complete-icon.success { background: #d1fae5; color: #059669; }
        .complete-icon.error   { background: #fee2e2; color: #dc2626; }

        .cred-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .45rem .75rem;
            border-radius: 6px;
            background: #f9fafb;
            margin-bottom: .4rem;
            font-size: .78rem;
        }

        .cred-row .cred-label { color: var(--muted); font-size: .7rem; }
        .cred-row .cred-value { font-weight: 600; color: var(--text); font-family: monospace; }

        .copy-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted);
            padding: 0 .2rem;
            transition: color .15s;
            font-size: .8rem;
        }

        .copy-btn:hover { color: var(--accent); }

        /* ── Form check ────────────────────────────────── */
        .custom-check {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            padding: .7rem .9rem;
            background: #f9fafb;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: border-color .15s;
        }

        .custom-check:hover { border-color: var(--accent); }

        .custom-check input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--accent);
            flex-shrink: 0;
            margin-top: .15rem;
            cursor: pointer;
        }

        .custom-check-text { font-size: .76rem; color: var(--text); }
        .custom-check-hint { font-size: .68rem; color: var(--muted); margin-top: .15rem; }

        @media (max-width: 640px) {
            .installer-sidebar { display: none; }
            .installer-shell { max-width: 100%; min-height: auto; }
            body { padding: .75rem; }
        }
    </style>
</head>
<body>

@php
    $steps = [
        ['route' => 'installer.welcome',  'name' => 'Requirements', 'icon' => 'bi-cpu'],
        ['route' => 'installer.database', 'name' => 'Database',     'icon' => 'bi-database'],
    ];

    if (config('laravel_installer.license_check', 'required') !== 'disabled') {
        $steps[] = ['route' => 'installer.license', 'name' => 'License', 'icon' => 'bi-key'];
    }

    $steps[] = ['route' => 'installer.install',  'name' => 'Install',   'icon' => 'bi-lightning'];
    $steps[] = ['route' => 'installer.complete',  'name' => 'Complete',  'icon' => 'bi-check-circle'];

    $currentStepIndex = 0;
    foreach ($steps as $i => $step) {
        if (request()->routeIs($step['route'])) {
            $currentStepIndex = $i;
        }
    }
@endphp

<div class="installer-shell">

    {{-- Sidebar --}}
    <aside class="installer-sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="brand-name">{{ config('laravel_installer.app_name', 'Setup Wizard') }}</div>
        </div>

        <ul class="step-list">
            @foreach($steps as $i => $step)
                @php
                    $isDone   = $i < $currentStepIndex;
                    $isActive = $i === $currentStepIndex;
                @endphp
                <li class="step-item {{ $isActive ? 'is-active' : ($isDone ? 'is-done' : '') }}">
                    <div class="step-dot">
                        @if($isDone)
                            <i class="bi bi-check2" style="font-size:.7rem;color:#fff;"></i>
                        @else
                            <div class="step-inner-dot"></div>
                        @endif
                    </div>
                    <span class="step-label">{{ $step['name'] }}</span>
                </li>
            @endforeach
        </ul>

        <div class="sidebar-footer">
            <div>Installation Wizard v{{ config('laravel_installer.app_version', '1.0.0') }}</div>
            <div style="margin-top:4px;font-size:.64rem;opacity:.75;">
                Powered by
                <a href="https://codekernel.net" target="_blank" style="color:#9ca3b0;text-decoration:none;">
                    CodeKernel
                </a>
            </div>
        </div>                        
    </aside>

    {{-- Content --}}
    <main class="installer-content">
        <div class="content-header">
            <h4>@yield('title')</h4>
            <p>@yield('subtitle', 'Follow the steps to configure your system.')</p>
        </div>

        <div class="content-body">
            @yield('content')
        </div>

        @hasSection('footer')
        <div class="content-footer">
            @yield('footer')
        </div>
        @endif
    </main>

</div>

<script src="{{ url('installer-assets/js/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
