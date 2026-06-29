@extends('installer::layout')

@section('title', 'System Requirements')
@section('subtitle', 'Verify your server meets all requirements before continuing.')

@section('content')

@php
    $byGroup  = collect($requirements)->groupBy(fn($r) => $r['group'] ?? 'requirements');
    $allPassed = collect($requirements)->every(fn($r) => $r['status']);
@endphp

<table class="check-table">
    <thead>
        <tr>
            <th>Requirement</th>
            <th>Current</th>
            <th style="text-align:right">Status</th>
        </tr>
    </thead>
    <tbody>
        {{-- Requirements --}}
        <tr><td colspan="3" style="padding:0">
            <div class="group-heading"><i class="bi bi-cpu-fill"></i> System</div>
        </td></tr>
        @foreach($byGroup->get('requirements', []) as $req)
        <tr>
            <td class="fw-medium">{{ $req['name'] }}</td>
            <td style="color:var(--muted);font-size:.72rem;font-family:monospace">{{ $req['current'] ?? '—' }}</td>
            <td style="text-align:right">
                @if($req['status'])
                    <span class="pill pill-ok"><i class="bi bi-check2"></i> OK</span>
                @else
                    <span class="pill pill-bad"><i class="bi bi-x"></i> Failed</span>
                @endif
            </td>
        </tr>
        @endforeach

        {{-- Permissions --}}
        <tr><td colspan="3" style="padding:0">
            <div class="group-heading" style="border-top:1px solid #f3f4f6;margin-top:.2rem"><i class="bi bi-shield-lock-fill"></i> Permissions</div>
        </td></tr>
        @foreach($byGroup->get('permissions', []) as $req)
        <tr>
            <td class="fw-medium">{{ $req['name'] }}</td>
            <td style="color:var(--muted);font-size:.67rem;word-break:break-all;font-family:monospace">{{ $req['current'] ?? '—' }}</td>
            <td style="text-align:right">
                @if($req['status'])
                    <span class="pill pill-ok"><i class="bi bi-check2"></i> Writable</span>
                @else
                    <span class="pill pill-bad"><i class="bi bi-x"></i> Blocked</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(!$allPassed)
<div class="installer-alert danger">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <div>
        <strong>Cannot proceed.</strong> Resolve the failed checks above.
        set permissions to 777 for the failed files and directories.
    </div>
</div>
@endif

@endsection

@section('footer')
    <span></span>
    @if($allPassed)
        <a href="{{ route('installer.database') }}" class="btn-primary-custom">
            Continue <i class="bi bi-arrow-right"></i>
        </a>
    @else
        <button disabled class="btn-primary-custom">
            Continue <i class="bi bi-arrow-right"></i>
        </button>
    @endif
@endsection
