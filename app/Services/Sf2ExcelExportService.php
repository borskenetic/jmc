<?php

namespace App\Services;

use App\Models\Sf2Report;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Sf2ExcelExportService
{
    public function __construct(
        protected Sf2GridBuilder $grid,
    ) {}

    public function download(Sf2Report $report): StreamedResponse
    {
        $spreadsheet = $this->buildSpreadsheet($report);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $this->excelFilename($report), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function buildSpreadsheet(Sf2Report $report): Spreadsheet
    {
        $report->loadMissing('students');
        $grid = $this->grid->build($report);

        $templatePath = config('sf2.excel.template');
        if (! is_file($templatePath)) {
            throw new RuntimeException(
                'SF2 Excel template missing. Add resources/templates/sf2/sf2-template.xlsx'
            );
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getSheet(0);
        $cfg = config('sf2.excel');

        $this->fillHeader($sheet, $report, $cfg);
        $this->clearAttendanceDiagonalBorders($sheet, $cfg);
        $this->fillDayHeaders($sheet, $grid, $cfg);
        $this->fillLearnerBlock($sheet, $grid['male'], $cfg['male_first_row'], $cfg['male_last_row'], $cfg, $report->school_days ?? []);
        $this->fillTotalRow($sheet, $cfg['male_total_row'], $grid['male_daily_totals'], $report->school_days ?? [], $cfg);
        $this->fillLearnerBlock($sheet, $grid['female'], $cfg['female_first_row'], $cfg['female_last_row'], $cfg, $report->school_days ?? []);
        $this->fillTotalRow($sheet, $cfg['female_total_row'], $grid['female_daily_totals'], $report->school_days ?? [], $cfg);
        $this->fillTotalRow($sheet, $cfg['combined_total_row'], $grid['combined_daily_totals'], $report->school_days ?? [], $cfg);
        $this->fillSummary($sheet, $report, $grid, $cfg);
        $this->fillSignatures($sheet, $report, $cfg);

        return $spreadsheet;
    }

    protected function fillHeader(Worksheet $sheet, Sf2Report $report, array $cfg): void
    {
        $h = $cfg['header'];
        $sheet->setCellValue($h['school_id'], $report->school_id ?? '');
        $sheet->setCellValue($h['school_year'], $report->school_year);
        $sheet->setCellValue($h['report_month'], strtoupper($report->reportMonthLabel()));
        $sheet->setCellValue($h['school_name'], $report->school_name);
        $sheet->setCellValue($h['grade_level'], $report->grade_level);
        $sheet->setCellValue($h['section'], $report->section);
    }

    protected function fillDayHeaders(Worksheet $sheet, array $grid, array $cfg): void
    {
        $startIndex = Coordinate::columnIndexFromString($cfg['first_day_col']);

        foreach ($grid['padded_columns'] as $i => $col) {
            $letter = Coordinate::stringFromColumnIndex($startIndex + $i);
            if ($col['day_num'] !== null) {
                $sheet->setCellValue($letter.$cfg['date_header_row'], $col['day_num']);
                $sheet->setCellValue($letter.$cfg['dow_header_row'], $col['dow'] ?? '');
            } else {
                $sheet->setCellValue($letter.$cfg['date_header_row'], '');
                $sheet->setCellValue($letter.$cfg['dow_header_row'], '');
            }
        }
    }

    /**
     * @param  list<array{student: \App\Models\Sf2ReportStudent, marks: array<string, string>, absent_total: int, tardy_total: int}>  $rows
     * @param  list<string>  $schoolDays
     */
    protected function fillLearnerBlock(
        Worksheet $sheet,
        array $rows,
        int $firstRow,
        int $lastRow,
        array $cfg,
        array $schoolDays,
    ): void {
        $maxRows = $lastRow - $firstRow + 1;
        $startIndex = Coordinate::columnIndexFromString($cfg['first_day_col']);

        foreach (array_slice($rows, 0, $maxRows) as $i => $row) {
            $r = $firstRow + $i;
            $student = $row['student'];
            $sheet->setCellValue($cfg['number_col'].$r, $i + 1);
            $sheet->setCellValue($cfg['name_col'].$r, $student->formattedName());

            $dayColCount = (int) ($cfg['day_column_count'] ?? 25);
            for ($dayIndex = 0; $dayIndex < $dayColCount; $dayIndex++) {
                $letter = Coordinate::stringFromColumnIndex($startIndex + $dayIndex);
                $date = $schoolDays[$dayIndex] ?? null;
                $mark = $date !== null
                    ? ($row['marks'][$date] ?? Sf2GridBuilder::MARK_PRESENT)
                    : Sf2GridBuilder::MARK_PRESENT;
                $this->applyMark($sheet, $letter.$r, $mark);
            }

            $sheet->setCellValue($cfg['absent_col'].$r, $row['absent_total'] ?: '');
            $sheet->setCellValue($cfg['tardy_col'].$r, $row['tardy_total'] ?: '');
            $remarks = trim((string) $student->remarks);
            if ($remarks !== '') {
                $sheet->setCellValue($cfg['remarks_col'].$r, $remarks);
            }
        }
    }

    /**
     * @param  array<string, int>  $dailyTotals
     * @param  list<string>  $schoolDays
     */
    protected function fillTotalRow(
        Worksheet $sheet,
        int $row,
        array $dailyTotals,
        array $schoolDays,
        array $cfg,
    ): void {
        $startIndex = Coordinate::columnIndexFromString($cfg['first_day_col']);

        $dayColCount = (int) ($cfg['day_column_count'] ?? 25);
        for ($dayIndex = 0; $dayIndex < $dayColCount; $dayIndex++) {
            $letter = Coordinate::stringFromColumnIndex($startIndex + $dayIndex);
            $date = $schoolDays[$dayIndex] ?? null;
            $sheet->setCellValue($letter.$row, $date !== null ? ($dailyTotals[$date] ?? 0) : '');
            $this->clearCellDiagonal($sheet, $letter.$row);
        }
    }

    /** Remove template diagonal slashes from all attendance day cells. */
    protected function clearAttendanceDiagonalBorders(Worksheet $sheet, array $cfg): void
    {
        $startIndex = Coordinate::columnIndexFromString($cfg['first_day_col']);
        $dayColCount = (int) ($cfg['day_column_count'] ?? 25);
        $lastRow = (int) $cfg['combined_total_row'];

        for ($r = (int) $cfg['male_first_row']; $r <= $lastRow; $r++) {
            for ($dayIndex = 0; $dayIndex < $dayColCount; $dayIndex++) {
                $letter = Coordinate::stringFromColumnIndex($startIndex + $dayIndex);
                $this->clearCellDiagonal($sheet, $letter.$r);
            }
        }
    }

    protected function clearCellDiagonal(Worksheet $sheet, string $cell): void
    {
        $borders = $sheet->getStyle($cell)->getBorders();
        $borders->getDiagonal()->setBorderStyle(Border::BORDER_NONE);
        $borders->setDiagonalDirection(Borders::DIAGONAL_NONE);
    }

    protected function fillSummary(Worksheet $sheet, Sf2Report $report, array $grid, array $cfg): void
    {
        $m = count($grid['male']);
        $f = count($grid['female']);
        $total = $m + $f;
        $days = count($report->school_days ?? []);
        $cols = $cfg['summary']['summary_value_cols'];
        $row = $cfg['summary']['registered_end_month'];

        $sheet->setCellValue($cfg['summary']['month_days_label'], $days);
        $sheet->setCellValue($cols[0].$row, $m);
        $sheet->setCellValue($cols[1].$row, $f);
        $sheet->setCellValue($cols[2].$row, $total);

        $sheet->setCellValue($cols[0].$cfg['summary']['school_days_row'], $days);
        $sheet->setCellValue($cols[1].$cfg['summary']['school_days_row'], $days);
        $sheet->setCellValue($cols[2].$cfg['summary']['school_days_row'], $days);
    }

    protected function fillSignatures(Worksheet $sheet, Sf2Report $report, array $cfg): void
    {
        if ($report->teacher_name) {
            $sheet->setCellValue($cfg['signatures']['teacher_name'], $report->teacher_name);
        }
        if ($report->school_head_name) {
            $sheet->setCellValue($cfg['signatures']['school_head_name'], $report->school_head_name);
        }
    }

    protected function applyMark(Worksheet $sheet, string $cell, string $mark): void
    {
        $this->clearCellDiagonal($sheet, $cell);
        $sheet->setCellValue($cell, '');

        $fill = $sheet->getStyle($cell)->getFill();
        $fill->setFillType(Fill::FILL_SOLID);
        $fill->getStartColor()->setRGB('FFFFFF');

        if ($mark === Sf2GridBuilder::MARK_ABSENT) {
            $sheet->setCellValue($cell, 'x');

            return;
        }

        if ($mark === Sf2GridBuilder::MARK_TARDY) {
            $fill->getStartColor()->setRGB('BFBFBF');
        }
    }

    protected function excelFilename(Sf2Report $report): string
    {
        return $this->baseFilename($report).'.xlsx';
    }

    protected function baseFilename(Sf2Report $report): string
    {
        return sprintf(
            'SF2_%s_%s_%s_%d',
            str_replace(' ', '_', $report->grade_level),
            str_replace(' ', '_', $report->section),
            $report->reportMonthLabel(),
            $report->report_year
        );
    }
}
