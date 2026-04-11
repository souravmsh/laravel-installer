@extends('installer::layout')

@section('title', 'Database Configuration')

@section('content')


<div class="text-center mb-4">
    <h3>Database Configuration</h3>
    <p class="text-muted">Enter your database credentials</p>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('installer.database.save') }}" method="POST" id="databaseForm">
    @csrf
    
    <div class="mb-3">
        <label class="form-label">Database Host</label>
        <input type="text" name="host" class="form-control" value="{{ old('host', '127.0.0.1') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Database Port</label>
        <input type="number" name="port" class="form-control" value="{{ old('port', '3306') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Database Name</label>
        <input type="text" name="database" class="form-control" value="{{ old('database') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Database Username</label>
        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Database Password</label>
        <input type="password" name="password" class="form-control" value="{{ old('password') }}">
        <small class="text-muted">Leave blank if no password</small>
    </div>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary" id="testConnectionBtn">
            <i class="bi bi-plug"></i> Test Connection
        </button>
        <button type="submit" class="btn btn-installer">
            Continue <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</form>

<div id="testResult" class="mt-3"></div>
@endsection

@push('scripts')
<script>
document.getElementById('testConnectionBtn').addEventListener('click', function() {
    const btn = this;
    const form = document.getElementById('databaseForm');
    const formData = new FormData(form);
    const resultDiv = document.getElementById('testResult');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Testing...';
    
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
            resultDiv.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle"></i> ' + data.message + '</div>';
        } else {
            if (data.missing_database) {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> ${data.message}
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-primary" id="createDatabaseBtn">
                                <i class="bi bi-plus-circle"></i> Create Database
                            </button>
                        </div>
                    </div>
                `;
                
                // Add event listener for create button
                document.getElementById('createDatabaseBtn').addEventListener('click', function() {
                    createDatabase(formData);
                });
            } else {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle"></i> ' + data.message + '</div>';
            }
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle"></i> Connection test failed</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plug"></i> Test Connection';
    });
});

function createDatabase(formData) {
    const resultDiv = document.getElementById('testResult');
    const createBtn = document.getElementById('createDatabaseBtn');
    
    createBtn.disabled = true;
    createBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creating...';
    
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
            resultDiv.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle"></i> ' + data.message + '</div>';
            // Automatically re-test connection after short delay
            setTimeout(() => {
                document.getElementById('testConnectionBtn').click();
            }, 1000);
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle"></i> ' + data.message + '</div>';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle"></i> Failed to create database</div>';
    });
}
</script>
@endpush
