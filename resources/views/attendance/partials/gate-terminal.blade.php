@php
  $gateList = $gateTerminals ?? [];
  $gateCount = count($gateList);
@endphp
<div id="gateTerminalModal" class="section-modal gate-terminal-modal" aria-hidden="true">
  <div class="modal-content section-picker-modal">
    <h2>Which gate is this terminal?</h2>
    <p class="gate-terminal-modal__hint">Choose the gate for this kiosk. Gates already used on another terminal are not shown.</p>
    <div class="section-buttons gate-terminal-buttons" id="gateTerminalButtons" data-count="{{ $gateCount }}">
      @forelse($gateList as $gateName)
        <button type="button" data-gate="{{ $gateName }}">{{ $gateName }}</button>
      @empty
      @endforelse
    </div>
    <p class="section-empty-msg" id="gateTerminalEmpty" @if($gateCount > 0) hidden @endif>
      No gates available. Add gates under Attendance → Gates, or wait if all are in use.
    </p>
    <p class="gate-terminal-modal__loading" id="gateTerminalLoading" hidden>Loading gates…</p>
  </div>
</div>
<script>
(function () {
  var modal = document.getElementById('gateTerminalModal');
  if (!modal) return;
  try {
    if (!localStorage.getItem('attendance_gate_terminal')) {
      modal.style.display = 'flex';
      modal.setAttribute('aria-hidden', 'false');
    }
  } catch (e) {
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
  }
})();
</script>
