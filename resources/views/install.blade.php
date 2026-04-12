@extends('installer::layout')

@section('title', 'Installation')

@section('content')

<div class="text-center mb-10">
    <h4 class="text-xl font-bold text-slate-900">Finalizing Setup</h4>
    <p class="text-slate-500 text-sm">Please wait while we prepare your environment.</p>
</div>

<div id="installProgress" class="animate-entrance">
    <div class="mb-8">
        <div class="flex items-start gap-4 mb-6">
            <div class="relative flex-shrink-0">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i class="bi bi-gear-fill animate-[spin_3s_linear_infinite] text-xl"></i>
                </div>
                <div class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white"></div>
            </div>
            <div class="pt-1">
                <h6 class="mb-1 font-bold text-slate-800 text-sm leading-none" id="statusText">Preparing Workspace...</h6>
                <p class="text-slate-400 font-semibold text-[11px] uppercase tracking-wider" id="statusDetail">Initial verification in progress</p>
            </div>
        </div>
        
        <div class="relative pt-1">
            <div class="flex mb-2 items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-100/50">
                        Installation Progress
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold inline-block text-indigo-600" id="progressPercent">
                        0%
                    </span>
                </div>
            </div>
            <div class="overflow-hidden h-2.5 mb-4 text-xs flex rounded-full bg-slate-100">
                <div id="progressBar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-700 ease-out"></div>
            </div>
        </div>
    </div>
    
    <div class="bg-indigo-50/30 border border-indigo-100/50 p-4 rounded-2xl">
        <p class="text-[11px] font-medium text-slate-500 leading-relaxed italic text-center">
            "We are setting up the database, configurations, and core assets to ensure everything runs smoothly."
        </p>
    </div>
</div>

<div id="installComplete" style="display: none;" class="text-center animate-entrance">
    <div class="mb-6">
        <div class="mx-auto w-24 h-24 bg-emerald-100 rounded-[32px] flex items-center justify-center text-emerald-600 shadow-xl shadow-emerald-100 relative">
            <i class="bi bi-check-lg text-5xl"></i>
            <div class="absolute inset-0 rounded-inherit bg-inherit filter blur-xl opacity-40 -z-10 scale-90 translate-y-3"></div>
        </div>
    </div>
    <h4 class="text-2xl font-800 text-slate-900 mb-2">Installation Complete!</h4>
    <p class="text-slate-500 font-medium text-sm mb-8">Your application has been successfully configured and is ready for use.</p>
    
    <a href="{{ route('installer.complete') }}" class="btn-premium w-full py-4 rounded-2xl text-white font-bold flex items-center justify-center gap-2 shadow-lg shadow-indigo-200 group">
        <span>Launch Application</span>
        <i class="bi bi-rocket-takeoff transition-transform group-hover:scale-110"></i>
    </a>
</div>

<div id="installError" style="display: none;" class="text-center animate-entrance">
    <div class="mb-6 text-rose-500">
        <div class="mx-auto w-20 h-20 bg-rose-100 rounded-[28px] flex items-center justify-center text-rose-600 shadow-lg shadow-rose-100">
            <i class="bi bi-exclamation-octagon text-4xl"></i>
        </div>
    </div>
    <h4 class="text-xl font-bold text-slate-900 mb-2">Setup Interrupted</h4>
    <p id="errorMessage" class="text-slate-500 text-sm mb-8 italic">Something went wrong during the setup process.</p>
    
    <div class="flex flex-col gap-3">
        <a href="{{ route('installer.database') }}" class="w-full py-4 rounded-2xl bg-white border border-slate-200 text-slate-600 font-bold text-sm tracking-wide hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
            <i class="bi bi-arrow-left"></i>
            Return to Database Config
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
    const progressPercent = document.getElementById('progressPercent');

    // Simulate installation steps
    let progress = 0;
    const steps = [
        { text: 'Migrating Schema', detail: 'Creating core database tables', progress: 25 },
        { text: 'Seeding Data', detail: 'Populating initial application state', progress: 50 },
        { text: 'Optimizing Config', detail: 'Writing environment variables', progress: 75 },
        { text: 'Finalizing', detail: 'Clearing cache and warming services', progress: 90 }
    ];

    let currentStep = 0;

    function updateProgress() {
        if (currentStep < steps.length) {
            const step = steps[currentStep];
            statusText.textContent = step.text;
            statusDetail.textContent = step.detail;
            progressBar.style.width = step.progress + '%';
            progressPercent.textContent = step.progress + '%';
            currentStep++;
            setTimeout(updateProgress, 1200);
        } else {
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
                progressPercent.textContent = '100%';
                setTimeout(() => {
                    installProgress.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => {
                        installProgress.style.display = 'none';
                        installComplete.style.display = 'block';
                    }, 500);
                }, 800);
            } else {
                installProgress.style.display = 'none';
                errorMessage.textContent = data.message;
                installError.style.display = 'block';
            }
        })
        .catch(error => {
            installProgress.style.display = 'none';
            errorMessage.textContent = 'A critical error occurred: ' + error.message;
            installError.style.display = 'block';
        });
    }

    setTimeout(updateProgress, 800);
});
</script>
@endpush
