@extends('installer::layout')

@section('title', 'Database')
@section('subtitle', 'Enter your database connection details.')

@section('content')

@if($errors->any())
<div class="installer-alert danger" style="margin-bottom:.9rem;margin-top:0">
    <i class="bi bi-x-circle-fill"></i>
    <div>
        <strong>Fix the following errors:</strong>
        <ul style="margin:.3rem 0 0;padding-left:1.1rem">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('installer.database.save') }}" method="POST" id="databaseForm">
    @csrf

    <div style="display:grid;grid-template-columns:1fr auto;gap:.6rem;margin-bottom:.6rem">
        <div>
            <label class="form-label" for="host">Host</label>
            <input type="text" name="host" id="host" class="form-control" value="{{ old('host', '127.0.0.1') }}" required>
        </div>
        <div style="width:90px">
            <label class="form-label" for="port">Port</label>
            <input type="number" name="port" id="port" class="form-control" value="{{ old('port', '3306') }}" required>
        </div>
    </div>

    <div style="margin-bottom:.6rem">
        <label class="form-label" for="database">Database Name</label>
        <input type="text" name="database" id="database" class="form-control" value="{{ old('database') }}" required placeholder="my_database">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-bottom:.9rem">
        <div>
            <label class="form-label" for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required>
        </div>
        <div>
            <label class="form-label" for="password">Password</label>
            <div style="position:relative">
                <input type="password" name="password" id="password" class="form-control" value="{{ old('password') }}" style="padding-right:2.2rem">
                <button type="button" id="togglePwd" style="position:absolute;right:.5rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:.85rem;padding:0">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>
    </div>

    <label class="custom-check" for="confirmWipe">
        <input type="checkbox" name="confirm_wipe" value="1" id="confirmWipe" required>
        <div>
            <div class="custom-check-text">I understand this will wipe the database and run migrations/seeders</div>
            <div class="custom-check-hint">Existing data will be removed from the specified database.</div>
        </div>
    </label>
</form>

<div id="testResult" style="margin-top:.75rem"></div>

@endsection

@section('footer')
    <button type="button" class="btn-ghost" id="testConnectionBtn">
        <i class="bi bi-plug"></i> Test Connection
    </button>
    <button type="button" onclick="document.getElementById('databaseForm').submit()" class="btn-primary-custom">
        Continue <i class="bi bi-arrow-right"></i>
    </button>
@endsection

@push('scripts')
<script>
// Toggle password visibility
document.getElementById('togglePwd').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Test connection
document.getElementById('testConnectionBtn').addEventListener('click', function () {
    const btn = this;
    const formData = new FormData(document.getElementById('databaseForm'));
    const result = document.getElementById('testResult');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:12px;height:12px;border-width:2px"></span> Testing…';

    fetch('{{ route('installer.database.test') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            result.innerHTML = `<div class="installer-alert success" style="margin-top:0"><i class="bi bi-check-circle-fill"></i><div>${data.message}</div></div>`;
        } else if (data.missing_database) {
            result.innerHTML = `
                <div class="installer-alert warning" style="margin-top:0">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <div style="margin-bottom:.4rem">${data.message}</div>
                        <button type="button" id="createDbBtn" class="btn-primary-custom" style="font-size:.72rem;padding:.3rem .8rem">
                            <i class="bi bi-plus-circle"></i> Create Database
                        </button>
                    </div>
                </div>`;
            document.getElementById('createDbBtn').addEventListener('click', () => createDatabase(formData));
        } else {
            result.innerHTML = `<div class="installer-alert danger" style="margin-top:0"><i class="bi bi-x-circle-fill"></i><div>${data.message}</div></div>`;
        }
    })
    .catch(() => {
        result.innerHTML = `<div class="installer-alert danger" style="margin-top:0"><i class="bi bi-x-circle-fill"></i><div>Connection test failed.</div></div>`;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plug"></i> Test Connection';
    });
});

function createDatabase(formData) {
    const createBtn = document.getElementById('createDbBtn');
    const result    = document.getElementById('testResult');
    createBtn.disabled = true;
    createBtn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:11px;height:11px;border-width:2px"></span> Creating…';

    fetch('{{ route('installer.database.create') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            result.innerHTML = `<div class="installer-alert success" style="margin-top:0"><i class="bi bi-check-circle-fill"></i><div>${data.message}</div></div>`;
        } else {
            result.innerHTML = `<div class="installer-alert danger" style="margin-top:0"><i class="bi bi-x-circle-fill"></i><div>${data.message}</div></div>`;
        }
    })
    .catch(() => {
        result.innerHTML = `<div class="installer-alert danger" style="margin-top:0"><i class="bi bi-x-circle-fill"></i><div>Database creation failed.</div></div>`;
    });
}
</script>
@endpush
