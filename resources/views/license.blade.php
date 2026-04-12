@extends('installer::layout')

@section('title', 'License Validation')

@section('content')


<div class="text-center mb-4">
    <h4 class="fw-bold">License</h4>
    <p class="text-muted small">Enter your license information.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.1); color: #991b1b;">
        <ul class="mb-0 small fw-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success mb-4" style="background: rgba(16, 185, 129, 0.1); color: #065f46;">
        <small class="fw-medium">{{ session('success') }}</small>
    </div>
@endif

<form action="{{ route('installer.license.save') }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label class="form-label small fw-semibold text-muted">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Your Name">
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-muted">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@example.com">
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-muted">License Key</label>
        <input type="text" name="license_key" class="form-control" value="{{ old('license_key') }}" required placeholder="XXXX-XXXX-XXXX-XXXX">
    </div>

    <div class="alert alert-info mb-4" style="background: rgba(99, 102, 241, 0.05); border: 1px dashed rgba(99, 102, 241, 0.2); color: #4338ca;">
        <div class="d-flex">
            <i class="bi bi-info-circle-fill me-2"></i>
            <small class="fw-medium">Your license will be validated with our central server.</small>
        </div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-installer">
            Validate & Continue <i class="bi bi-arrow-right ms-1"></i>
        </button>
        
        @if(config('laravel_installer.license_check', 'required') === 'optional')
            <a href="{{ route('installer.install') }}" class="btn btn-link text-decoration-none text-muted transition-all">
                <small class="fw-medium">Skip for now</small>
            </a>
        @endif
        
        <a href="{{ route('installer.database') }}" class="btn btn-link text-decoration-none text-muted">
            <small class="fw-medium"><i class="bi bi-arrow-left me-1"></i> Back to Database</small>
        </a>
    </div>
</form>
@endsection
