@extends('installer::layout')

@section('title', 'All Done!')
@section('subtitle', 'Your application is installed and ready to use.')

@section('content')

<div style="text-align:center;padding:.75rem 0 1rem">
    <div class="complete-icon success" style="margin-bottom:.85rem">
        <i class="bi bi-rocket-takeoff-fill"></i>
    </div>
    <div style="font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:.3rem">
        {{ config('laravel_installer.app_name', 'Application') }} is ready
    </div>
    <div style="font-size:.74rem;color:var(--muted)">
        Login with the credentials below and change your password immediately.
    </div>
</div>

<div style="margin-bottom:.5rem">
    <div style="font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:.5rem;display:flex;align-items:center;gap:.3rem">
        <i class="bi bi-person-badge-fill"></i> Admin Credentials
    </div>

    <div class="cred-row">
        <div>
            <div class="cred-label">Email</div>
            <div class="cred-value" id="credEmail">{{ config('laravel_installer.admin_email', 'admin@admin.com') }}</div>
        </div>
        <button class="copy-btn" onclick="copyText('credEmail', this)" title="Copy">
            <i class="bi bi-clipboard"></i>
        </button>
    </div>

    <div class="cred-row">
        <div>
            <div class="cred-label">Password</div>
            <div class="cred-value" id="credPass">{{ config('laravel_installer.admin_password', 'password') }}</div>
        </div>
        <button class="copy-btn" onclick="copyText('credPass', this)" title="Copy">
            <i class="bi bi-clipboard"></i>
        </button>
    </div>
</div>

<div class="installer-alert warning" style="margin-top:.75rem">
    <i class="bi bi-shield-exclamation"></i>
    <div><strong>Security:</strong> Change your password on first login.</div>
</div>

@endsection

@section('footer')
    <span></span>
    <a href="{{ url('/') }}" class="btn-primary-custom">
        Go to Application <i class="bi bi-arrow-right"></i>
    </a>
@endsection

@push('scripts')
<script>
function copyText(id, btn) {
    const text = document.getElementById(id).textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        btn.innerHTML = '<i class="bi bi-clipboard-check"></i>';
        btn.style.color = 'var(--accent)';
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-clipboard"></i>';
            btn.style.color = '';
        }, 1800);
    });
}
</script>
@endpush
