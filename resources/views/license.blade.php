@extends('installer::layout')

@section('title', 'License Validation')

@section('content')

<div class="text-center mb-8">
    <h4 class="text-xl font-bold text-slate-900">License Verification</h4>
    <p class="text-slate-500 text-sm">Enter your purchase details to proceed.</p>
</div>

@if($errors->any())
    <div class="bg-rose-50/50 border border-rose-100/50 p-4 rounded-2xl mb-6">
        <ul class="text-xs font-semibold text-rose-700 space-y-1">
            @foreach($errors->all() as $error)
                <li class="flex items-center gap-2">
                    <span class="w-1 h-1 rounded-full bg-rose-400"></span>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="bg-emerald-50/50 border border-emerald-100/50 p-4 rounded-2xl mb-6 flex items-center gap-2">
        <i class="bi bi-check-circle-fill text-emerald-500 text-sm"></i>
        <p class="text-xs font-bold text-emerald-800">{{ session('success') }}</p>
    </div>
@endif

<form action="{{ route('installer.license.save') }}" method="POST" class="space-y-5">
    @csrf
    
    <div class="space-y-1.5">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <i class="bi bi-person text-sm"></i>
            </div>
            <input type="text" name="name" class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 text-sm" value="{{ old('name') }}" required placeholder="Your Name">
        </div>
    </div>

    <div class="space-y-1.5">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <i class="bi bi-envelope text-sm"></i>
            </div>
            <input type="email" name="email" class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 text-sm" value="{{ old('email') }}" required placeholder="email@example.com">
        </div>
    </div>

    <div class="space-y-1.5">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">License Key</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <i class="bi bi-hash text-sm"></i>
            </div>
            <input type="text" name="license_key" class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 text-sm italic" value="{{ old('license_key') }}" required placeholder="XXXX-XXXX-XXXX-XXXX">
        </div>
    </div>

    <div class="bg-indigo-50/50 border border-indigo-100/50 p-4 rounded-2xl flex gap-3 mb-6">
        <i class="bi bi-info-circle-fill text-indigo-500 text-sm mt-0.5"></i>
        <p class="text-[11px] font-semibold text-indigo-800 leading-relaxed uppercase tracking-wider">Your license will be validated with our central server over a secure connection.</p>
    </div>

    <div class="space-y-4 pt-2">
        <button type="submit" class="btn-premium w-full py-4 rounded-2xl text-white font-bold flex items-center justify-center gap-2 shadow-lg shadow-indigo-200 group">
            <span>Validate & Continue</span>
            <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
        </button>
        
        <div class="flex flex-col gap-3">
            @if(config('laravel_installer.license_check', 'required') === 'optional')
                <a href="{{ route('installer.install') }}" class="text-center text-[10px] font-bold text-slate-400 hover:text-indigo-500 uppercase tracking-widest transition-colors py-1">
                    Skip Registration for Now
                </a>
            @endif
            
            <a href="{{ route('installer.database') }}" class="text-center text-[10px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors flex items-center justify-center gap-2 py-1">
                <i class="bi bi-arrow-left"></i>
                Back to Database Settings
            </a>
        </div>
    </div>
</form>
@endsection
