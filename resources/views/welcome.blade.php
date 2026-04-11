@extends('installer::layout')

@section('title', 'Welcome')

@section('content')


<div class="text-center mb-4">
    <h3>Welcome to the Installer</h3>
    <p class="text-muted">Let's get your {{ config('laravel_installer.app_name') }} up and running!</p>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">System Requirements</h5>
    </div>
    <div class="card-body p-0">
        @foreach($requirements as $key => $requirement)
        <div class="requirement-item">
            <span>{{ $requirement['name'] }}</span>
            <span>
                @if($requirement['status'])
                    <i class="bi bi-check-circle-fill text-success"></i>
                    @if(isset($requirement['current']))
                        <small class="text-muted">({{ $requirement['current'] }})</small>
                    @endif
                @else
                    <i class="bi bi-x-circle-fill text-danger"></i>
                @endif
            </span>
        </div>
        @endforeach
    </div>
</div>

@php
    $allPassed = collect($requirements)->every(fn($req) => $req['status']);
@endphp

@if($allPassed)
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> All requirements are met! You can proceed with the installation.
    </div>
    <div class="text-center">
        <a href="{{ route('installer.database') }}" class="btn btn-installer btn-lg">
            Continue <i class="bi bi-arrow-right"></i>
        </a>
    </div>
@else
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> Some requirements are not met. Please fix them before proceeding.
    </div>
@endif
@endsection
