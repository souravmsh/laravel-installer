@extends('installer::layout')

@section('title', 'Database Configuration')
@section('subtitle', 'Enter your database connection parameters.')

@section('content')

@if($errors->any())
    <div class="rounded-md bg-red-50 p-4 border border-red-200 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="bi bi-x-circle-fill text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul role="list" class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('installer.database.save') }}" method="POST" id="databaseForm">
    @csrf
    
    <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
        
        <div class="sm:col-span-4">
            <label for="host" class="form-label">Database Host</label>
            <div class="mt-1">
                <input type="text" name="host" id="host" class="form-input" value="{{ old('host', '127.0.0.1') }}" required>
            </div>
        </div>

        <div class="sm:col-span-2">
            <label for="port" class="form-label">Port</label>
            <div class="mt-1">
                <input type="number" name="port" id="port" class="form-input" value="{{ old('port', '3306') }}" required>
            </div>
        </div>

        <div class="sm:col-span-6">
            <label for="database" class="form-label">Database Name</label>
            <div class="mt-1">
                <input type="text" name="database" id="database" class="form-input" value="{{ old('database') }}" required placeholder="laravel_app">
            </div>
        </div>

        <div class="sm:col-span-3">
            <label for="username" class="form-label">Username</label>
            <div class="mt-1">
                <input type="text" name="username" id="username" class="form-input" value="{{ old('username') }}" required>
            </div>
        </div>

        <div class="sm:col-span-3">
            <label for="password" class="form-label">Password</label>
            <div class="mt-1">
                <input type="password" name="password" id="password" class="form-input" value="{{ old('password') }}">
            </div>
        </div>
    </div>
</form>

<div id="testResult" class="mt-6"></div>

@endsection

@section('footer')
    <button type="button" class="btn-secondary" id="testConnectionBtn">
        Test Connection
    </button>
    
    <button type="button" onclick="document.getElementById('databaseForm').submit();" class="btn-primary">
        Next step <i class="bi bi-chevron-right ml-2 text-xs"></i>
    </button>
@endsection

@push('scripts')
<script>
document.getElementById('testConnectionBtn').addEventListener('click', function() {
    const btn = this;
    const form = document.getElementById('databaseForm');
    const formData = new FormData(form);
    const resultDiv = document.getElementById('testResult');
    
    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Testing...';
    
    // Custom tailwind spinner
    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing...';
    
    fetch('{{ route('installer.database.test') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0"><i class="bi bi-check-circle-fill text-green-400"></i></div>
                        <div class="ml-3"><p class="text-sm font-medium text-green-800">${data.message}</p></div>
                    </div>
                </div>
            `;
        } else {
            if (data.missing_database) {
                resultDiv.innerHTML = `
                    <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
                        <div class="flex">
                            <div class="flex-shrink-0"><i class="bi bi-exclamation-triangle-fill text-yellow-400"></i></div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Database Not Found</h3>
                                <div class="mt-2 text-sm text-yellow-700"><p>${data.message}</p></div>
                                <div class="mt-4">
                                    <button type="button" id="createDatabaseBtn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700">Create Database</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('createDatabaseBtn').addEventListener('click', function() {
                    createDatabase(formData);
                });
            } else {
                resultDiv.innerHTML = `
                    <div class="rounded-md bg-red-50 p-4 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0"><i class="bi bi-x-circle-fill text-red-400"></i></div>
                            <div class="ml-3"><p class="text-sm font-medium text-red-800">${data.message}</p></div>
                        </div>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="bi bi-x-circle-fill text-red-400"></i></div>
                    <div class="ml-3"><p class="text-sm font-medium text-red-800">Connection test failed entirely.</p></div>
                </div>
            </div>
        `;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

function createDatabase(formData) {
    const resultDiv = document.getElementById('testResult');
    const createBtn = document.getElementById('createDatabaseBtn');
    
    createBtn.disabled = true;
    createBtn.innerText = 'Creating...';
    
    fetch('{{ route('installer.database.create') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0"><i class="bi bi-check-circle-fill text-green-400"></i></div>
                        <div class="ml-3"><p class="text-sm font-medium text-green-800">${data.message}</p></div>
                    </div>
                </div>
            `;
            setTimeout(() => { document.getElementById('testConnectionBtn').click(); }, 500);
        } else {
            resultDiv.innerHTML = `
                <div class="rounded-md bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0"><i class="bi bi-x-circle-fill text-red-400"></i></div>
                        <div class="ml-3"><p class="text-sm font-medium text-red-800">${data.message}</p></div>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="bi bi-x-circle-fill text-red-400"></i></div>
                    <div class="ml-3"><p class="text-sm font-medium text-red-800">Database creation failed.</p></div>
                </div>
            </div>
        `;
    });
}
</script>
@endpush
