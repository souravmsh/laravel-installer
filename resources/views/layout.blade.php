<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Installer') - {{ config('laravel_installer.app_name', 'System Setup') }}</title>
    
    <!-- Fonts -->
    <link href="{{ url('installer-assets/fonts/Inter.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face {
          font-family: 'Inter';
          font-style: normal;
          font-weight: 100 900;
          font-display: swap;
          src: url('{{ url('installer-assets/fonts/Inter.woff2') }}') format('woff2');
        }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    </style>
    
    <!-- Icons -->
    <link rel="stylesheet" href="{{ url('installer-assets/icons/bootstrap-icons.css') }}">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ url('installer-assets/css/bootstrap.min.css') }}">
    
    @stack('styles')
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4">
    
    @php
        $steps = [
            ['route' => 'installer.welcome', 'name' => 'Requirements'],
            ['route' => 'installer.database', 'name' => 'Database'],
        ];

        if(config('laravel_installer.license_check', 'required') !== 'disabled') {
            $steps[] = ['route' => 'installer.license', 'name' => 'License'];
        }

        $steps[] = ['route' => 'installer.install', 'name' => 'Installation'];
        $steps[] = ['route' => 'installer.complete', 'name' => 'Complete'];

        $currentStepIndex = 0;
        foreach($steps as $index => $step) {
            if(request()->routeIs($step['route']) || (request()->route()->getName() == '' && $index == 0)) {
                $currentStepIndex = $index;
            }
        }
    @endphp

    <!-- Wizard Container -->
    <div class="card shadow-lg border-0 overflow-hidden" style="max-width: 900px; width: 100%; min-height: 550px;">
        <div class="row g-0 h-100">
            <!-- Sidebar (Steps Track) -->
            <div class="col-md-4 bg-light border-end p-4 d-flex flex-column h-100">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">
                        {{ config('laravel_installer.app_name', 'System Setup') }}
                    </h5>
                </div>

                <div class="flex-grow-1 mt-3">
                    <ul class="list-unstyled position-relative">
                        @foreach($steps as $index => $step)
                            @php
                                $isCompleted = $index < $currentStepIndex;
                                $isActive = $index === $currentStepIndex;
                                $isLast = $index === count($steps) - 1;
                            @endphp
                            
                            <li class="position-relative {{ !$isLast ? 'pb-4' : '' }}">
                                @if(!$isLast)
                                    <div class="position-absolute" style="left: 11px; top: 24px; bottom: 0; width: 2px; background-color: {{ $isCompleted ? '#0d6efd' : '#dee2e6' }};"></div>
                                @endif

                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-relative" style="z-index: 1; width: 24px; height: 24px; border: 2px solid {{ $isActive || $isCompleted ? '#0d6efd' : '#dee2e6' }}; {{ $isCompleted ? 'background-color: #0d6efd !important;' : '' }}">
                                        @if($isCompleted)
                                            <i class="bi bi-check text-white" style="font-size: 14px;"></i>
                                        @elseif($isActive)
                                            <div class="rounded-circle bg-primary" style="width: 8px; height: 8px;"></div>
                                        @endif
                                    </div>
                                    <span class="ms-3 fw-medium {{ $isActive ? 'text-primary' : ($isCompleted ? 'text-dark' : 'text-muted') }}">{{ $step['name'] }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted">Setup Wizard v1.3.0</small>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-md-8 d-flex flex-column h-100 bg-white">
                <!-- Header -->
                <div class="px-4 py-3 border-bottom">
                    <h4 class="mb-1 fw-semibold">@yield('title')</h4>
                    <p class="text-muted small mb-0">@yield('subtitle', 'Follow the steps to configure your system.')</p>
                </div>

                <!-- Scrollable Content -->
                <div class="flex-grow-1 p-4" style="overflow-y: auto;">
                    @yield('content')
                </div>

                <!-- Footer Action Bar -->
                @hasSection('footer')
                    <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center">
                        @yield('footer')
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="{{ url('installer-assets/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
