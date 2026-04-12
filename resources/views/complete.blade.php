@extends('installer::layout')

@section('title', 'Setup Complete')
@section('subtitle', 'Your application is securely installed and ready.')

@section('content')

<div class="py-4">
    <div class="bg-brand-50 border border-brand-200 rounded-md p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="bi bi-info-circle-fill text-brand-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-brand-800">Installer Locked</h3>
                <div class="mt-2 text-sm text-brand-700">
                    <p>The installer wizard has been disabled. To run it again, delete the <code>.installed</code> file located in your storage directory.</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h4 class="text-sm font-semibold text-slate-900 mb-3 border-b border-slate-200 pb-2">Default Administrator</h4>
        
        <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
            <dl class="divide-y divide-slate-200">
                <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-slate-500">Email address</dt>
                    <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2 font-mono">superadmin@codekernel.net</dd>
                </div>
                <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-slate-500">Password</dt>
                    <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2 font-mono">12345678</dd>
                </div>
            </dl>
        </div>
        
        <p class="mt-3 text-xs text-red-600 font-medium">
            <i class="bi bi-shield-exclamation text-red-500 mr-1"></i>
            Please change this password immediately upon logging in!
        </p>
    </div>
</div>

@endsection

@section('footer')
    <div></div>
    
    <a href="{{ url('/') }}" class="btn-primary">
        Go to Login <i class="bi bi-box-arrow-in-right ml-2 text-xs"></i>
    </a>
@endsection
