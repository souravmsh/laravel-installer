@extends('installer::layout')

@section('title', 'License Validation')

@section('content')


<div class="text-center mb-4">
    <h3>License Validation</h3>
    <p class="text-muted">Enter your license information</p>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('installer.license.save') }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">License Key</label>
        <input type="text" name="license_key" class="form-control" value="{{ old('license_key') }}" required>
        <small class="text-muted">Enter your purchase license key</small>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Your license will be validated with our central server. Make sure you have an active internet connection.
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('installer.database') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <button type="submit" class="btn btn-installer">
            Validate & Continue <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
