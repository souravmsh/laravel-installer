@extends('installer::layout')

@section('title', 'Welcome')

@section('content')

<div class="text-center mb-8">
    <h4 class="text-xl font-bold text-slate-900">System Requirements</h4>
    <p class="text-slate-500 text-sm">Validating your environment for installation.</p>
</div>

<div class="space-y-3 mb-8">
    @foreach($requirements as $key => $requirement)
    <div class="flex items-center justify-between bg-white/40 border border-white/20 p-4 rounded-2xl hover:bg-white/60 hover:translate-x-1 transition-all duration-300">
        <div class="flex flex-col">
            <span class="text-sm font-semibold text-slate-700">{{ $requirement['name'] }}</span>
            @if(isset($requirement['current']))
                <span class="text-[10px] text-slate-400 font-bold tracking-wider uppercase -mt-0.5">Current: {{ $requirement['current'] }}</span>
            @endif
        </div>
        <div class="flex items-center">
            @if($requirement['status'])
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                    <i class="bi bi-check-lg"></i>
                </div>
            @else
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shadow-sm shadow-rose-100/50">
                    <i class="bi bi-x-lg"></i>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

@php
    $allPassed = collect($requirements)->every(fn($req) => $req['status']);
@endphp

@if($allPassed)
    <div class="bg-emerald-50/50 border border-emerald-100/50 p-4 rounded-2xl flex items-center gap-3 mb-6 transition-all animate-pulse">
        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
        <p class="text-sm font-medium text-emerald-800">Your system is ready for installation.</p>
    </div>
    
    <a href="{{ route('installer.database') }}" class="btn-premium w-full py-4 rounded-2xl text-white font-bold flex items-center justify-center gap-2 shadow-lg shadow-indigo-200">
        Get Started
        <i class="bi bi-arrow-right leading-none"></i>
    </a>
@else
    <div class="bg-rose-50/50 border border-rose-100/50 p-4 rounded-2xl flex items-center gap-3 mb-6">
        <div class="w-2 h-2 rounded-full bg-rose-500"></div>
        <p class="text-sm font-medium text-rose-800">Please resolve the requirements above to continue.</p>
    </div>
    <button disabled class="w-full py-4 rounded-2xl bg-slate-100 text-slate-400 font-bold cursor-not-allowed">
        Check Requirements
    </button>
@endif

@endsection
