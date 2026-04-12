@extends('installer::layout')

@section('title', 'Installation')

@section('content')


<div class="text-center mb-4">
    <h4 class="fw-bold">Installing</h4>
    <p class="text-muted small">Setting up your environment.</p>
</div>

<div id="installProgress">
    <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <div class="spinner-grow text-primary me-3" role="status" style="width: 20px; height: 20px; background-color: #6366f1;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div>
                <h6 class="mb-0 fw-bold small" id="statusText">Preparing...</h6>
                <p class="text-muted mb-0" style="font-size: 0.75rem;" id="statusDetail">Hang tight.</p>
            </div>
        </div>
        
        <div class="progress" style="height: 8px; border-radius: 4px; background: rgba(0,0,0,0.05); overflow: visible;">
            <div class="progress-bar" role="progressbar" 
                 style="width: 0%; background: var(--primary-gradient); border-radius: 4px; transition: width 0.5s ease; position: relative;" id="progressBar">
                 <div style="position: absolute; right: 0; top: -25px; font-size: 0.7rem; font-weight: 700; color: #6366f1;" id="progressPercent">0%</div>
            </div>
        </div>
    </div>
</div>

<div id="installComplete" style="display: none;" class="text-center">
    <div class="mb-4">
        <div class="mx-auto bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <i class="bi bi-check2-all text-success" style="font-size: 40px;"></i>
        </div>
    </div>
    <h4 class="fw-bold mb-2">Success!</h4>
    <p class="text-muted small mb-4">Everything is ready for you.</p>
    <a href="{{ route('installer.complete') }}" class="btn btn-installer">
        Finish Setup <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

<div id="installError" style="display: none;" class="text-center">
    <div class="mb-4">
        <div class="mx-auto bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <i class="bi bi-x-lg text-danger" style="font-size: 32px;"></i>
        </div>
    </div>
    <h4 class="fw-bold mb-2">Oops!</h4>
    <p id="errorMessage" class="text-muted small mb-4">Something went wrong during setup.</p>
    <div class="d-grid">
        <a href="{{ route('installer.database') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Try Again
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
            document.getElementById('progressPercent').textContent = step.progress + '%';
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
                document.getElementById('progressPercent').textContent = '100%';
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
