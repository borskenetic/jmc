@extends('layouts.public')

@section('title', 'Online registration')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/auth/register.css') }}">
@endpush

@section('content')
<div class="reg-page">
    <div class="reg-wrap">
        <header class="reg-hero">
            <img src="{{ asset('images/pantasLogo.png') }}" alt="{{ config('app.name') }}" class="reg-hero__logo">
            <div>
                <h1 class="reg-hero__title">Online registration</h1>
                <p class="reg-hero__subtitle">Fill out the form below. Staff will review and approve your account.</p>
            </div>
        </header>

        <div class="reg-sheet">
            @if(session('success'))
                <div class="reg-notice reg-notice--success" role="status">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="reg-notice reg-notice--error" role="alert">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="reg-notice reg-notice--error" role="alert">
                    <strong>Please fix the following:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="reg-tabs" role="tablist" aria-label="Registration type">
                <button type="button" class="reg-tabs__btn is-active" id="btnStudent" role="tab" aria-selected="true">Student</button>
                <button type="button" class="reg-tabs__btn" id="btnEmployee" role="tab" aria-selected="false">Employee</button>
            </div>

            <p class="text-center small text-muted mb-3">
                Visiting for the day?
                <a href="{{ route('visitors.register') }}">Register as a visitor</a> to get a QR pass for the gate terminal.
            </p>

            <form id="studentForm" class="reg-form" method="POST" action="{{ route('pending.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="reg-block">
                    <h2 class="reg-block__title">Personal information</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="student_firstname">First name</label>
                            <input type="text" id="student_firstname" name="firstname" class="form-control" value="{{ old('firstname') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="student_middle">Middle initial</label>
                            <input type="text" id="student_middle" name="middle_initial" class="form-control" value="{{ old('middle_initial') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="student_lastname">Last name</label>
                            <input type="text" id="student_lastname" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_id">Student ID</label>
                            <input type="text" id="student_id" name="student_id" class="form-control" value="{{ old('student_id') }}" placeholder="Leave blank if unknown">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_birth">Birth date</label>
                            <input type="date" id="student_birth" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_mobile">Mobile number</label>
                            <input type="text" id="student_mobile" name="mobile_number" class="form-control" value="{{ old('mobile_number') }}" required>
                        </div>
                    </div>
                </div>

                <div class="reg-block">
                    <h2 class="reg-block__title">School details</h2>
                    <div class="row g-3">
                        @include('students.partials.educational-fields', [
                            'programs' => $programs ?? collect(),
                            'schoolSetup' => $schoolSetup ?? [],
                            'educationalLevel' => old('educational_level'),
                            'year' => old('year'),
                            'course' => old('course'),
                            'section' => old('section'),
                            'sex' => old('sex'),
                        ])
                    </div>
                </div>

                <div class="reg-block">
                    <h2 class="reg-block__title">Emergency contact</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="student_emergency_name">Contact name</label>
                            <input type="text" id="student_emergency_name" name="emergency_person" class="form-control" value="{{ old('emergency_person') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_emergency_rel">Relationship</label>
                            <input type="text" id="student_emergency_rel" name="emergency_relationship" class="form-control" value="{{ old('emergency_relationship') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_emergency_num">Contact number</label>
                            <input type="text" id="student_emergency_num" name="emergency_number" class="form-control" value="{{ old('emergency_number') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_emergency_addr">Address</label>
                            <input type="text" id="student_emergency_addr" name="emergency_address" class="form-control" value="{{ old('emergency_address') }}">
                        </div>
                    </div>
                </div>

                <div class="reg-block">
                    <h2 class="reg-block__title">Photo &amp; signature</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="student_photo">Profile picture</label>
                            <div class="reg-notice reg-notice--warning" style="margin-bottom: 0.75rem;">
                                Upload a <strong>1×1 ID photo</strong> with a <strong>plain white background</strong>.
                            </div>
                            <input type="file" id="student_photo" name="profile_picture" class="form-control" accept=".jpg,.jpeg,.png">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="studentSignaturePad">Signature</label>
                            <div class="reg-signature-wrap">
                                <canvas id="studentSignaturePad" aria-label="Draw your signature"></canvas>
                            </div>
                            <input type="hidden" name="student_signature" id="studentSignatureInput">
                            <button type="button" id="clearStudentSignature" class="reg-btn-clear">Clear signature</button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="reg-submit">Submit student registration</button>
            </form>

            <form id="employeeForm" class="reg-form hidden" method="POST" action="{{ route('pendingEmployee.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="reg-block">
                    <h2 class="reg-block__title">Personal information</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="emp_firstname">First name</label>
                            <input type="text" id="emp_firstname" name="firstname" class="form-control" value="{{ old('firstname') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_lastname">Last name</label>
                            <input type="text" id="emp_lastname" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_department">Department</label>
                            <input type="text" id="emp_department" name="department" class="form-control" value="{{ old('department') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_position">Position</label>
                            <input type="text" id="emp_position" name="position" class="form-control" value="{{ old('position') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_id">Employee ID</label>
                            <input type="text" id="emp_id" name="employee_id" class="form-control" value="{{ old('employee_id') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_birth">Birth date</label>
                            <input type="date" id="emp_birth" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_sex">Sex</label>
                            <select id="emp_sex" name="sex" class="form-select" required>
                                <option value="">Select…</option>
                                <option value="MALE" @selected(old('sex') === 'MALE')>Male</option>
                                <option value="FEMALE" @selected(old('sex') === 'FEMALE')>Female</option>
                                <option value="OTHER" @selected(old('sex') === 'OTHER')>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_blood">Blood type</label>
                            <input type="text" id="emp_blood" name="blood_type" class="form-control" value="{{ old('blood_type') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_civil">Civil status</label>
                            <input type="text" id="emp_civil" name="civil_status" class="form-control" value="{{ old('civil_status') }}">
                        </div>
                    </div>
                </div>

                <div class="reg-block">
                    <h2 class="reg-block__title">Government IDs</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="emp_tin">TIN</label>
                            <input type="text" id="emp_tin" name="tin_id_number" class="form-control" value="{{ old('tin_id_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_philhealth">PhilHealth</label>
                            <input type="text" id="emp_philhealth" name="philhealth_number" class="form-control" value="{{ old('philhealth_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_sss">SSS</label>
                            <input type="text" id="emp_sss" name="sss_number" class="form-control" value="{{ old('sss_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_hdmf">HDMF</label>
                            <input type="text" id="emp_hdmf" name="hdmf_number" class="form-control" value="{{ old('hdmf_number') }}">
                        </div>
                    </div>
                </div>

                <div class="reg-block">
                    <h2 class="reg-block__title">Emergency contact</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="emp_emergency_name">Contact name</label>
                            <input type="text" id="emp_emergency_name" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_emergency_rel">Relationship</label>
                            <input type="text" id="emp_emergency_rel" name="emergency_contact_relationship" class="form-control" value="{{ old('emergency_contact_relationship') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emp_emergency_num">Contact number</label>
                            <input type="text" id="emp_emergency_num" name="emergency_contact_number" class="form-control" value="{{ old('emergency_contact_number') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="emp_address">Home address</label>
                            <textarea id="emp_address" name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="reg-block">
                    <h2 class="reg-block__title">Photo &amp; signature</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="emp_photo">Formal picture</label>
                            <input type="file" id="emp_photo" name="formal_picture" class="form-control" accept=".jpg,.jpeg,.png">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="employeeSignaturePad">Signature</label>
                            <div class="reg-signature-wrap">
                                <canvas id="employeeSignaturePad" aria-label="Draw your signature"></canvas>
                            </div>
                            <input type="hidden" name="employee_signature" id="employeeSignatureInput">
                            <button type="button" id="clearEmployeeSignature" class="reg-btn-clear">Clear signature</button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="reg-submit">Submit employee registration</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function setupSignaturePad(canvasId, inputId, clearBtnId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let points = [];

    function resizeCanvas() {
        const dataUrl = canvas.toDataURL();
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        const img = new Image();
        img.src = dataUrl;
        img.onload = () => ctx.drawImage(img, 0, 0);
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
    canvas.style.touchAction = 'none';

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: (clientX - rect.left) * scaleX,
            y: (clientY - rect.top) * scaleY
        };
    }

    function startDrawing(e) {
        e.preventDefault();
        drawing = true;
        points = [getPos(e)];
    }

    function draw(e) {
        e.preventDefault();
        if (!drawing) return;

        const pos = getPos(e);
        points.push(pos);
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#0f172a';

        if (points.length === 1) {
            ctx.beginPath();
            ctx.arc(pos.x, pos.y, 1.5, 0, Math.PI * 2);
            ctx.fill();
            return;
        }

        const last = points[points.length - 2];
        const dx = pos.x - last.x;
        const dy = pos.y - last.y;
        const speed = Math.sqrt(dx * dx + dy * dy);
        ctx.lineWidth = Math.max(1, 4 - speed / 2);
        ctx.beginPath();
        ctx.moveTo(last.x, last.y);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function stopDrawing() {
        if (!drawing) return;
        drawing = false;
        document.getElementById(inputId).value = canvas.toDataURL();
    }

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseleave', stopDrawing);
    canvas.addEventListener('touchstart', startDrawing);
    canvas.addEventListener('touchmove', draw);
    canvas.addEventListener('touchend', stopDrawing);

    document.getElementById(clearBtnId).addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        points = [];
        document.getElementById(inputId).value = '';
    });

    return { resize: resizeCanvas };
}

const studentPad = setupSignaturePad('studentSignaturePad', 'studentSignatureInput', 'clearStudentSignature');
const employeePad = setupSignaturePad('employeeSignaturePad', 'employeeSignatureInput', 'clearEmployeeSignature');

const btnStudent = document.getElementById('btnStudent');
const btnEmployee = document.getElementById('btnEmployee');
const studentForm = document.getElementById('studentForm');
const employeeForm = document.getElementById('employeeForm');

function showStudent() {
    studentForm.classList.remove('hidden');
    employeeForm.classList.add('hidden');
    btnStudent.classList.add('is-active');
    btnEmployee.classList.remove('is-active');
    btnStudent.setAttribute('aria-selected', 'true');
    btnEmployee.setAttribute('aria-selected', 'false');
    setTimeout(() => studentPad.resize(), 50);
}

function showEmployee() {
    employeeForm.classList.remove('hidden');
    studentForm.classList.add('hidden');
    btnEmployee.classList.add('is-active');
    btnStudent.classList.remove('is-active');
    btnEmployee.setAttribute('aria-selected', 'true');
    btnStudent.setAttribute('aria-selected', 'false');
    setTimeout(() => employeePad.resize(), 50);
}

btnStudent.addEventListener('click', showStudent);
btnEmployee.addEventListener('click', showEmployee);
</script>
@endsection
