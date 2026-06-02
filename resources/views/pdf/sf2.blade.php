<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>School Form 2 — {{ $report->grade_level }} {{ $report->section }}</title>
    <style>
        @page { size: A4 landscape; margin: 6mm 5mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #000; margin: 0; }
        table { border-collapse: collapse; width: 100%; }
        .header-table td { border: 1px solid #000; padding: 2px 4px; vertical-align: middle; }
        .header-label { font-weight: bold; white-space: nowrap; width: 1%; }
        .form-title { text-align: center; font-weight: bold; font-size: 9px; line-height: 1.2; }
        .form-sub { text-align: center; font-size: 7px; }
        .grid-table { margin-top: 4px; table-layout: fixed; }
        .grid-table th, .grid-table td { border: 1px solid #000; padding: 0; text-align: center; vertical-align: middle; }
        .grid-table .name-head { width: 22%; text-align: left; padding: 2px 3px; font-size: 6.5px; }
        .grid-table .name-cell { text-align: left; padding: 1px 3px; font-size: 6.5px; height: 14px; overflow: hidden; }
        .grid-table .day-head { width: 2.1%; font-size: 5.5px; font-weight: normal; }
        .grid-table .dow-head { font-size: 5px; }
        .grid-table .total-head { width: 3%; font-size: 6px; }
        .grid-table .total-cell { font-size: 6.5px; font-weight: bold; }
        .day-cell { position: relative; height: 14px; width: 14px; padding: 0; }
        .cell-diagonal {
            position: absolute; left: 0; top: 0; width: 100%; height: 100%;
            background: linear-gradient(to top right, transparent 49%, #999 50%, transparent 51%);
        }
        .mark-x { position: relative; z-index: 1; font-weight: bold; font-size: 8px; }
        .mark-tardy {
            position: absolute; left: 0; top: 0; width: 100%; height: 50%;
            background: #bbb; z-index: 1;
        }
        .section-label-cell { text-align: left; padding: 2px 4px; font-size: 6.5px; background: #f0f0f0; }
        .total-row .name-cell { font-weight: bold; font-size: 6px; }
        .total-row .total-num { font-weight: bold; font-size: 6.5px; }
        .remarks-head { width: 8%; font-size: 6px; }
        .page-break { page-break-before: always; }
        .page2 { font-size: 6.5px; }
        .page2 h3 { font-size: 8px; margin: 0 0 4px; text-align: center; }
        .page2-col { width: 48%; vertical-align: top; padding: 4px; }
        .page2-box { border: 1px solid #000; padding: 4px; margin-bottom: 6px; }
        .summary-table td, .summary-table th { border: 1px solid #000; padding: 2px 4px; font-size: 6.5px; }
        .sig-line { border-bottom: 1px solid #000; margin-top: 20px; min-height: 14px; }
        .sig-label { font-size: 6.5px; margin-top: 2px; }
    </style>
</head>
<body>

@php
    $padded = $grid['padded_columns'];
    $schoolDays = $report->school_days ?? [];
    $dayCount = count($schoolDays);
@endphp

{{-- Page 1 --}}
<table class="header-table" style="width:100%">
    <tr>
        <td rowspan="3" style="width:12%; text-align:center; font-size:6px;">DepEd</td>
        <td colspan="4" class="form-title">School Form 2 (SF2)<br>Daily Attendance Report of Learners</td>
        <td rowspan="3" style="width:12%; text-align:center; font-size:6px;">DepEd</td>
    </tr>
    <tr>
        <td class="header-label">School ID</td>
        <td>{{ $report->school_id }}</td>
        <td class="header-label">School Year</td>
        <td>{{ $report->school_year }}</td>
    </tr>
    <tr>
        <td class="header-label">Report for the Month of</td>
        <td>{{ $report->reportMonthLabel() }}</td>
        <td class="header-label">Name of School</td>
        <td>{{ $report->school_name }}</td>
    </tr>
    <tr>
        <td class="header-label">Grade Level</td>
        <td>{{ $report->grade_level }}</td>
        <td class="header-label">Section</td>
        <td colspan="3">{{ $report->section }}</td>
    </tr>
</table>

<table class="grid-table">
    <thead>
        <tr>
            <th rowspan="3" class="name-head">LEARNER'S NAME<br>(Last Name, First Name, Middle Name)</th>
            @foreach($padded as $col)
                <th class="day-head">{{ $col['day_num'] ?? '' }}</th>
            @endforeach
            <th colspan="2" class="total-head">Total for the Month</th>
            <th rowspan="3" class="remarks-head">REMARKS</th>
        </tr>
        <tr>
            @foreach($padded as $col)
                <th class="dow-head">{{ $col['dow'] ?? '' }}</th>
            @endforeach
            <th class="total-head" style="font-size:5px">ABSENT</th>
            <th class="total-head" style="font-size:5px">TARDY</th>
        </tr>
        <tr>
            @foreach($padded as $col)
                <th class="dow-head" style="font-size:5px">(Date)</th>
            @endforeach
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @include('pdf.sf2._learner-section', [
            'label' => 'MALE',
            'rows' => $grid['male'],
            'dailyTotals' => $grid['male_daily_totals'],
        ])
        @include('pdf.sf2._learner-section', [
            'label' => 'FEMALE',
            'rows' => $grid['female'],
            'dailyTotals' => $grid['female_daily_totals'],
        ])
        <tr class="total-row">
            <td class="name-cell total-label">Combined TOTAL PER DAY</td>
            @foreach($schoolDays as $d)
                <td class="day-cell total-num">{{ $grid['combined_daily_totals'][$d] ?? 0 }}</td>
            @endforeach
            @for($i = count($schoolDays); $i < count($padded); $i++)
                <td class="day-cell"></td>
            @endfor
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>

{{-- Page 2 (simplified) --}}
<div class="page-break page2">
    <table style="width:100%">
        <tr>
            <td class="page2-col">
                <div class="page2-box">
                    <strong>Guidelines</strong>
                    <p style="margin:4px 0 0">1. The attendance shall be accomplished daily.</p>
                    <p style="margin:2px 0">2. Dates shall be indicated in the columns after each learner's name.</p>
                    <p style="margin:2px 0">3. <strong>Blank</strong> = Present, <strong>x</strong> = Absent, <strong>shaded upper half</strong> = Tardy (late).</p>
                    <p style="margin:2px 0">4. This form shall be submitted to the Office of the Principal on or before the end of the month.</p>
                </div>
                <div class="page2-box">
                    <strong>Codes for Checking Attendance</strong><br>
                    Blank — Present &nbsp;|&nbsp; x — Absent &nbsp;|&nbsp; Shaded — Tardy
                </div>
            </td>
            <td class="page2-col">
                <table class="summary-table" style="width:100%">
                    <tr>
                        <th colspan="4" style="text-align:center">Monthly Summary</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>M</th>
                        <th>F</th>
                        <th>TOTAL</th>
                    </tr>
                    @php
                        $mCount = count($grid['male']);
                        $fCount = count($grid['female']);
                    @endphp
                    <tr>
                        <td>Registered Learners (as of end of the month)</td>
                        <td>{{ $mCount }}</td>
                        <td>{{ $fCount }}</td>
                        <td>{{ $mCount + $fCount }}</td>
                    </tr>
                    <tr>
                        <td>Number of school days in reporting month</td>
                        <td colspan="3" style="text-align:center">{{ $dayCount }}</td>
                    </tr>
                </table>
                <p style="margin:8px 0 2px"><strong>Month:</strong> {{ $report->reportMonthLabel() }} &nbsp;
                   <strong>No. of Days of Classes:</strong> {{ $dayCount }}</p>
                <table style="width:100%; margin-top:12px">
                    <tr>
                        <td style="width:50%">
                            <div class="sig-line"></div>
                            <div class="sig-label">Signature of Teacher over Printed Name</div>
                            <div style="margin-top:4px">{{ $report->teacher_name }}</div>
                        </td>
                        <td style="width:50%">
                            <div class="sig-line"></div>
                            <div class="sig-label">Signature of School Head over Printed Name</div>
                            <div style="margin-top:4px">{{ $report->school_head_name }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p style="text-align:right; margin-top:8px; font-size:6px">School Form 2 — Page 1 &amp; 2</p>
</div>

</body>
</html>
