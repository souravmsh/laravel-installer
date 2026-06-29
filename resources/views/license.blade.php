@extends('installer::layout')

@section('title', 'License')
@section('subtitle', 'Enter your purchase details to activate the software.')

@section('content')

@if($errors->any())
<div class="installer-alert danger" style="margin-bottom:.9rem;margin-top:0">
    <i class="bi bi-x-circle-fill"></i>
    <div>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<form action="{{ route('installer.license.save') }}" method="POST" id="licenseForm">
    @csrf

    <div style="margin-bottom:.6rem">
        <label class="form-label" for="name">Full Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="John Doe">
    </div>

    <div style="margin-bottom:.6rem">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="you@example.com">
    </div>

    <div style="margin-bottom:.5rem">
        <label class="form-label" for="license_key">License Key</label>
        <input type="text" name="license_key" id="license_key" class="form-control"
               style="font-family:monospace;letter-spacing:.05em;text-transform:uppercase"
               value="{{ old('license_key') }}" required placeholder="XXXX-XXXX-XXXX-XXXX"
               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9]/g,'').replace(/(.{4})(?=.)/g,'$1-').substring(0,19)">
        <div class="form-text" style="margin-top:.3rem">
            <i class="bi bi-shield-check" style="color:#059669"></i>
            Validated securely against the licensing server.
        </div>
    </div>
</form>

@endsection

@section('footer')
    <a href="{{ route('installer.database') }}" class="btn-ghost">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div style="display:flex;align-items:center;gap:.6rem">
        @if(config('laravel_installer.license_check', 'required') === 'optional')
            <a href="{{ route('installer.install') }}" style="font-size:.73rem;color:var(--muted);text-decoration:none">Skip</a>
        @endif
        <button type="button" onclick="document.getElementById('licenseForm').submit()" class="btn-primary-custom">
            Verify & Continue <i class="bi bi-arrow-right"></i>
        </button>
    </div>
@endsection
