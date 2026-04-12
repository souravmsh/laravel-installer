<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Installer') - {{ config('laravel_installer.app_name', 'System Setup') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    
    <style type="text/tailwindcss">
        @layer utilities {
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        }
        
        /* Standard generic form styling */
        .form-input {
            @apply w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-colors;
        }
        .form-label {
            @apply block text-sm font-medium text-slate-700 mb-1;
        }
        .btn-primary {
            @apply inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed;
        }
        .btn-secondary {
            @apply inline-flex justify-center items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md shadow-sm text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4 font-sans text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-700">
    
    @php
        $steps = [
            ['route' => 'installer.requirements', 'name' => 'Requirements'],
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
    <div class="w-full max-w-4xl bg-white shadow-xl rounded-lg border border-slate-200 flex flex-col md:flex-row overflow-hidden min-h-[550px]">
        
        <!-- Sidebar (Steps Track) -->
        <div class="w-full md:w-64 bg-slate-50 border-r border-slate-200 p-6 flex flex-col flex-shrink-0">
            <div class="mb-8 flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-brand-600 text-white flex items-center justify-center shrink-0">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <h1 class="text-sm font-bold text-slate-900 leading-tight">
                    {{ config('laravel_installer.app_name', 'System Setup') }}
                </h1>
            </div>

            <div class="flex-1">
                <nav aria-label="Progress">
                    <ol role="list" class="overflow-hidden">
                        @foreach($steps as $index => $step)
                            @php
                                $isCompleted = $index < $currentStepIndex;
                                $isActive = $index === $currentStepIndex;
                                $isLast = $index === count($steps) - 1;
                            @endphp
                            
                            <li class="relative {{ !$isLast ? 'pb-8' : '' }}">
                                @if(!$isLast)
                                    <div class="absolute left-3 top-4 -ml-px mt-0.5 h-full w-0.5 {{ $isCompleted ? 'bg-brand-600' : 'bg-slate-200' }}" aria-hidden="true"></div>
                                @endif

                                <div class="relative flex items-center group">
                                    <span class="h-9 flex items-center">
                                        @if($isCompleted)
                                            <span class="relative z-10 w-6 h-6 flex items-center justify-center bg-brand-600 rounded-full">
                                                <i class="bi bi-check text-white text-xs"></i>
                                            </span>
                                        @elseif($isActive)
                                            <span class="relative z-10 w-6 h-6 flex items-center justify-center bg-white border-2 border-brand-600 rounded-full">
                                                <span class="h-2 w-2 bg-brand-600 rounded-full"></span>
                                            </span>
                                        @else
                                            <span class="relative z-10 w-6 h-6 flex items-center justify-center bg-white border-2 border-slate-300 rounded-full"></span>
                                        @endif
                                    </span>
                                    <span class="ml-3 flex flex-col min-w-0">
                                        <span class="text-sm font-medium {{ $isActive ? 'text-brand-600' : ($isCompleted ? 'text-slate-900' : 'text-slate-500') }}">{{ $step['name'] }}</span>
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </div>
            
            <div class="mt-8 pt-4 border-t border-slate-200">
                <p class="text-xs text-slate-400">Setup Wizard v1.0</p>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-white">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-slate-100 flex-shrink-0">
                <h2 class="text-xl font-semibold text-slate-800">@yield('title')</h2>
                <p class="text-sm text-slate-500 mt-1">@yield('subtitle', 'Follow the steps to configure your system.')</p>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 p-8 overflow-y-auto scrollbar-hide">
                @yield('content')
            </div>

            <!-- Footer Action Bar -->
            @hasSection('footer')
                <div class="px-8 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between flex-shrink-0">
                    @yield('footer')
                </div>
            @endif
        </div>
    </div>

    @stack('scripts')
</body>
</html>
