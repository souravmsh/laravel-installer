@extends('installer::layout')

@section('title', 'Installation Complete')

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
    </div>
    
    <h2 class="mb-3">Installation Completed!</h2>
    <p class="text-muted mb-4">Your {{ config('laravel_installer.app_name') }} has been successfully installed.</p>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Default Admin Credentials</h5>
            <div class="text-start mt-3">
                <p class="mb-2"><strong>Email:</strong> <code>superadmin@codekernel.net</code></p>
                <p class="mb-2"><strong>Password:</strong> <code>12345678</code></p>
            </div>
            <div class="alert alert-warning mt-3 mb-0">
                <i class="bi bi-exclamation-triangle"></i> Please change the default password after logging in for security reasons.
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> The installer has been locked. You cannot access it again unless you delete the <code>storage/.installed</code> file.
    </div>

    <a href="/" class="btn btn-installer btn-lg">
        Go to Dashboard <i class="bi bi-arrow-right"></i>
    </a>
</div>
@endsection
