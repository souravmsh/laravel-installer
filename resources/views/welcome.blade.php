@extends('installer::layout')

@section('title', 'System Requirements')
@section('subtitle', 'Please ensure your server meets the following requirements before proceeding.')

@section('content')

@php
    $byGroup = collect($requirements)->groupBy(fn($r) => $r['group'] ?? 'requirements');
    $allPassed = collect($requirements)->every(fn($req) => $req['status']);
@endphp

{{-- ── System Requirements ─────────────────────────────────────────────── --}}
<div class="card border mb-3">
    <div class="card-header bg-light py-2 px-3">
        <span class="fw-semibold text-secondary small text-uppercase">
            <i class="bi bi-cpu me-1"></i> System Requirements
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="text-secondary small text-uppercase">Requirement</th>
                    <th scope="col" class="text-secondary small text-uppercase">Version</th>
                    <th scope="col" class="text-end text-secondary small text-uppercase">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byGroup->get('requirements', []) as $key => $requirement)
                <tr>
                    <td class="align-middle fw-medium">{{ $requirement['name'] }}</td>
                    <td class="align-middle text-muted">{{ $requirement['current'] ?? 'N/A' }}</td>
                    <td class="align-middle text-end">
                        @if($requirement['status'])
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 border border-success border-opacity-25 rounded-pill">
                                <i class="bi bi-check2 me-1"></i> Passed
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 border border-danger border-opacity-25 rounded-pill">
                                <i class="bi bi-x-lg me-1"></i> Failed
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── Permissions ──────────────────────────────────────────────────────── --}}
<div class="card border">
    <div class="card-header bg-light py-2 px-3">
        <span class="fw-semibold text-secondary small text-uppercase">
            <i class="bi bi-shield-lock me-1"></i> File &amp; Directory Permissions
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="text-secondary small text-uppercase">Path</th>
                    <th scope="col" class="text-secondary small text-uppercase">Location</th>
                    <th scope="col" class="text-end text-secondary small text-uppercase">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byGroup->get('permissions', []) as $key => $requirement)
                <tr>
                    <td class="align-middle fw-medium">{{ $requirement['name'] }}</td>
                    <td class="align-middle text-muted" style="font-size: .8rem; word-break: break-all;">
                        {{ $requirement['current'] ?? 'N/A' }}
                    </td>
                    <td class="align-middle text-end">
                        @if($requirement['status'])
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 border border-success border-opacity-25 rounded-pill">
                                <i class="bi bi-check2 me-1"></i> Writable
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 border border-danger border-opacity-25 rounded-pill">
                                <i class="bi bi-x-lg me-1"></i> Not Writable
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if(!$allPassed)
<div class="alert alert-danger d-flex align-items-start mt-4 mb-0" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-5 me-3 mt-1"></i>
    <div>
        <h6 class="alert-heading fw-bold mb-1">Cannot Proceed</h6>
        <p class="mb-0 small">Please resolve the failed requirements or permission issues listed above before continuing with the installation.</p>
        <p class="mb-0 small mt-1 text-muted">
            Run <code>php artisan laravel-installer:reset</code> to attempt an automatic permission fix.
        </p>
    </div>
</div>
@endif

@endsection

@section('footer')
    <div class="w-100 d-flex justify-content-end align-items-center">
        @if($allPassed)
            <a href="{{ route('installer.database') }}" class="btn btn-primary px-5 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm">
                <span>Next step</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        @else
            <button disabled class="btn btn-primary px-5 py-2 fw-semibold d-flex align-items-center gap-2 opacity-50 pe-none">
                <span>Next step</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        @endif
    </div>
@endsection
