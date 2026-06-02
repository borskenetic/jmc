<?php

namespace App\Http\Controllers;

use App\Models\Sf2Report;
use App\Models\Sf2ReportStudent;
use App\Services\Sf2ExcelExportService;
use App\Services\Sf2GridBuilder;
use App\Services\Sf2SchoolCalendar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Sf2ReportController extends Controller
{
    public function __construct(
        protected Sf2SchoolCalendar $calendar,
        protected Sf2GridBuilder $grid,
        protected Sf2ExcelExportService $excel,
    ) {}

    public function index()
    {
        $reports = Sf2Report::query()
            ->withCount('students')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('sf2.index', compact('reports'));
    }

    public function create()
    {
        $gradeLevels = config('sf2.grade_levels', []);
        $defaults = [
            'school_name' => config('app.name'),
            'school_year' => $this->defaultSchoolYear(),
            'report_month' => (int) now(config('sf2.timezone', 'Asia/Manila'))->format('n'),
            'report_year' => (int) now(config('sf2.timezone', 'Asia/Manila'))->format('Y'),
        ];

        return view('sf2.create', compact('gradeLevels', 'defaults'));
    }

    public function store(Request $request)
    {
        $report = $this->persistReport($request, new Sf2Report);

        return redirect()
            ->route('sf2.show', $report)
            ->with('success', 'SF2 report saved. You can preview or download the PDF.');
    }

    public function show(Sf2Report $sf2)
    {
        $sf2->load('students');
        $grid = $this->grid->build($sf2);

        return view('sf2.show', [
            'report' => $sf2,
            'grid' => $grid,
        ]);
    }

    public function edit(Sf2Report $sf2)
    {
        $sf2->load('students');
        $gradeLevels = config('sf2.grade_levels', []);

        return view('sf2.edit', compact('sf2', 'gradeLevels'));
    }

    public function update(Request $request, Sf2Report $sf2)
    {
        $report = $this->persistReport($request, $sf2);

        return redirect()
            ->route('sf2.show', $report)
            ->with('success', 'SF2 report updated.');
    }

    public function destroy(Sf2Report $sf2)
    {
        $sf2->delete();

        return redirect()
            ->route('sf2.index')
            ->with('success', 'SF2 report deleted.');
    }

    public function pdf(Sf2Report $sf2)
    {
        $sf2->load('students');
        $grid = $this->grid->build($sf2);

        $pdf = Pdf::loadView('pdf.sf2', [
            'report' => $sf2,
            'grid' => $grid,
        ])
            ->setPaper('a4', 'landscape');

        $filename = sprintf(
            'SF2_%s_%s_%s_%d.pdf',
            str_replace(' ', '_', $sf2->grade_level),
            str_replace(' ', '_', $sf2->section),
            $sf2->reportMonthLabel(),
            $sf2->report_year
        );

        return $pdf->download($filename);
    }

    public function excel(Sf2Report $sf2)
    {
        return $this->excel->download($sf2);
    }

    protected function persistReport(Request $request, Sf2Report $report): Sf2Report
    {
        $validated = $request->validate([
            'school_id' => 'nullable|string|max:50',
            'school_name' => 'required|string|max:255',
            'school_year' => 'required|string|max:16',
            'report_month' => 'required|integer|min:1|max:12',
            'report_year' => 'required|integer|min:2000|max:2100',
            'grade_level' => 'required|string|max:64',
            'section' => 'required|string|max:64',
            'teacher_name' => 'nullable|string|max:255',
            'school_head_name' => 'nullable|string|max:255',
            'students' => 'required|array|min:1',
            'students.*.sex' => 'required|in:male,female',
            'students.*.last_name' => 'required|string|max:100',
            'students.*.first_name' => 'required|string|max:100',
            'students.*.middle_name' => 'nullable|string|max:100',
            'students.*.remarks' => 'nullable|string|max:500',
            'students.*.absent_dates' => 'nullable|string|max:2000',
            'students.*.tardy_dates' => 'nullable|string|max:2000',
        ]);

        $schoolDays = $this->calendar->schoolDaysInMonth(
            (int) $validated['report_year'],
            (int) $validated['report_month']
        );

        return DB::transaction(function () use ($request, $report, $validated, $schoolDays) {
            $report->fill([
                'user_id' => $request->user()?->id,
                'school_id' => $validated['school_id'] ?? null,
                'school_name' => $validated['school_name'],
                'school_year' => $validated['school_year'],
                'report_month' => (int) $validated['report_month'],
                'report_year' => (int) $validated['report_year'],
                'grade_level' => $validated['grade_level'],
                'section' => $validated['section'],
                'school_days' => $schoolDays,
                'teacher_name' => $validated['teacher_name'] ?? null,
                'school_head_name' => $validated['school_head_name'] ?? null,
            ]);
            $report->save();

            $report->students()->delete();

            foreach ($validated['students'] as $i => $row) {
                Sf2ReportStudent::create([
                    'sf2_report_id' => $report->id,
                    'sort_order' => $i,
                    'sex' => $row['sex'],
                    'last_name' => $row['last_name'],
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'] ?? null,
                    'remarks' => $row['remarks'] ?? null,
                    'absent_dates' => $this->grid->parseDateList($row['absent_dates'] ?? null),
                    'tardy_dates' => $this->grid->parseDateList($row['tardy_dates'] ?? null),
                ]);
            }

            return $report->fresh(['students']);
        });
    }

    protected function defaultSchoolYear(): string
    {
        $tz = config('sf2.timezone', 'Asia/Manila');
        $now = now($tz);
        $y = (int) $now->format('Y');
        $m = (int) $now->format('n');

        if ($m >= 6) {
            return $y.'-'.($y + 1);
        }

        return ($y - 1).'-'.$y;
    }
}
