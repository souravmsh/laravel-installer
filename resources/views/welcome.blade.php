@extends('installer::layout')

@section('title', 'Welcome')

@section('content')


<div class="text-center mb-4">
    <h4 class="fw-bold">Welcome</h4>
    <p class="text-muted small">Let's get your application ready.</p>
</div>

<div class="mb-4">
    <label class="form-label fw-bold mb-3 small text-uppercase tracking-wider">System Requirements</label>
    @foreach($requirements as $key => $requirement)
    <div class="requirement-item">
        <span class="small fw-medium">{{ $requirement['name'] }}</span>
        <span>
            @if($requirement['status'])
                <i class="bi bi-check-circle-fill text-success"></i>
                @if(isset($requirement['current']))
                    <small class="text-muted ms-1" style="font-size: 0.75rem;">({{ $requirement['current'] }})</small>
                @endif
            @else
                <i class="bi bi-x-circle-fill text-danger"></i>
            @endif
        </span>
    </div>
    @endforeach
</div>

@php
    $allPassed = collect($requirements)->every(fn($req) => $req['status']);
@endphp

@if($allPassed)
    <div class="alert alert-success d-flex align-items-center mb-4" style="background: rgba(16, 185, 129, 0.1); color: #065f46;">
        <i class="bi bi-check-circle-fill me-2"></i>
        <small class="fw-medium">Ready to proceed!</small>
    </div>
    <a href="{{ route('installer.database') }}" class="btn btn-installer">
        Get Started <i class="bi bi-arrow-right ms-1"></i>
    </a>
@else
    <div class="alert alert-danger d-flex align-items-center mb-4" style="background: rgba(239, 68, 68, 0.1); color: #991b1b;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <small class="fw-medium">Fix requirements to continue.</small>
    </div>
@endif
@endsection
