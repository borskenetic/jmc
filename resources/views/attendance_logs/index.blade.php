@extends('layouts.app')

@section('title', 'Attendance Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance_logs/logs.css') }}">
@endpush

@section('content')
@php
    $query = request()->query();
    $hasFilters = collect($query)->except('page')->filter()->isNotEmpty();
@endphp

<div class="data-page attendance-logs-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-2">
        <div>
            <h4 class="mb-1">Attendance Logs</h4>
            <p class="text-muted small mb-0">School-wide scan history. Filter by date, grade, section, or status.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('attendance.scan') }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                Open Attendance Scanner
            </a>
            <a href="{{ route('attendance_logs.reports.hub') }}" class="btn btn-outline-secondary btn-sm">Reports</a>
            <a href="{{ route('attendance_logs.export.excel', $query) }}" class="btn btn-outline-secondary btn-sm">Excel</a>
            <a href="{{ route('attendance_logs.export.pdf', $query) }}" class="btn btn-outline-secondary btn-sm">PDF</a>
        </div>
    </div>

    <div class="al-summary">
        <span class="al-chip"><strong>{{ number_format($summary['total']) }}</strong> matching</span>
        <span class="al-chip al-chip--in"><strong>{{ number_format($summary['in']) }}</strong> IN</span>
        <span class="al-chip al-chip--out"><strong>{{ number_format($summary['out']) }}</strong> OUT</span>
        @if($summary['today'] > 0)
            <span class="al-chip"><strong>{{ number_format($summary['today']) }}</strong> today</span>
        @endif
    </div>

    <div class="card al-filter-card mb-3 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           value="{{ request('search') }}" placeholder="Name or student ID…">
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label">From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label">To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <label class="form-label">Grade</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">All grades</option>
                        @foreach($yearOptions as $year)
                            <option value="{{ $year }}" @selected(request('year') === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <label class="form-label">Section</label>
                    <select name="homeroom_section" class="form-select form-select-sm">
                        <option value="">All sections</option>
                        @foreach($homeroomSections as $section)
                            <option value="{{ $section }}" @selected(request('homeroom_section') === $section)>{{ $section }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-4 col-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="IN" @selected(strtoupper((string) request('status')) === 'IN')>IN</option>
                        <option value="OUT" @selected(strtoupper((string) request('status')) === 'OUT')>OUT</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 btn-search-filter">Apply</button>
                    @if($hasFilters)
                        <a href="{{ route('attendance_logs.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if($hasFilters)
                <div class="al-active-filters">
                    @if(request('search'))
                        <span class="badge text-bg-light border">Search: {{ request('search') }}</span>
                    @endif
                    @if(request('from') || request('to'))
                        <span class="badge text-bg-light border">
                            {{ request('from') ?: '…' }} → {{ request('to') ?: '…' }}
                        </span>
                    @endif
                    @if(request('year'))
                        <span class="badge text-bg-light border">{{ request('year') }}</span>
                    @endif
                    @if(request('homeroom_section'))
                        <span class="badge text-bg-light border">{{ request('homeroom_section') }}</span>
                    @endif
                    @if(request('status'))
                        <span class="badge text-bg-light border">{{ strtoupper(request('status')) }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 al-table">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th>Scanned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $student = $log->student;
                            $status = strtoupper((string) $log->status);
                        @endphp
                        <tr>
                            <td>
                                @if($student)
                                    <div class="al-student-name">{{ $student->lastname }}, {{ $student->firstname }}</div>
                                    @if($student->student_id)
                                        <div class="al-student-meta">{{ $student->student_id }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td>{{ $student?->year ?? '—' }}</td>
                            <td>{{ $student?->section ?? '—' }}</td>
                            <td>
                                @if($status === 'IN')
                                    <span class="badge badge-in">IN</span>
                                @elseif($status === 'OUT')
                                    <span class="badge badge-out">OUT</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ $status ?: '—' }}</span>
                                @endif
                            </td>
                            <td class="al-time">
                                {{ $log->scanned_at?->timezone(config('app.timezone'))->format('M j, Y g:i A') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                No attendance records match your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
