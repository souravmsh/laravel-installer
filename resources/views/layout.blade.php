<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Installer') - {{ config('laravel_installer.app_name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        indigo: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'entrance': 'entrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        entrance: {
                            '0%': { opacity: '0', transform: 'translateY(20px) scale(0.95)' },
                            '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style type="text/tailwindcss">
        @layer utilities {
            .glass {
                @apply bg-white/70 backdrop-blur-2xl border border-white/40 shadow-[0_20px_50px_rgba(0,0,0,0.1)];
            }
            .btn-premium {
                @apply relative overflow-hidden transition-all duration-300 active:scale-95;
                background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            }
            .btn-premium::after {
                content: '';
                @apply absolute inset-0 opacity-0 transition-opacity duration-300 bg-white/10;
            }
            .btn-premium:hover::after {
                @apply opacity-100;
            }
        }

        body {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(243, 75%, 90%, 1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(271, 91%, 95%, 1) 0, transparent 50%), 
                radial-gradient(at 50% 100%, hsla(160, 84%, 93%, 1) 0, transparent 50%);
            background-attachment: fixed;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-indigo-100 italic-none">
    <!-- Animated Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10 blur-3xl opacity-60">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-200 rounded-full animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-96 h-96 bg-purple-200 rounded-full animate-blob animation-delay-2000" style="animation-delay: 2s"></div>
        <div class="absolute -bottom-24 left-1/4 w-96 h-96 bg-emerald-100 rounded-full animate-blob animation-delay-4000" style="animation-delay: 4s"></div>
    </div>

    <div class="w-full max-w-[480px] animate-entrance">
        <div class="glass rounded-[40px] overflow-hidden relative">
            <!-- Sleek Top Progress -->
            @php
                $progress = 20;
                if(request()->routeIs('installer.database')) $progress = 40;
                elseif(request()->routeIs('installer.license')) $progress = 60;
                elseif(request()->routeIs('installer.install')) $progress = 80;
                elseif(request()->routeIs('installer.complete')) $progress = 100;
            @endphp
            <div class="absolute top-0 left-0 w-full h-1 bg-black/5 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-700 ease-in-out" style="width: {{ $progress }}%"></div>
            </div>

            <div class="pt-12 px-10 pb-4 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-xl shadow-indigo-200 mb-6 text-white text-4xl relative group">
                    <i class="bi bi-rocket-takeoff group-hover:scale-110 transition-transform duration-500"></i>
                    <div class="absolute inset-0 rounded-inherit bg-inherit filter blur-xl opacity-40 -z-10 scale-90 translate-y-3"></div>
                </div>
                <h2 class="text-3xl font-[800] tracking-tight text-slate-900 mb-1">
                    {{ config('laravel_installer.app_name', 'Laravel') }}
                </h2>
                <p class="text-slate-500 font-medium text-sm">Setup Wizard</p>
            </div>
            
            <div class="px-10 pb-12">
                @yield('content')
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-slate-400 text-[10px] font-bold tracking-[0.2em] flex items-center justify-center gap-2 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                <span class="w-8 h-[1px] bg-slate-200"></span>
                SECURE INSTALLATION
                <span class="w-8 h-[1px] bg-slate-200"></span>
            </p>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
