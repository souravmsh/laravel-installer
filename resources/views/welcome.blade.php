@extends('installer::layout')

@section('title', 'System Requirements')
@section('subtitle', 'Please ensure your server meets the following requirements.')

@section('content')

<div class="card border">
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
                @foreach($requirements as $key => $requirement)
                <tr>
                    <td class="align-middle fw-medium">
                        {{ $requirement['name'] }}
                    </td>
                    <td class="align-middle text-muted">
                        {{ $requirement['current'] ?? 'N/A' }}
                    </td>
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

@php
    $allPassed = collect($requirements)->every(fn($req) => $req['status']);
@endphp

@if(!$allPassed)
<div class="alert alert-danger d-flex align-items-start mt-4 mb-0" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-5 me-3 mt-1"></i>
    <div>
        <h6 class="alert-heading fw-bold mb-1">Cannot Proceed</h6>
        <p class="mb-0 small">Please resolve the failed requirements listed above before continuing with the installation.</p>
    </div>
</div>
@endif

@endsection

@section('footer')
    <div></div> <!-- Empty div for flex space-between -->
    
    @if($allPassed)
        <a href="{{ route('installer.database') }}" class="btn btn-primary px-4">
            Next step <i class="bi bi-chevron-right ms-1 small"></i>
        </a>
    @else
        <button disabled class="btn btn-primary px-4 opacity-50 pe-none">
            Next step <i class="bi bi-chevron-right ms-1 small"></i>
        </button>
    @endif
@endsection
