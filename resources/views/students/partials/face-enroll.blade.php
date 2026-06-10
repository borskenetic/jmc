<div class="card mt-4 face-enroll-panel" id="faceEnrollPanel"
     data-store-url="{{ route('students.face.store', $student) }}"
     data-destroy-url="{{ route('students.face.destroy', $student) }}"
     data-csrf="{{ csrf_token() }}"
     data-enrolled="{{ $student->hasFaceEnrolled() ? '1' : '0' }}"
     data-face-model-cdn="{{ config('face.model_cdn') }}">
    <div class="card-header">
        <strong>Face recognition (optional)</strong>
    </div>
    <div class="card-body">
        <p class="small text-muted mb-3">
            Capture the student's face once for the <a href="{{ route('attendance.face') }}" target="_blank" rel="noopener">face attendance kiosk</a>.
            Uses the device camera; works best in good lighting, face forward.
        </p>
        @if($student->hasFaceEnrolled())
            <p class="small text-success mb-2">
                Enrolled {{ $student->face_enrolled_at?->format('M j, Y g:i A') ?? 'yes' }}.
            </p>
        @endif
        <div class="face-enroll-preview mb-2">
            <video id="faceEnrollVideo" autoplay muted playsinline></video>
            <canvas id="faceEnrollCanvas"></canvas>
        </div>
        <p id="faceEnrollStatus" class="small text-muted mb-2">Starting camera…</p>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-primary btn-sm" id="faceEnrollCapture">Capture &amp; enroll face</button>
            <button type="button" class="btn btn-outline-danger btn-sm" id="faceEnrollRemove"
                @if(!$student->hasFaceEnrolled()) hidden @endif>Remove enrollment</button>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/attendance/face-scan.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/dist/face-api.js"></script>
    <script src="{{ \App\Support\VersionedAsset::url('js/face-api-common.js') }}"></script>
    <script src="{{ \App\Support\VersionedAsset::url('js/face-enroll.js') }}"></script>
@endpush
