@extends('installer::layout')

@section('title', 'Database Configuration')

@section('content')

<div class="text-center mb-8">
    <h4 class="text-xl font-bold text-slate-900">Database Connection</h4>
    <p class="text-slate-500 text-sm">Configure your database storage parameters.</p>
</div>

@if($errors->any())
    <div class="bg-rose-50/50 border border-rose-100/50 p-4 rounded-2xl mb-6">
        <ul class="text-xs font-semibold text-rose-700 space-y-1">
            @foreach($errors->all() as $error)
                <li class="flex items-center gap-2">
                    <span class="w-1 h-1 rounded-full bg-rose-400"></span>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="bg-emerald-50/50 border border-emerald-100/50 p-4 rounded-2xl mb-6 flex items-center gap-2">
        <i class="bi bi-check-circle-fill text-emerald-500 text-sm"></i>
        <p class="text-xs font-bold text-emerald-800">{{ session('success') }}</p>
    </div>
@endif

<form action="{{ route('installer.database.save') }}" method="POST" id="databaseForm" class="space-y-5">
    @csrf
    
    <div class="space-y-1.5">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Database Host</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <i class="bi bi-hdd-network"></i>
            </div>
            <input type="text" name="host" class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 text-sm" value="{{ old('host', '127.0.0.1') }}" required placeholder="127.0.0.1">
        </div>
    </div>

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-8 space-y-1.5">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Database Name</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                    <i class="bi bi-database"></i>
                </div>
                <input type="text" name="database" class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 text-sm" value="{{ old('database') }}" required placeholder="db_name">
            </div>
        </div>
        <div class="col-span-4 space-y-1.5">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Port</label>
            <input type="number" name="port" class="block w-full px-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 text-sm" value="{{ old('port', '3306') }}" required placeholder="3306">
        </div>
    </div>

    <div class="space-y-1.5">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <i class="bi bi-person"></i>
            </div>
            <input type="text" name="username" class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 text-sm" value="{{ old('username') }}" required placeholder="root">
        </div>
    </div>

    <div class="space-y-1.5 pb-2">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <i class="bi bi-key"></i>
            </div>
            <input type="password" name="password" class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 text-sm" value="{{ old('password') }}" placeholder="••••••••">
        </div>
    </div>

    <div class="space-y-3">
        <button type="submit" class="btn-premium w-full py-4 rounded-2xl text-white font-bold flex items-center justify-center gap-2 shadow-lg shadow-indigo-200 group">
            <span>Continue to Next Step</span>
            <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
        </button>
        
        <button type="button" class="w-full py-3.5 rounded-2xl bg-white/40 border border-slate-200 text-slate-500 font-bold text-xs uppercase tracking-widest hover:bg-white hover:text-indigo-600 hover:border-indigo-200 transition-all flex items-center justify-center gap-2 group" id="testConnectionBtn">
            <i class="bi bi-plug-fill text-sm group-hover:rotate-12 transition-transform"></i> 
            Test Connection
        </button>
    </div>
</form>

<div id="testResult" class="mt-6"></div>
@endsection

@push('scripts')
<script>
document.getElementById('testConnectionBtn').addEventListener('click', function() {
    const btn = this;
    const form = document.getElementById('databaseForm');
    const formData = new FormData(form);
    const resultDiv = document.getElementById('testResult');
    
    btn.disabled = true;
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing...</span>';
    
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
                <div class="bg-emerald-50/80 border border-emerald-100 p-4 rounded-2xl flex items-center gap-3 animate-entrance">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-emerald-800">${data.message}</p>
                    </div>
                </div>
            `;
        } else {
            if (data.missing_database) {
                resultDiv.innerHTML = `
                    <div class="bg-amber-50/80 border border-amber-100 p-4 rounded-2xl animate-entrance">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <p class="text-xs font-bold text-amber-800">${data.message}</p>
                        </div>
                        <button type="button" class="w-full py-2.5 bg-amber-500 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-amber-600 transition-all" id="createDatabaseBtn">
                            Create Database Now
                        </button>
                    </div>
                `;
                
                document.getElementById('createDatabaseBtn').addEventListener('click', function() {
                    createDatabase(formData);
                });
            } else {
                resultDiv.innerHTML = `
                    <div class="bg-rose-50/80 border border-rose-100 p-4 rounded-2xl flex items-center gap-3 animate-entrance">
                        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                            <i class="bi bi-x-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-rose-800">${data.message}</p>
                        </div>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="bg-rose-50/80 border border-rose-100 p-4 rounded-2xl flex items-center gap-3 animate-entrance">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                    <i class="bi bi-x-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-rose-800">Connection test failed</p>
                </div>
            </div>
        `;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    });
});

function createDatabase(formData) {
    const resultDiv = document.getElementById('testResult');
    const createBtn = document.getElementById('createDatabaseBtn');
    
    createBtn.disabled = true;
    createBtn.innerHTML = 'Creating...';
    
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
                <div class="bg-emerald-50/80 border border-emerald-100 p-4 rounded-2xl flex items-center gap-3 animate-entrance">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-emerald-800">${data.message}</p>
                    </div>
                </div>
            `;
            setTimeout(() => {
                document.getElementById('testConnectionBtn').click();
            }, 1000);
        } else {
            resultDiv.innerHTML = `
                <div class="bg-rose-50/80 border border-rose-100 p-4 rounded-2xl flex items-center gap-3 animate-entrance">
                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                        <i class="bi bi-x-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-rose-800">${data.message}</p>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="bg-rose-50/80 border border-rose-100 p-4 rounded-2xl flex items-center gap-3 animate-entrance">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                    <i class="bi bi-x-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-rose-800">Failed to create database</p>
                </div>
            </div>
        `;
    });
}
</script>
@endpush
