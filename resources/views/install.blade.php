@extends('installer::layout')

@section('title', 'Installation')
@section('subtitle', 'Please wait while the installation completes.')

@section('content')

<div id="installProgress" class="py-8">
    <div class="mb-8">
        <div class="flex justify-between text-sm font-medium text-slate-900 mb-2">
            <span id="statusText">Migrating Database...</span>
            <span id="progressPercent">0%</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div id="progressBar" class="bg-brand-600 h-2.5 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
        </div>
        <p class="mt-3 text-xs text-slate-500" id="statusDetail">Setting up tables...</p>
    </div>

    <div class="bg-slate-900 rounded-md p-4 text-xs font-mono text-slate-300 h-32 overflow-y-auto" id="consoleLog">
        <div>> Starting installation wizard...</div>
    </div>
</div>

<div id="installComplete" style="display: none;" class="py-8 text-center">
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
        <i class="bi bi-check-lg text-2xl text-green-600"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-900 mb-2">Installation Successful</h3>
    <p class="text-sm text-slate-500">The application database has been installed successfully.</p>
</div>

<div id="installError" style="display: none;" class="py-8 text-center">
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
        <i class="bi bi-x-lg text-2xl text-red-600"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-900 mb-2">Installation Failed</h3>
    <p id="errorMessage" class="text-sm text-red-600 mb-6 border border-red-200 bg-red-50 p-3 rounded text-left">An error occurred.</p>
</div>

@endsection

@section('footer')
    <div id="footerInstall" class="w-full flex justify-between">
        <button type="button" disabled class="btn-secondary opacity-50 cursor-not-allowed border-transparent bg-transparent">
            <!-- empty space -->
        </button>
        <button type="button" disabled id="installNextBtn" class="btn-primary opacity-50 cursor-not-allowed">
            Installing...
        </button>
    </div>
    
    <div id="footerComplete" style="display: none;" class="w-full flex justify-end">
        <a href="{{ route('installer.complete') }}" class="btn-primary">
            Finish <i class="bi bi-chevron-right ml-2 text-xs"></i>
        </a>
    </div>

    <div id="footerError" style="display: none;" class="w-full flex justify-between">
        <a href="{{ route('installer.database') }}" class="btn-secondary">
            <i class="bi bi-chevron-left mr-2 text-xs"></i> Back to Database
        </a>
        <button type="button" disabled class="btn-primary opacity-50 cursor-not-allowed">
            Next step <i class="bi bi-chevron-right ml-2 text-xs"></i>
        </button>
    </div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function() {
    const progressBar = document.getElementById('progressBar');
    const statusText = document.getElementById('statusText');
    const statusDetail = document.getElementById('statusDetail');
    const progressPercent = document.getElementById('progressPercent');
    const consoleLog = document.getElementById('consoleLog');
    
    const installProgress = document.getElementById('installProgress');
    const installComplete = document.getElementById('installComplete');
    const installError = document.getElementById('installError');
    const errorMessage = document.getElementById('errorMessage');

    const footerInstall = document.getElementById('footerInstall');
    const footerComplete = document.getElementById('footerComplete');
    const footerError = document.getElementById('footerError');

    function logToConsole(message) {
        const div = document.createElement('div');
        div.textContent = '> ' + message;
        consoleLog.appendChild(div);
        consoleLog.scrollTop = consoleLog.scrollHeight;
    }

    let progress = 0;
    const steps = [
        { text: 'Migrating Database', detail: 'Creating tables...', progress: 30, log: 'Running php artisan migrate...' },
        { text: 'Seeding Data', detail: 'Inserting default records...', progress: 60, log: 'Running php artisan db:seed...' },
        { text: 'Configuring Environment', detail: 'Setting up services...', progress: 85, log: 'Publishing core assets...' },
    ];

    let currentStep = 0;

    function updateProgress() {
        if (currentStep < steps.length) {
            const step = steps[currentStep];
            statusText.textContent = step.text;
            statusDetail.textContent = step.detail;
            progressBar.style.width = step.progress + '%';
            progressPercent.textContent = step.progress + '%';
            logToConsole(step.log);
            
            currentStep++;
            setTimeout(updateProgress, 1200);
        } else {
            runInstallation();
        }
    }

    function runInstallation() {
        logToConsole('Finalizing installation process...');
        fetch('{{ route('installer.install.process') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                progressBar.style.width = '100%';
                progressPercent.textContent = '100%';
                logToConsole('Installation completed successfully.');
                
                setTimeout(() => {
                    installProgress.style.display = 'none';
                    installComplete.style.display = 'block';
                    
                    footerInstall.style.display = 'none';
                    footerComplete.style.display = 'flex';
                }, 800);
            } else {
                installProgress.style.display = 'none';
                errorMessage.textContent = data.message;
                installError.style.display = 'block';
                
                footerInstall.style.display = 'none';
                footerError.style.display = 'flex';
            }
        })
        .catch(error => {
            installProgress.style.display = 'none';
            errorMessage.textContent = 'An expected error occurred during setup.';
            installError.style.display = 'block';
            
            footerInstall.style.display = 'none';
            footerError.style.display = 'flex';
        });
    }

    setTimeout(updateProgress, 1000);
});
</script>
@endpush
