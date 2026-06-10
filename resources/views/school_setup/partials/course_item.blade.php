<li id="course-{{ $course->id }}" class="list-group-item d-flex justify-content-between align-items-center py-2">
    <span><strong>{{ $course->course_code }}</strong> — {{ $course->course_name }}</span>
    <div class="d-flex gap-1">
        <button type="button" class="btn btn-sm btn-warning ss-toolbar-btn"
                data-action="edit-course"
                data-id="{{ $course->id }}"
                data-code="{{ $course->course_code }}"
                data-name="{{ $course->course_name }}">
            Edit
        </button>
        <button type="button" class="btn btn-sm btn-danger ss-toolbar-btn"
                data-action="delete-course"
                data-id="{{ $course->id }}"
                data-code="{{ $course->course_code }}">
            Delete
        </button>
    </div>
</li>
