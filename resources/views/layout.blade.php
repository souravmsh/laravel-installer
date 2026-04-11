<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Installer') - {{ config('laravel_installer.app_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .installer-container {
            max-width: 700px;
            width: 100%;
            margin: 20px;
        }
        .installer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .installer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .installer-body {
            padding: 40px;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .step::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e0e0e0;
            z-index: -1;
        }
        .step:last-child::after {
            display: none;
        }
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .step.active .step-number {
            background: #667eea;
            color: white;
        }
        .step.completed .step-number {
            background: #28a745;
            color: white;
        }
        .requirement-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .requirement-item:last-child {
            border-bottom: none;
        }
        .btn-installer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
        }
        .btn-installer:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="installer-container">
        <div class="installer-card">
            <div class="installer-header">
                <h2 class="mb-0">
                    <i class="bi bi-building"></i>
                    {{ config('laravel_installer.app_name') }}
                </h2>
                <p class="mb-0 mt-2">Installation Wizard</p>
            </div>
            <div class="installer-body">
                <div class="step-indicator">
                    <div class="step {{ Route::is('installer.requirements') ? 'active' : (Route::is('installer.database') || Route::is('installer.license') || Route::is('installer.install') || Route::is('installer.complete') ? 'completed' : '') }}">
                        <div class="step-number">
                            @if(Route::is('installer.database') || Route::is('installer.license') || Route::is('installer.install') || Route::is('installer.complete'))
                                <i class="bi bi-check"></i>
                            @else
                                1
                            @endif
                        </div>
                        <small>Requirements</small>
                    </div>
                    <div class="step {{ Route::is('installer.database') ? 'active' : (Route::is('installer.license') || Route::is('installer.install') || Route::is('installer.complete') ? 'completed' : '') }}">
                        <div class="step-number">
                            @if(Route::is('installer.license') || Route::is('installer.install') || Route::is('installer.complete'))
                                <i class="bi bi-check"></i>
                            @else
                                2
                            @endif
                        </div>
                        <small>Database</small>
                    </div>
                    
                    @if(config('laravel_installer.license_check', true))
                    <div class="step {{ Route::is('installer.license') ? 'active' : (Route::is('installer.install') || Route::is('installer.complete') ? 'completed' : '') }}">
                        <div class="step-number">
                            @if(Route::is('installer.install') || Route::is('installer.complete'))
                                <i class="bi bi-check"></i>
                            @else
                                3
                            @endif
                        </div>
                        <small>License</small>
                    </div>
                    @endif

                    <div class="step {{ Route::is('installer.install') ? 'active' : (Route::is('installer.complete') ? 'completed' : '') }}">
                        <div class="step-number">
                            @if(Route::is('installer.complete'))
                                <i class="bi bi-check"></i>
                            @else
                                @if(config('laravel_installer.license_check', true)) 4 @else 3 @endif
                            @endif
                        </div>
                        <small>Install</small>
                    </div>
                </div>
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
