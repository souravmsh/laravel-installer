@extends('installer::layout')

@section('title', 'Installation')
@section('subtitle', 'Please wait while the installation completes.')

@section('content')

<div id="installProgress" class="py-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between text-dark fw-medium small mb-2">
            <span id="statusText">Migrating Database...</span>
            <span id="progressPercent" class="fw-bold text-primary">0%</span>
        </div>
        
        <div class="progress" style="height: 10px;">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%; transition: width .5s ease;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        
        <p class="mt-2 text-muted small" id="statusDetail">Setting up tables...</p>
    </div>

    <div class="card bg-dark text-light border-0 mt-4">
        <div class="card-body p-3 font-monospace small" id="consoleLog" style="height: 160px; overflow-y: auto;">
            <div class="text-secondary">> Starting installation wizard...</div>
        </div>
    </div>
</div>

<div id="installComplete" class="d-none py-5 text-center">
    <div class="d-inline-flex justify-content-center align-items-center bg-success bg-opacity-10 rounded-circle mb-4" style="width: 80px; height: 80px;">
        <i class="bi bi-check-lg text-success" style="font-size: 40px;"></i>
    </div>
    <h3 class="fw-bold mb-2 text-dark">Installation Successful</h3>
    <p class="text-muted">The application database has been installed successfully.</p>
</div>

<div id="installError" class="d-none py-5 text-center">
    <div class="d-inline-flex justify-content-center align-items-center bg-danger bg-opacity-10 rounded-circle mb-4" style="width: 80px; height: 80px;">
        <i class="bi bi-x-lg text-danger" style="font-size: 40px;"></i>
    </div>
    <h3 class="fw-bold mb-3 text-dark">Installation Failed</h3>
    <div id="errorMessage" class="alert alert-danger text-start mx-auto mb-0" style="max-width: 400px;">
        An error occurred.
    </div>
</div>

@endsection

@section('footer')
    <div id="footerInstall" class="w-100 d-flex justify-content-between align-items-center">
        <button type="button" disabled class="btn btn-outline-secondary opacity-0 pe-none">
            <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <button type="button" disabled id="installNextBtn" class="btn btn-primary px-4 d-flex align-items-center gap-2">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Installing...</span>
        </button>
    </div>
    
    <div id="footerComplete" class="w-100 d-none justify-content-end align-items-center">
        <a href="{{ route('installer.complete') }}" class="btn btn-primary px-5 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm">
            <span>Finish</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div id="footerError" class="w-100 d-none justify-content-between align-items-center">
        <a href="{{ route('installer.database') }}" class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Database</span>
        </a>
        <button type="button" disabled class="btn btn-primary px-4 opacity-50 pe-none">
            <span>Next step</span>
            <i class="bi bi-chevron-right ms-1 small"></i>
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
        div.className = 'text-success mb-1';
        div.innerHTML = '<span class="text-secondary">></span> ' + message;
        consoleLog.appendChild(div);
        consoleLog.scrollTop = consoleLog.scrollHeight;
    }

    let progress = 0;
    const steps = [
        { text: 'Finalizing Configuration', detail: 'Setting up details...', progress: 50, log: 'Saving configuration...' },
        { text: 'Saving License Data', detail: 'Storing license information...', progress: 85, log: 'Writing to database...' },
    ];

    let currentStep = 0;

    function updateProgress() {
        if (currentStep < steps.length) {
            const step = steps[currentStep];
            statusText.textContent = step.text;
            statusDetail.textContent = step.detail;
            progressBar.style.width = step.progress + '%';
            progressBar.setAttribute('aria-valuenow', step.progress);
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
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-success');
                progressBar.style.width = '100%';
                progressPercent.textContent = '100%';
                logToConsole('Installation completed successfully.');
                
                setTimeout(() => {
                    installProgress.classList.add('d-none');
                    installComplete.classList.remove('d-none');
                    
                    footerInstall.classList.add('d-none');
                    footerInstall.classList.remove('d-flex');
                    
                    footerComplete.classList.remove('d-none');
                    footerComplete.classList.add('d-flex');
                }, 800);
            } else {
                installProgress.classList.add('d-none');
                errorMessage.textContent = data.message;
                installError.classList.remove('d-none');
                
                footerInstall.classList.add('d-none');
                footerInstall.classList.remove('d-flex');
                
                footerError.classList.remove('d-none');
                footerError.classList.add('d-flex');
            }
        })
        .catch(error => {
            installProgress.classList.add('d-none');
            errorMessage.textContent = 'An expected error occurred during setup.';
            installError.classList.remove('d-none');
            
            footerInstall.classList.add('d-none');
            footerInstall.classList.remove('d-flex');
            
            footerError.classList.remove('d-none');
            footerError.classList.add('d-flex');
        });
    }

    setTimeout(updateProgress, 1000);
});
</script>
@endpush
