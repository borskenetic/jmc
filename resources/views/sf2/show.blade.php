@extends('layouts.app')

@section('title', 'SF2 — ' . $report->titleLabel())

@section('content')
<div class="mb-3 d-flex flex-wrap justify-content-between align-items-start gap-2">
    <div>
        <a href="{{ route('sf2.index') }}" class="text-decoration-none small">&larr; SF2 list</a>
        <h4 class="mt-2 mb-1">{{ $report->school_name }}</h4>
        <p class="text-muted mb-0">
            {{ $report->grade_level }} — Section {{ $report->section }} ·
            {{ $report->reportMonthLabel() }} {{ $report->report_year }} ·
            {{ count($report->school_days ?? []) }} school days
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sf2.excel', $report) }}" class="btn btn-primary">Download Excel</a>
        <a href="{{ route('sf2.pdf', $report) }}" class="btn btn-outline-success">Download PDF</a>
        <a href="{{ route('sf2.edit', $report) }}" class="btn btn-primary">Edit</a>
        <form method="POST" action="{{ route('sf2.destroy', $report) }}" onsubmit="return confirm('Delete this SF2 report?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Delete</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3"><strong>School ID:</strong> {{ $report->school_id ?: '—' }}</div>
    <div class="col-md-3"><strong>School year:</strong> {{ $report->school_year }}</div>
    <div class="col-md-3"><strong>Teacher:</strong> {{ $report->teacher_name ?: '—' }}</div>
    <div class="col-md-3"><strong>School head:</strong> {{ $report->school_head_name ?: '—' }}</div>
</div>

@php
    $schoolDays = $report->school_days ?? [];
@endphp

<div class="card mb-4">
    <div class="card-header fw-semibold">Male learners ({{ count($grid['male']) }})</div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0" style="font-size:0.75rem">
            <thead>
                <tr>
                    <th>Name</th>
                    @foreach($schoolDays as $d)
                        <th class="text-center" style="min-width:1.5rem">{{ \Carbon\Carbon::parse($d)->format('j') }}</th>
                    @endforeach
                    <th>Abs</th>
                    <th>Tardy</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grid['male'] as $row)
                    <tr>
                        <td>{{ $row['student']->formattedName() }}</td>
                        @foreach($schoolDays as $d)
                            @php $m = $row['marks'][$d] ?? 'present'; @endphp
                            <td class="text-center">
                                @if($m === 'absent') x @elseif($m === 'tardy') T @endif
                            </td>
                        @endforeach
                        <td>{{ $row['absent_total'] }}</td>
                        <td>{{ $row['tardy_total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">Female learners ({{ count($grid['female']) }})</div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0" style="font-size:0.75rem">
            <thead>
                <tr>
                    <th>Name</th>
                    @foreach($schoolDays as $d)
                        <th class="text-center">{{ \Carbon\Carbon::parse($d)->format('j') }}</th>
                    @endforeach
                    <th>Abs</th>
                    <th>Tardy</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grid['female'] as $row)
                    <tr>
                        <td>{{ $row['student']->formattedName() }}</td>
                        @foreach($schoolDays as $d)
                            @php $m = $row['marks'][$d] ?? 'present'; @endphp
                            <td class="text-center">
                                @if($m === 'absent') x @elseif($m === 'tardy') T @endif
                            </td>
                        @endforeach
                        <td>{{ $row['absent_total'] }}</td>
                        <td>{{ $row['tardy_total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
