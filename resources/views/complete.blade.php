@extends('installer::layout')

@section('title', 'Installation Complete')

@section('content')
<div class="text-center">
    <div class="mb-4">
        <div class="mx-auto bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px;">
            <i class="bi bi-patch-check-fill text-success" style="font-size: 48px;"></i>
        </div>
    </div>
    
    <h4 class="fw-bold mb-2">Installed!</h4>
    <p class="text-muted small mb-4">Your application is ready to use.</p>

    <div class="bg-white bg-opacity-40 p-4 rounded-4 border border-white mb-4 shadow-sm">
        <label class="small fw-bold text-muted text-uppercase mb-3 d-block tracking-wider" style="font-size: 0.7rem;">Admin Credentials</label>
        <div class="text-start">
            <div class="bg-white bg-opacity-50 p-3 rounded-3 mb-2 d-flex justify-content-between align-items-center border">
                <span class="small text-muted fw-medium">Email</span>
                <code class="small fw-bold text-primary">superadmin@codekernel.net</code>
            </div>
            <div class="bg-white bg-opacity-50 p-3 rounded-3 mb-3 d-flex justify-content-between align-items-center border">
                <span class="small text-muted fw-medium">Password</span>
                <code class="small fw-bold text-primary">12345678</code>
            </div>
        </div>
        <div class="alert alert-warning py-2 px-3 mb-0 text-start" style="background: rgba(245, 158, 11, 0.05); border: 1px dashed rgba(245, 158, 11, 0.2); color: #92400e;">
            <i class="bi bi-shield-lock-fill me-2"></i>
            <small class="fw-medium">Please change this immediately.</small>
        </div>
    </div>

    <div class="alert alert-info py-3 px-4 mb-4 text-start" style="background: rgba(99, 102, 241, 0.05); border: 1px dashed rgba(99, 102, 241, 0.2); color: #4338ca;">
        <div class="d-flex align-items-center">
            <i class="bi bi-lock-fill me-2"></i>
            <small class="fw-medium">The installer has been locked for security.</small>
        </div>
    </div>

    <a href="/" class="btn btn-installer">
        Go to Dashboard <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>
@endsection
