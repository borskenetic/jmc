@php
    $rows = $rows ?? [];
    $label = $label ?? '';
    $schoolDays = $report->school_days ?? [];
    $padded = $grid['padded_columns'] ?? [];
    $dailyTotals = $dailyTotals ?? [];
@endphp
@php $colSpan = 1 + count($padded) + 3; @endphp
<tr class="section-label">
    <td colspan="{{ $colSpan }}" class="section-label-cell"><strong>{{ $label }}</strong></td>
</tr>
@forelse($rows as $row)
    <tr class="learner-row">
        <td class="name-cell">{{ $row['student']->formattedName() }}</td>
        @foreach($schoolDays as $d)
            @include('pdf.sf2._mark-cell', ['mark' => $row['marks'][$d] ?? 'present'])
        @endforeach
        @for($i = count($schoolDays); $i < count($padded); $i++)
            <td class="day-cell"><div class="cell-diagonal"></div></td>
        @endfor
        <td class="total-cell">{{ $row['absent_total'] ?: '' }}</td>
        <td class="total-cell">{{ $row['tardy_total'] ?: '' }}</td>
        <td class="name-cell" style="font-size:5.5px">{{ $row['student']->remarks }}</td>
    </tr>
@empty
    <tr>
        <td class="name-cell text-muted" colspan="{{ $colSpan }}">—</td>
    </tr>
@endforelse
@if($label !== '')
<tr class="total-row">
    <td class="name-cell total-label">{{ strtoupper(explode(' ', $label)[0] ?? '') }} | TOTAL Per Day</td>
    @foreach($schoolDays as $d)
        <td class="day-cell total-num">{{ $dailyTotals[$d] ?? 0 }}</td>
    @endforeach
    @for($i = count($schoolDays); $i < count($padded); $i++)
        <td class="day-cell"></td>
    @endfor
    <td class="total-cell"></td>
    <td class="total-cell"></td>
    <td></td>
</tr>
@endif
