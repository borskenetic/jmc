@php
    use App\Enums\EducationalLevel;

    $selectedLevel = old('educational_level', $educationalLevel ?? '');
    $selectedYear = old('year', $year ?? '');
    $yearOptionsByLevel = config('patron.year_options', []);
    $programs = $programs ?? collect();
    $courseValue = old('course', $course ?? '');
    $isCollege = $selectedLevel === EducationalLevel::College->value;
@endphp

<div class="col-md-6">
    <label for="educational_level" class="form-label">Educational level <span class="text-danger">*</span></label>
    <select name="educational_level" id="educational_level"
            class="form-select @error('educational_level') is-invalid @enderror" required>
        <option value="">Select level…</option>
        @foreach (EducationalLevel::options() as $value => $label)
            <option value="{{ $value }}" {{ $selectedLevel === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error('educational_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-6" id="patron-course-college" @if(! $isCollege) hidden @endif>
    <label for="course" class="form-label">Course / program <span class="text-danger course-required-mark">*</span></label>
    @if($programs->isNotEmpty())
        <select id="course" class="form-select @error('course') is-invalid @enderror"
                @if($isCollege) name="course" required @endif>
            <option value="">Select course…</option>
            @foreach ($programs as $program)
                <option value="{{ $program->program_code }}"
                    {{ $courseValue == $program->program_code ? 'selected' : '' }}>
                    {{ $program->program_name }}
                </option>
            @endforeach
            @if($courseValue && ! $programs->contains('program_code', $courseValue))
                <option value="{{ $courseValue }}" selected>{{ $courseValue }} (current)</option>
            @endif
        </select>
    @else
        <input type="text" id="course" class="form-control @error('course') is-invalid @enderror"
               value="{{ $courseValue }}" placeholder="Course or program"
               @if($isCollege) name="course" required @endif>
    @endif
    @error('course')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-6" id="patron-course-general" @if($isCollege) hidden @endif>
    <label for="course_general" class="form-label">Section / strand</label>
    <input type="text" name="course_general" id="course_general" class="form-control @error('course') is-invalid @enderror"
           value="{{ $isCollege ? '' : $courseValue }}" placeholder="Optional (e.g. Section A, STEM)">
    @error('course')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-6">
    <label for="year" class="form-label">Year / grade level <span class="text-danger">*</span></label>
    <select name="year" id="year" class="form-select @error('year') is-invalid @enderror" required
            data-selected="{{ $selectedYear }}">
        <option value="">Select year…</option>
    </select>
    @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@push('scripts')
<script>
(function () {
    const levelSelect = document.getElementById('educational_level');
    const yearSelect = document.getElementById('year');
    const collegeCourse = document.getElementById('patron-course-college');
    const generalCourse = document.getElementById('patron-course-general');
    const courseSelect = document.getElementById('course');
    const courseGeneral = document.getElementById('course_general');
    const yearOptionsByLevel = @json($yearOptionsByLevel);

    if (!levelSelect || !yearSelect) return;

    function populateYears(level, keepValue) {
        const options = yearOptionsByLevel[level] || [];
        const current = keepValue ?? yearSelect.dataset.selected ?? yearSelect.value;
        yearSelect.innerHTML = '<option value="">Select year…</option>';
        let found = false;
        options.forEach(function (y) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            if (current && current === y) {
                opt.selected = true;
                found = true;
            }
            yearSelect.appendChild(opt);
        });
        if (current && !found) {
            const extra = document.createElement('option');
            extra.value = current;
            extra.textContent = current + ' (current)';
            extra.selected = true;
            yearSelect.appendChild(extra);
        }
    }

    function toggleCourseFields(level) {
        const isCollege = level === 'college';
        if (collegeCourse) collegeCourse.hidden = !isCollege;
        if (generalCourse) generalCourse.hidden = isCollege;
        if (courseSelect) {
            courseSelect.required = isCollege;
            courseSelect.disabled = !isCollege;
            if (isCollege) {
                courseSelect.setAttribute('name', 'course');
            } else {
                courseSelect.removeAttribute('name');
            }
        }
        if (courseGeneral) {
            courseGeneral.disabled = isCollege;
            if (!isCollege) {
                courseGeneral.setAttribute('name', 'course');
            } else {
                courseGeneral.removeAttribute('name');
            }
        }
    }

    levelSelect.addEventListener('change', function () {
        populateYears(this.value, '');
        toggleCourseFields(this.value);
    });

    populateYears(levelSelect.value, yearSelect.dataset.selected);
    toggleCourseFields(levelSelect.value);
})();
</script>
@endpush
