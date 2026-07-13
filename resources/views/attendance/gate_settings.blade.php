@extends('layouts.sec')

@section('content')
<div class="container py-4" style="max-width: 820px;">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="mb-0">Gates</h3>
        <a href="{{ route('attendance.scan') }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
            Open gate terminal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('attendance.gate.settings.update') }}">
        @csrf

        <div class="card mb-4">
            <div class="card-body">
                <p class="text-muted mb-0">
                    Define the gates around campus. Each gate terminal picks one gate on first use;
                    gates already in use on another kiosk are hidden from the list until released or timed out.
                </p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fw-semibold">Gate list</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addGateRow">+ Add gate</button>
            </div>
            <div class="card-body">
                <div id="gateRows" class="d-flex flex-column gap-2 mb-2">
                    @foreach(old('gates', $gates) as $gateName)
                        <div class="gate-row input-group">
                            <span class="input-group-text text-muted">⋮⋮</span>
                            <input type="text" name="gates[]" class="form-control"
                                   value="{{ $gateName }}" maxlength="120" required
                                   placeholder="e.g. Main Gate">
                            <button type="button" class="btn btn-outline-danger remove-gate-row" title="Remove">×</button>
                        </div>
                    @endforeach
                </div>
                <p class="small text-muted mb-0">Keep at least one gate.</p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save gates</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const rows = document.getElementById('gateRows');
    const addBtn = document.getElementById('addGateRow');
    if (!rows || !addBtn) return;

    function bindRemove(btn) {
        btn.addEventListener('click', () => {
            if (rows.querySelectorAll('.gate-row').length <= 1) {
                alert('You need at least one gate.');
                return;
            }
            btn.closest('.gate-row')?.remove();
        });
    }

    rows.querySelectorAll('.remove-gate-row').forEach(bindRemove);

    addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'gate-row input-group';
        row.innerHTML = `
            <span class="input-group-text text-muted">⋮⋮</span>
            <input type="text" name="gates[]" class="form-control" maxlength="120" required placeholder="Gate name">
            <button type="button" class="btn btn-outline-danger remove-gate-row" title="Remove">×</button>
        `;
        rows.appendChild(row);
        bindRemove(row.querySelector('.remove-gate-row'));
        row.querySelector('input')?.focus();
    });
})();
</script>
@endsection
