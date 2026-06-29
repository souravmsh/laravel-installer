@extends('installer::layout')

@section('title', 'Installing')
@section('subtitle', 'Running migrations and finalizing your setup.')

@section('content')

{{-- Progress state --}}
<div id="stateProgress">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.3rem">
        <span id="statusText" style="font-size:.76rem;font-weight:600;color:var(--text)">Preparing…</span>
        <span id="progressPct" style="font-size:.72rem;font-weight:700;color:var(--accent)">0%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" id="progressBar" style="width:0%"></div>
    </div>
    <div id="statusDetail" style="font-size:.68rem;color:var(--muted);margin-top:.2rem">Starting up…</div>

    <div class="terminal" id="consoleLog" style="margin-top:.9rem">
        <span class="t-dim">&gt;</span> <span class="t-info">Initializing installer…</span>
    </div>
</div>

{{-- Success state --}}
<div id="stateSuccess" style="display:none;text-align:center;padding:1.5rem 0">
    <div class="complete-icon success"><i class="bi bi-check-lg"></i></div>
    <div style="font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:.3rem">Database Installed</div>
    <div style="font-size:.76rem;color:var(--muted)">All migrations and seeders completed successfully.</div>
</div>

{{-- Error state --}}
<div id="stateError" style="display:none;text-align:center;padding:1.5rem 0">
    <div class="complete-icon error"><i class="bi bi-x-lg"></i></div>
    <div style="font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:.5rem">Installation Failed</div>
    <div id="errorMessage" class="installer-alert danger" style="text-align:left;display:inline-flex;max-width:380px">
        <i class="bi bi-x-circle-fill"></i><div>An error occurred.</div>
    </div>
</div>

@endsection

@section('footer')
    {{-- During install --}}
    <div id="footerInstall" style="width:100%;display:flex;justify-content:flex-end">
        <button disabled class="btn-primary-custom" style="opacity:.5;cursor:not-allowed">
            <span class="spinner-border spinner-border-sm" style="width:11px;height:11px;border-width:2px"></span>
            Installing…
        </button>
    </div>

    {{-- After success --}}
    <div id="footerSuccess" style="width:100%;display:none;justify-content:flex-end">
        <a href="{{ route('installer.complete') }}" class="btn-primary-custom">
            Finish <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    {{-- After error --}}
    <div id="footerError" style="width:100%;display:none;justify-content:space-between;align-items:center">
        <a href="{{ route('installer.database') }}" class="btn-ghost">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <button disabled class="btn-primary-custom" style="opacity:.4;cursor:not-allowed">Continue</button>
    </div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function () {
    const bar        = document.getElementById('progressBar');
    const pct        = document.getElementById('progressPct');
    const statusText = document.getElementById('statusText');
    const statusDet  = document.getElementById('statusDetail');
    const log        = document.getElementById('consoleLog');

    function addLog(msg, cls = 't-ok') {
        log.innerHTML += `\n<div><span class="t-dim">&gt;</span> <span class="${cls}">${msg}</span></div>`;
        log.scrollTop = log.scrollHeight;
    }

    function setProgress(p, text, detail) {
        bar.style.width = p + '%';
        pct.textContent = p + '%';
        statusText.textContent = text;
        statusDet.textContent  = detail;
    }

    function showState(state) {
        ['Progress', 'Success', 'Error'].forEach(s => {
            document.getElementById('state' + s).style.display = 'none';
            document.getElementById('footer' + (s === 'Progress' ? 'Install' : s)).style.display = 'none';
        });
        document.getElementById('state'  + state).style.display = state === 'Success' || state === 'Error' ? 'block' : 'block';
        const fKey = state === 'Progress' ? 'footerInstall' : ('footer' + state);
        document.getElementById(fKey).style.display = 'flex';
    }

    const preSteps = [
        { p: 20, text: 'Finalizing configuration', detail: 'Saving .env settings…', log: ['Merging configuration…', 't-info'] },
        { p: 45, text: 'Preparing database',       detail: 'Connecting to server…',  log: ['Connected to database.', 't-ok'] },
    ];

    let idx = 0;
    function runPreStep() {
        if (idx >= preSteps.length) { runInstall(); return; }
        const s = preSteps[idx++];
        setProgress(s.p, s.text, s.detail);
        addLog(s.log[0], s.log[1]);
        setTimeout(runPreStep, 1100);
    }

    function runInstall() {
        setProgress(70, 'Running migrations', 'Setting up database tables…');
        addLog('Running: php artisan migrate --force', 't-info');

        fetch('{{ route('installer.install.process') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                setProgress(100, 'Complete', 'Installation finished.');
                addLog('Migrations completed.', 't-ok');
                addLog('Seeders executed.', 't-ok');
                addLog('Installation lock created.', 't-ok');
                setTimeout(() => showState('Success'), 700);
            } else {
                addLog(data.message || 'Unknown error.', 't-err');
                document.getElementById('errorMessage').innerHTML =
                    `<i class="bi bi-x-circle-fill"></i><div>${data.message}</div>`;
                setTimeout(() => showState('Error'), 400);
            }
        })
        .catch(() => {
            addLog('Unexpected error during installation.', 't-err');
            document.getElementById('errorMessage').innerHTML =
                `<i class="bi bi-x-circle-fill"></i><div>Unexpected error. Check server logs.</div>`;
            setTimeout(() => showState('Error'), 400);
        });
    }

    setTimeout(runPreStep, 600);
});
</script>
@endpush
