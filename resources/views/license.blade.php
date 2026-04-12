@extends('installer::layout')

@section('title', 'License Verification')
@section('subtitle', 'Please provide your purchase details.')

@section('content')

@if($errors->any())
    <div class="alert alert-danger d-flex align-items-start mb-4" role="alert">
        <i class="bi bi-x-circle-fill fs-5 me-3 mt-1"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">There were errors with your submission</h6>
            <ul class="mb-0 small ps-3 text-danger">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('installer.license.save') }}" method="POST" id="licenseForm">
    @csrf
    
    <div class="row g-4">
        <div class="col-12">
            <label for="name" class="form-label fw-medium small text-secondary">Full Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="John Doe">
        </div>

        <div class="col-12">
            <label for="email" class="form-label fw-medium small text-secondary">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="email@example.com">
        </div>

        <div class="col-12">
            <label for="license_key" class="form-label fw-medium small text-secondary">License Key</label>
            <input type="text" name="license_key" id="license_key" class="form-control font-monospace" value="{{ old('license_key') }}" required placeholder="XXXX-XXXX-XXXX-XXXX">
            <div class="form-text mt-2"><i class="bi bi-shield-check me-1 text-success"></i> Your license key will be securely validated against our servers.</div>
        </div>
    </div>
</form>

@endsection

@section('footer')
    <a href="{{ route('installer.database') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-chevron-left me-1 small"></i> Back
    </a>
    
    <div class="d-flex align-items-center gap-3">
        @if(config('laravel_installer.license_check', 'required') === 'optional')
            <a href="{{ route('installer.install') }}" class="text-decoration-none small fw-medium me-2">
                Skip for Now
            </a>
        @endif
        
        <button type="button" onclick="document.getElementById('licenseForm').submit();" class="btn btn-primary px-4">
            Next step <i class="bi bi-chevron-right ms-1 small"></i>
        </button>
    </div>
@endsection
