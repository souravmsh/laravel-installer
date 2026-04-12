@extends('installer::layout')

@section('title', 'License Verification')
@section('subtitle', 'Please provide your purchase details.')

@section('content')

@if($errors->any())
    <div class="rounded-md bg-red-50 p-4 border border-red-200 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="bi bi-x-circle-fill text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul role="list" class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('installer.license.save') }}" method="POST" id="licenseForm">
    @csrf
    
    <div class="space-y-6">
        <div>
            <label for="name" class="form-label">Full Name</label>
            <div class="mt-1">
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required placeholder="John Doe">
            </div>
        </div>

        <div>
            <label for="email" class="form-label">Email Address</label>
            <div class="mt-1">
                <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required placeholder="email@example.com">
            </div>
        </div>

        <div>
            <label for="license_key" class="form-label">License Key</label>
            <div class="mt-1">
                <input type="text" name="license_key" id="license_key" class="form-input font-mono text-sm tracking-wide" value="{{ old('license_key') }}" required placeholder="XXXX-XXXX-XXXX-XXXX">
            </div>
            <p class="mt-2 text-xs text-slate-500">Your license key will be validated against our servers.</p>
        </div>
    </div>
</form>

@endsection

@section('footer')
    <a href="{{ route('installer.database') }}" class="btn-secondary">
        <i class="bi bi-chevron-left mr-2 text-xs"></i> Back
    </a>
    
    <div class="flex items-center gap-3">
        @if(config('laravel_installer.license_check', 'required') === 'optional')
            <a href="{{ route('installer.install') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500 mr-2">
                Skip for Now
            </a>
        @endif
        
        <button type="button" onclick="document.getElementById('licenseForm').submit();" class="btn-primary">
            Next step <i class="bi bi-chevron-right ml-2 text-xs"></i>
        </button>
    </div>
@endsection
