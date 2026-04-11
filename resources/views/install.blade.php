@extends('installer::layout')

@section('title', 'Installation')

@section('content')


<div class="text-center mb-4">
    <h3>Installing Application</h3>
    <p class="text-muted">Please wait while we set up your application</p>
</div>

<div id="installProgress">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="spinner-border text-primary me-3" role="status" id="spinner">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1" id="statusText">Preparing installation...</h6>
                    <small class="text-muted" id="statusDetail">This may take a few moments</small>
                </div>
            </div>
        </div>
    </div>

    <div class="progress" style="height: 25px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
             style="width: 0%" id="progressBar">0%</div>
    </div>
</div>

<div id="installComplete" style="display: none;">
    <div class="alert alert-success text-center">
        <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
        <h4 class="mt-3">Installation Completed Successfully!</h4>
        <p>Your {{ config('laravel_installer.app_name') }} is ready to use.</p>
        <a href="{{ route('installer.complete') }}" class="btn btn-installer mt-3">
            Finish <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>

<div id="installError" style="display: none;">
    <div class="alert alert-danger text-center">
        <i class="bi bi-x-circle" style="font-size: 3rem;"></i>
        <h4 class="mt-3">Installation Failed</h4>
        <p id="errorMessage">An error occurred during installation.</p>
        <a href="{{ route('installer.database') }}" class="btn btn-outline-danger mt-3">
            <i class="bi bi-arrow-left"></i> Go Back
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function() {
    const progressBar = document.getElementById('progressBar');
    const statusText = document.getElementById('statusText');
    const statusDetail = document.getElementById('statusDetail');
    const installProgress = document.getElementById('installProgress');
    const installComplete = document.getElementById('installComplete');
    const installError = document.getElementById('installError');
    const errorMessage = document.getElementById('errorMessage');

    // Simulate installation steps
    let progress = 0;
    const steps = [
        { text: 'Running database migrations...', detail: 'Creating tables and schema', progress: 25 },
        { text: 'Seeding database...', detail: 'Populating initial data', progress: 50 },
        { text: 'Configuring application...', detail: 'Setting up configurations', progress: 75 },
        { text: 'Finalizing installation...', detail: 'Almost done!', progress: 90 }
    ];

    let currentStep = 0;

    function updateProgress() {
        if (currentStep < steps.length) {
            const step = steps[currentStep];
            statusText.textContent = step.text;
            statusDetail.textContent = step.detail;
            progressBar.style.width = step.progress + '%';
            progressBar.textContent = step.progress + '%';
            currentStep++;
            setTimeout(updateProgress, 1500);
        } else {
            // Actually run the installation
            runInstallation();
        }
    }

    function runInstallation() {
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
                progressBar.textContent = '100%';
                setTimeout(() => {
                    installProgress.style.display = 'none';
                    installComplete.style.display = 'block';
                }, 500);
            } else {
                installProgress.style.display = 'none';
                errorMessage.textContent = data.message;
                installError.style.display = 'block';
            }
        })
        .catch(error => {
            installProgress.style.display = 'none';
            errorMessage.textContent = 'An unexpected error occurred: ' + error.message;
            installError.style.display = 'block';
        });
    }

    // Start the installation process
    setTimeout(updateProgress, 1000);
});
</script>
@endpush
