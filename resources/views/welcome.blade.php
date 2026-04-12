@extends('installer::layout')

@section('title', 'System Requirements')
@section('subtitle', 'Please ensure your server meets the following requirements.')

@section('content')

<div class="border border-slate-200 rounded-md overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Requirement</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Version</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @foreach($requirements as $key => $requirement)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                    {{ $requirement['name'] }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                    {{ $requirement['current'] ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    @if($requirement['status'])
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="bi bi-check2 me-1"></i> Passed
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="bi bi-x-lg me-1"></i> Failed
                        </span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@php
    $allPassed = collect($requirements)->every(fn($req) => $req['status']);
@endphp

@if(!$allPassed)
<div class="mt-4 rounded-md bg-red-50 p-4 border border-red-200">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="bi bi-exclamation-triangle-fill text-red-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Cannot Proceed</h3>
            <div class="mt-2 text-sm text-red-700">
                <p>Please resolve the failed requirements listed above before continuing with the installation.</p>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('footer')
    <div></div> <!-- Empty div for flex space-between -->
    
    @if($allPassed)
        <a href="{{ route('installer.database') }}" class="btn-primary">
            Next step <i class="bi bi-chevron-right ml-2 text-xs"></i>
        </a>
    @else
        <button disabled class="btn-primary opacity-50 cursor-not-allowed">
            Next step <i class="bi bi-chevron-right ml-2 text-xs"></i>
        </button>
    @endif
@endsection
