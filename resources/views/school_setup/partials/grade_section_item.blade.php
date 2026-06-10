<li class="list-group-item d-flex justify-content-between align-items-center py-2"
    id="grade-section-{{ $row->id }}"
    data-grade="{{ $row->grade_level }}"
    data-strand="{{ $row->strand }}">
    <span>{{ $row->section }}</span>
    <button type="button" class="btn btn-sm btn-outline-danger ss-remove-btn"
            data-action="remove-section"
            data-id="{{ $row->id }}"
            title="Remove section"
            aria-label="Remove section">&times;</button>
</li>
