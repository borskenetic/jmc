@extends('layouts.sec')

@section('title', 'Register Employee')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/layout/data-pages.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush

@section('content')
<div class="data-page employee-form-page mt-2">
    <header class="student-edit-header">
        <div>
            <a href="{{ route('employees.index') }}" class="student-edit-back">&larr; Back to employees</a>
            <h1 class="student-edit-title">Register employee</h1>
            <p class="student-edit-subtitle">Staff record for attendance scanning and ID generation. QR code is assigned when an employee ID is set.</p>
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

    <form id="employeeForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
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
                            <input type="text" class="form-control form-control-sm bg-light font-monospace" value="E-{employee ID}" readonly>
                            <p class="student-edit-hint">Generated as E- plus employee ID when saved. Used at the gate if RFID is empty.</p>
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
                    <div class="student-edit-panel__head">Work information</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">Employee ID</label>
                                <input type="text" name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                                       value="{{ old('employee_id') }}" placeholder="e.g. EMP-2024-001">
                                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="employee_number" class="form-label">Employee number</label>
                                <input type="text" name="employee_number" id="employee_number" class="form-control"
                                       value="{{ old('employee_number') }}" placeholder="Optional">
                            </div>
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">First name <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror"
                                       value="{{ old('firstname') }}" required>
                                @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Last name <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror"
                                       value="{{ old('lastname') }}" required>
                                @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" name="department" id="department" class="form-control"
                                       value="{{ old('department') }}" placeholder="e.g. Library Services">
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" name="position" id="position" class="form-control"
                                       value="{{ old('position') }}" placeholder="e.g. Librarian">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-edit-panel">
                    <div class="student-edit-panel__head">Personal details</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="birth_date" class="form-label">Birth date</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="sex" class="form-label">Sex</label>
                                <select name="sex" id="sex" class="form-select">
                                    <option value="">Select…</option>
                                    <option value="Male" @selected(old('sex') === 'Male')>Male</option>
                                    <option value="Female" @selected(old('sex') === 'Female')>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="civil_status" class="form-label">Civil status</label>
                                <input type="text" name="civil_status" id="civil_status" class="form-control"
                                       value="{{ old('civil_status') }}" placeholder="e.g. Single">
                            </div>
                            <div class="col-md-4">
                                <label for="blood_type" class="form-label">Blood type</label>
                                <input type="text" name="blood_type" id="blood_type" class="form-control"
                                       value="{{ old('blood_type') }}" placeholder="e.g. O+">
                            </div>
                            <div class="col-md-8">
                                <label for="address" class="form-label">Home address</label>
                                <textarea name="address" id="address" class="form-control" rows="2" placeholder="Optional">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-edit-panel">
                    <div class="student-edit-panel__head">Government &amp; benefits IDs</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tin_id_number" class="form-label">TIN</label>
                                <input type="text" name="tin_id_number" id="tin_id_number" class="form-control" value="{{ old('tin_id_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="philhealth_number" class="form-label">PhilHealth</label>
                                <input type="text" name="philhealth_number" id="philhealth_number" class="form-control" value="{{ old('philhealth_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="sss_number" class="form-label">SSS</label>
                                <input type="text" name="sss_number" id="sss_number" class="form-control" value="{{ old('sss_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="hdmf_number" class="form-label">HDMF</label>
                                <input type="text" name="hdmf_number" id="hdmf_number" class="form-control" value="{{ old('hdmf_number') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-edit-panel">
                    <div class="student-edit-panel__head">Emergency contact</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="emergency_contact_name" class="form-label">Contact name</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control"
                                       value="{{ old('emergency_contact_name') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                                <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" class="form-control"
                                       value="{{ old('emergency_contact_relationship') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="emergency_contact_number" class="form-label">Contact number</label>
                                <input type="text" name="emergency_contact_number" id="emergency_contact_number" class="form-control"
                                       value="{{ old('emergency_contact_number') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-edit-panel">
                    <div class="student-edit-panel__head">Photo &amp; signature</div>
                    <div class="student-edit-panel__body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="formal_picture" class="form-label">Formal picture</label>
                                <label class="student-edit-upload">
                                    <input type="file" name="formal_picture" id="formal_picture" accept="image/jpeg,image/png,image/jpg">
                                    <span class="student-edit-upload__label">Choose photo</span>
                                    <span class="student-edit-upload__hint">1×1 or formal ID photo. JPG or PNG, max 2 MB.</span>
                                </label>
                                @error('formal_picture')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Signature</label>
                                <div class="signature-wrap">
                                    <canvas id="employeeSignaturePad"></canvas>
                                </div>
                                <input type="hidden" name="employee_signature" id="employeeSignatureInput">
                                <button type="button" id="clearEmployeeSignature" class="btn btn-sm btn-outline-secondary mt-2">Clear signature</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-edit-actions">
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Register employee</button>
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
    const fileInput = document.getElementById('formal_picture');
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

    const canvas = document.getElementById('employeeSignaturePad');
    const input = document.getElementById('employeeSignatureInput');
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

    document.getElementById('clearEmployeeSignature')?.addEventListener('click', () => {
        signaturePad.clear();
        input.value = '';
    });

    document.getElementById('employeeForm')?.addEventListener('submit', () => {
        if (!signaturePad.isEmpty()) {
            input.value = signaturePad.toDataURL();
        }
    });
})();
</script>
@endsection
