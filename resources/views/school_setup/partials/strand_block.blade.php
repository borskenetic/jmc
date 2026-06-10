@php
    use Illuminate\Support\Str;

    $sectionListId = 'grade-sections-'.Str::slug($grade.'-'.$strand);
    $sections = $sections ?? collect();
@endphp
<div class="border-bottom px-3 py-2 strand-block" data-strand="{{ $strand }}">
    <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
        <span class="fw-semibold">{{ $strand }}</span>
        <div class="d-flex gap-1 flex-shrink-0">
            <button type="button" class="btn btn-sm btn-primary ss-add-section-btn"
                    data-action="add-section"
                    data-grade="{{ $grade }}"
                    data-strand="{{ $strand }}"
                    title="Add section under {{ $strand }}">+ Section</button>
            <button type="button" class="btn btn-sm btn-outline-danger ss-remove-btn"
                    data-action="remove-strand"
                    data-strand-id="{{ $strandId }}"
                    data-strand="{{ $strand }}"
                    title="Remove strand and all its sections"
                    aria-label="Remove strand">&times;</button>
        </div>
    </div>
    <ul class="list-group list-group-flush grade-section-list mb-0"
        id="{{ $sectionListId }}"
        data-grade="{{ $grade }}"
        data-strand="{{ $strand }}">
        @forelse($sections as $row)
            @include('school_setup.partials.grade_section_item', ['row' => $row])
        @empty
            <li class="list-group-item text-muted small section-empty py-1 border-0 px-0">No sections</li>
        @endforelse
    </ul>
</div>
