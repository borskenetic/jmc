@extends('layouts.app')

@section('title', 'School Form 2 (SF2)')

@section('content')
<div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h4 class="mb-1">School Form 2 (SF2)</h4>
        <p class="text-muted small mb-0">Daily attendance report of learners — manual entry, DepEd PDF export.</p>
    </div>
    <a href="{{ route('sf2.create') }}" class="btn btn-primary">Create SF2 report</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>School</th>
                    <th>Grade / Section</th>
                    <th>Month</th>
                    <th>Learners</th>
                    <th>Updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->school_name }}</td>
                        <td>{{ $report->grade_level }} — {{ $report->section }}</td>
                        <td>{{ $report->reportMonthLabel() }} {{ $report->report_year }}</td>
                        <td>{{ $report->students_count }}</td>
                        <td>{{ $report->updated_at->format('M j, Y') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('sf2.show', $report) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            <a href="{{ route('sf2.edit', $report) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="{{ route('sf2.excel', $report) }}" class="btn btn-sm btn-primary">Excel</a>
                            <a href="{{ route('sf2.pdf', $report) }}" class="btn btn-sm btn-outline-success">PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No SF2 reports yet. Create one to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())
        <div class="card-footer">{{ $reports->links() }}</div>
    @endif
</div>
@endsection
