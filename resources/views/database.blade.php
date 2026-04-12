@extends('installer::layout')

@section('title', 'Database Configuration')
@section('subtitle', 'Enter your database connection parameters.')

@section('content')

@if($errors->any())
    <div class="alert alert-danger d-flex align-items-start mb-4" role="alert">
        <i class="bi bi-x-circle-fill fs-5 me-3 mt-1"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">There were errors with your submission</h6>
            <ul class="mb-0 small ps-3 text-danger">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('installer.database.save') }}" method="POST" id="databaseForm">
    @csrf
    
    <div class="row g-3">
        <div class="col-md-8">
            <label for="host" class="form-label fw-medium small text-secondary">Database Host</label>
            <input type="text" name="host" id="host" class="form-control" value="{{ old('host', '127.0.0.1') }}" required>
        </div>

        <div class="col-md-4">
            <label for="port" class="form-label fw-medium small text-secondary">Port</label>
            <input type="number" name="port" id="port" class="form-control" value="{{ old('port', '3306') }}" required>
        </div>

        <div class="col-12">
            <label for="database" class="form-label fw-medium small text-secondary">Database Name</label>
            <input type="text" name="database" id="database" class="form-control" value="{{ old('database') }}" required placeholder="laravel_app">
        </div>

        <div class="col-md-6">
            <label for="username" class="form-label fw-medium small text-secondary">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required>
        </div>

        <div class="col-md-6">
            <label for="password" class="form-label fw-medium small text-secondary">Password</label>
            <input type="password" name="password" id="password" class="form-control" value="{{ old('password') }}">
        </div>
    </div>
</form>

<div id="testResult" class="mt-4"></div>

@endsection

@section('footer')
    <button type="button" class="btn btn-outline-secondary px-4" id="testConnectionBtn">
        Test Connection
    </button>
    
    <button type="button" onclick="document.getElementById('databaseForm').submit();" class="btn btn-primary px-4">
        Next step <i class="bi bi-chevron-right ms-1 small"></i>
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
                <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                    <div class="small fw-medium">${data.message}</div>
                </div>
            `;
        } else {
            if (data.missing_database) {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning d-flex align-items-start mb-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-3 mt-1"></i>
                        <div class="w-100">
                            <h6 class="alert-heading fw-bold mb-1">Database Not Found</h6>
                            <p class="mb-3 small">${data.message}</p>
                            <button type="button" id="createDatabaseBtn" class="btn btn-sm btn-warning fw-medium px-3">Create Database</button>
                        </div>
                    </div>
                `;
                document.getElementById('createDatabaseBtn').addEventListener('click', function() {
                    createDatabase(formData);
                });
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                        <i class="bi bi-x-circle-fill fs-5 me-3"></i>
                        <div class="small fw-medium">${data.message}</div>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                <i class="bi bi-x-circle-fill fs-5 me-3"></i>
                <div class="small fw-medium">Connection test failed entirely.</div>
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
    createBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Creating...';
    
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
                <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                    <div class="small fw-medium">${data.message}</div>
                </div>
            `;
            setTimeout(() => { document.getElementById('testConnectionBtn').click(); }, 500);
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                    <i class="bi bi-x-circle-fill fs-5 me-3"></i>
                    <div class="small fw-medium">${data.message}</div>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                <i class="bi bi-x-circle-fill fs-5 me-3"></i>
                <div class="small fw-medium">Database creation failed.</div>
            </div>
        `;
    });
}
</script>
@endpush
