@extends('installer::layout')

@section('title', 'Setup Complete')
@section('subtitle', 'Your application is ready to use.')

@section('content')

<div class="text-center py-4 mb-4">
    <div class="d-inline-flex justify-content-center align-items-center bg-success text-white rounded-circle mb-3 shadow-sm" style="width: 70px; height: 70px;">
        <i class="bi bi-rocket-takeoff" style="font-size: 32px;"></i>
    </div>
    <h3 class="fw-bold text-dark">You're All Set!</h3>
    <p class="text-muted mx-auto" style="max-width: 400px;">
        {{ config('laravel_installer.app_name', 'System') }} has been installed and configured successfully. You may now log in to the administrator portal.
    </p>
</div>

<div class="card bg-light border">
    <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
        <h6 class="mb-0 fw-bold text-secondary text-uppercase small">
            <i class="bi bi-person-badge me-1"></i> Admin Credentials
        </h6>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-4 text-muted fw-normal small">Email Address</dt>
            <dd class="col-sm-8 fw-medium text-dark mb-2">{{ config('laravel_installer.admin_email', 'admin@admin.com') }}</dd>
            <dt class="col-sm-4 text-muted fw-normal small">Password</dt>
            <dd class="col-sm-8 fw-medium text-dark mb-0">{{ config('laravel_installer.admin_password', 'password') }}</dd>
        </dl>
    </div>
</div>

<div class="alert alert-warning border border-warning border-opacity-25 mt-4 d-flex" role="alert">
    <i class="bi bi-shield-exclamation fs-5 me-3 text-warning"></i>
    <div class="small text-dark">
        <strong>Security Warning:</strong> Please copy these credentials now and change your password immediately upon first login.
    </div>
</div>

@endsection

@section('footer')
    <div class="w-100 d-flex justify-content-end align-items-center">
        <a href="{{ url('/') }}" class="btn btn-primary px-5 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm">
            <span>Go to Application</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
@endsection
