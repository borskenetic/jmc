<div id="sectionModal" class="section-modal" aria-hidden="true">
  <div class="modal-content section-picker-modal">
    <h2>Select section</h2>
    <div class="section-buttons" id="sectionButtons" data-count="{{ count($attendanceSections ?? []) }}">
      @forelse($attendanceSections ?? [] as $section)
        <button type="button" data-section="{{ $section }}">{{ $section }}</button>
      @empty
        <p class="section-empty-msg">No sections configured.</p>
      @endforelse
    </div>
  </div>
</div>

<div id="feedbackModal" class="section-modal" aria-hidden="true">
  <div class="modal-content feedback-card">
    <h2>How was your experience?</h2>
    <div class="feedback-options">
      <button type="button" data-rating="excellent">😊<span>Excellent</span></button>
      <button type="button" data-rating="good">🙂<span>Good</span></button>
      <button type="button" data-rating="medium">😐<span>Medium</span></button>
      <button type="button" data-rating="poor">🙁<span>Poor</span></button>
      <button type="button" data-rating="very_bad">😠<span>Very Bad</span></button>
    </div>
    <button type="button" id="declineFeedback" class="decline-btn">Skip</button>
  </div>
</div>
