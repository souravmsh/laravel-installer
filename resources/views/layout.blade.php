<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Installer') - {{ config('laravel_installer.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }

        body {
            background: radial-gradient(circle at top left, #eef2ff 0%, #f5f3ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            margin: 0;
            padding: 20px;
        }

        .installer-container {
            max-width: 420px;
            width: 100%;
        }

        .installer-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .installer-header {
            padding: 40px 30px 20px;
            text-align: center;
        }

        .app-icon {
            width: 64px;
            height: 64px;
            background: var(--primary-gradient);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 28px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
        }

        .installer-body {
            padding: 0 30px 40px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 35px;
        }

        .step-dot {
            height: 6px;
            width: 24px;
            border-radius: 3px;
            background: #e5e7eb;
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: #6366f1;
            width: 40px;
        }

        .step-dot.completed {
            background: #10b981;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.5);
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            border-color: #6366f1;
        }

        .btn-installer {
            background: var(--primary-gradient);
            border: none;
            padding: 14px 24px;
            border-radius: 16px;
            color: white;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .btn-installer:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
            opacity: 0.95;
            color: white;
        }

        .btn-outline-secondary {
            border-radius: 16px;
            padding: 14px 24px;
            border: 1px solid #e5e7eb;
            color: var(--text-muted);
            font-weight: 500;
        }

        .alert {
            border-radius: 16px;
            border: none;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.4);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 8px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="installer-container">
        <div class="installer-card">
            <div class="installer-header">
                <div class="app-icon">
                    <i class="bi bi-rocket-takeoff"></i>
                </div>
                <h3 class="fw-bold mb-1">{{ config('laravel_installer.app_name') }}</h3>
                <p class="text-muted small">Setup Wizard</p>
            </div>
            <div class="installer-body"><div class="step-indicator">
                    <div class="step-dot {{ request()->routeIs('installer.requirements') ? 'active' : (request()->routeIs('installer.database') || request()->routeIs('installer.license') || request()->routeIs('installer.install') || request()->routeIs('installer.complete') ? 'completed' : '') }}"></div>
                    <div class="step-dot {{ request()->routeIs('installer.database') ? 'active' : (request()->routeIs('installer.license') || request()->routeIs('installer.install') || request()->routeIs('installer.complete') ? 'completed' : '') }}"></div>
                    @if(config('laravel_installer.license_check', 'required') !== 'disabled')
                        <div class="step-dot {{ request()->routeIs('installer.license') ? 'active' : (request()->routeIs('installer.install') || request()->routeIs('installer.complete') ? 'completed' : '') }}"></div>
                    @endif
                    <div class="step-dot {{ request()->routeIs('installer.install') ? 'active' : (request()->routeIs('installer.complete') ? 'completed' : '') }}"></div>
                    <div class="step-dot {{ request()->routeIs('installer.complete') ? 'active' : '' }}"></div>
                </div>
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
