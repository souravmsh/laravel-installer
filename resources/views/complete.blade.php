@extends('installer::layout')

@section('title', 'Installation Complete')

@section('content')
<div class="text-center animate-entrance">
    <div class="mb-8">
        <div class="mx-auto w-24 h-24 bg-emerald-100 rounded-[32px] flex items-center justify-center text-emerald-600 shadow-xl shadow-emerald-100 relative mb-6">
            <i class="bi bi-patch-check-fill text-[3rem]"></i>
            <div class="absolute inset-0 rounded-inherit bg-inherit filter blur-xl opacity-50 -z-10 scale-90 translate-y-3"></div>
        </div>
        <h4 class="text-3xl font-800 text-slate-900 mb-2 tracking-tight">Installation Complete!</h4>
        <p class="text-slate-500 font-medium text-sm">Your application has been successfully configured and is ready for use.</p>
    </div>

    <div class="bg-white/40 border border-white/20 p-6 rounded-3xl mb-6 shadow-sm text-left backdrop-blur-md relative overflow-hidden group hover:bg-white/50 transition-colors">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Default Admin Credentials</label>
        
        <div class="space-y-3 relative z-10">
            <div class="bg-white/80 border border-slate-100 p-4 rounded-2xl flex justify-between items-center group-hover:shadow-sm transition-shadow">
                <span class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                    <i class="bi bi-envelope text-indigo-400"></i>
                    Email
                </span>
                <code class="text-sm font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg">superadmin@codekernel.net</code>
            </div>
            <div class="bg-white/80 border border-slate-100 p-4 rounded-2xl flex justify-between items-center group-hover:shadow-sm transition-shadow">
                <span class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                    <i class="bi bi-key text-indigo-400"></i>
                    Password
                </span>
                <code class="text-sm font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg">12345678</code>
            </div>
        </div>
        
        <div class="mt-4 bg-amber-50/80 border border-amber-100 p-3 rounded-xl flex items-center gap-2 relative z-10">
            <i class="bi bi-exclamation-triangle-fill text-amber-500"></i>
            <span class="text-xs font-bold text-amber-700 uppercase tracking-widest">For your security, please change this immediately after login.</span>
        </div>
    </div>

    <div class="bg-indigo-50/50 border border-indigo-100/50 p-4 rounded-2xl flex items-start gap-3 mb-8 text-left">
        <i class="bi bi-lock-fill text-indigo-500 text-lg mt-0.5"></i>
        <div>
            <p class="text-sm font-bold text-indigo-800 mb-0.5">Installer Locked</p>
            <p class="text-[11px] font-medium text-indigo-600 leading-relaxed">The setup wizard has been securely locked. To reinstall, you must delete the `.installed` flag manually.</p>
        </div>
    </div>

    <a href="{{ url('/') }}" class="btn-premium w-full py-4.5 rounded-2xl text-white font-bold flex items-center justify-center gap-2 shadow-xl shadow-indigo-200 group text-lg">
        <span>Go to Dashboard</span>
        <i class="bi bi-arrow-right-circle-fill text-xl transition-transform group-hover:translate-x-1"></i>
    </a>
</div>
@endsection
