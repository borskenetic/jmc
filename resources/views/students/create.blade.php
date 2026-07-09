@extends('layouts.sec')

@section('title', 'Register Student')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/layout/data-pages.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush

@section('content')
<div class="data-page student-form-page mt-2">
    <header class="student-edit-header">
        <div>
            <a href="{{ route('students.index') }}" class="student-edit-back">&larr; Back to students</a>
            <h1 class="student-edit-title">Register student</h1>
            <p class="student-edit-subtitle">Creates a patron record for attendance scanning and ID cards. QR code is assigned automatically.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="studentForm" action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4 student-edit-layout">
            <aside class="col-lg-4">
                <div class="student-edit-panel student-edit-identity">
                    <div class="student-edit-panel__head">Identity</div>
                    <div class="student-edit-panel__body text-center">
                        <div class="student-edit-avatar-wrap">
                            <div class="student-edit-avatar student-edit-avatar--empty" id="profilePreviewPlaceholder">?</div>
                            <img src="" alt="" class="student-edit-avatar d-none" id="profilePreview">
                        </div>

                        <div class="student-edit-field text-start">
                            <label class="form-label">QR code</label>
                            <input type="text" class="form-control form-control-sm bg-light font-monospace" value="Assigned on save" readonly>
                            <p class="student-edit-hint">Generated automatically (e.g. S-00000001). Used at the gate if RFID is empty.</p>
                        </div>

                        <div class="student-edit-field text-start">
                            <label for="rfid" class="form-label">RFID tag</label>
                            <input type="text" name="rfid" id="rfid" class="form-control form-control-sm @error('rfid') is-invalid @enderror"
                                   value="{{ old('rfid') }}" placeholder="Scan or type tag"
                                   autocomplete="off">
                            @error('rfid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <p class="student-edit-hint">Optional. Primary identifier at the gate scanner when set.</p>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="col-lg-8">
                <div class="student-edit-panel">
                    <div class="student-edit-panel__head">Personal details</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="firstname" class="form-label">First name <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror"
                                       value="{{ old('firstname') }}" required>
                                @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="lastname" class="form-label">Last name <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror"
                                       value="{{ old('lastname') }}" required>
                                @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="midname" class="form-label">Middle name</label>
                                <input type="text" name="midname" id="midname" class="form-control"
                                       value="{{ old('midname') }}" maxlength="255" placeholder="Optional">
                            </div>
                            <div class="col-md-6">
                                <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                                <input type="text" name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror"
                                       value="{{ old('student_id') }}" placeholder="School ID number" required>
                                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="lrn" class="form-label">LRN</label>
                                <input type="text" name="lrn" id="lrn" class="form-control @error('lrn') is-invalid @enderror"
                                       value="{{ old('lrn') }}" placeholder="Learner reference number">
                                @error('lrn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label">Birthday</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                                       value="{{ old('birth_date') }}">
                                @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mobile_number" class="form-label">Mobile number</label>
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                                       value="{{ old('mobile_number') }}" placeholder="09XXXXXXXXX">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Home address</label>
                                <input type="text" name="address" id="address" class="form-control"
                                       value="{{ old('address') }}" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-edit-panel">
                    <div class="student-edit-panel__head">School enrollment</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-3">
                            @include('students.partials.educational-fields', [
                                'programs' => $programs,
                                'schoolSetup' => $schoolSetup ?? [],
                                'educationalLevel' => old('educational_level'),
                                'year' => old('year'),
                                'course' => old('course'),
                                'section' => old('section'),
                                'sex' => old('sex'),
                            ])
                        </div>
                    </div>
                </div>

                <div class="student-edit-panel">
                    <div class="student-edit-panel__head">Emergency contact</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="emergency_person" class="form-label">Contact name</label>
                                <input type="text" name="emergency_person" id="emergency_person" class="form-control"
                                       value="{{ old('emergency_person') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="emergency_relationship" class="form-label">Relationship</label>
                                <input type="text" name="emergency_relationship" id="emergency_relationship" class="form-control"
                                       value="{{ old('emergency_relationship') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="emergency_number" class="form-label">Contact number</label>
                                <input type="text" name="emergency_number" id="emergency_number" class="form-control"
                                       value="{{ old('emergency_number') }}">
                            </div>
                            <div class="col-12">
                                <label for="emergency_address" class="form-label">Emergency address</label>
                                <textarea name="emergency_address" id="emergency_address" class="form-control" rows="2"
                                          placeholder="Optional">{{ old('emergency_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-edit-panel">
                    <div class="student-edit-panel__head">Photo &amp; signature</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="profile_picture" class="form-label">Profile picture</label>
                                <label class="student-edit-upload">
                                    <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/png,image/jpg">
                                    <span class="student-edit-upload__label">Choose photo</span>
                                    <span class="student-edit-upload__hint">1×1 ID photo preferred. JPG or PNG, max 4 MB.</span>
                                </label>
                                @error('profile_picture')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Signature</label>
                                <div class="signature-wrap">
                                    <canvas id="studentSignaturePad"></canvas>
                                </div>
                                <input type="hidden" name="student_signature" id="studentSignatureInput" value="{{ old('student_signature') }}">
                                <button type="button" id="clearStudentSignature" class="btn btn-sm btn-outline-secondary mt-2">Clear signature</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-edit-actions">
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Register student</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const firstnameInput = document.getElementById('firstname');
    const fileInput = document.getElementById('profile_picture');
    const preview = document.getElementById('profilePreview');
    const placeholder = document.getElementById('profilePreviewPlaceholder');

    function updateInitial() {
        if (!placeholder || preview?.src) return;
        const letter = (firstnameInput?.value || '').trim().charAt(0).toUpperCase();
        placeholder.textContent = letter || '?';
    }

    firstnameInput?.addEventListener('input', updateInitial);
    updateInitial();

    fileInput?.addEventListener('change', function () {
        const file = fileInput.files?.[0];
        if (!file || !preview) return;
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
        placeholder?.classList.add('d-none');
    });

    const canvas = document.getElementById('studentSignaturePad');
    const input = document.getElementById('studentSignatureInput');
    if (!canvas || typeof SignaturePad === 'undefined') return;

    const signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const data = signaturePad.isEmpty() ? null : signaturePad.toData();
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = 150 * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        canvas.style.width = '100%';
        canvas.style.height = '150px';
        signaturePad.clear();
        if (data) signaturePad.fromData(data);
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    document.getElementById('clearStudentSignature')?.addEventListener('click', () => {
        signaturePad.clear();
        input.value = '';
    });

    document.getElementById('studentForm')?.addEventListener('submit', () => {
        if (!signaturePad.isEmpty()) {
            input.value = signaturePad.toDataURL();
        }
    });
})();
</script>
@endsection
