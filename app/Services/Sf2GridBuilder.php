<?php

namespace App\Services;

use App\Models\Sf2Report;
use App\Models\Sf2ReportStudent;
use Carbon\Carbon;

class Sf2GridBuilder
{
    public const MARK_PRESENT = 'present';

    public const MARK_ABSENT = 'absent';

    public const MARK_TARDY = 'tardy';

    /**
     * @return array{
     *   columns: list<array{date: string, day_num: int, dow: string}>,
     *   padded_columns: list<array{date: ?string, day_num: ?int, dow: ?string}>,
     *   male: list<array{student: Sf2ReportStudent, marks: array<string, string>, absent_total: int, tardy_total: int}>,
     *   female: list<array{student: Sf2ReportStudent, marks: array<string, string>, absent_total: int, tardy_total: int}>,
     *   male_daily_totals: array<string, int>,
     *   female_daily_totals: array<string, int>,
     *   combined_daily_totals: array<string, int>,
     * }
     */
    public function build(Sf2Report $report): array
    {
        $report->loadMissing('students');

        $schoolDays = $report->school_days ?? [];
        $maxCols = (int) config('sf2.max_day_columns', 25);
        $tz = config('sf2.timezone', 'Asia/Manila');

        $columns = [];
        foreach ($schoolDays as $date) {
            $c = Carbon::parse($date, $tz);
            $columns[] = [
                'date' => $date,
                'day_num' => (int) $c->format('j'),
                'dow' => $this->dowLabel($c),
            ];
        }

        $padded = $columns;
        while (count($padded) < $maxCols) {
            $padded[] = ['date' => null, 'day_num' => null, 'dow' => null];
        }

        $male = [];
        $female = [];
        $maleDaily = array_fill_keys($schoolDays, 0);
        $femaleDaily = array_fill_keys($schoolDays, 0);

        foreach ($report->students as $student) {
            $row = $this->buildStudentRow($student, $schoolDays);
            if ($student->isMale()) {
                $male[] = $row;
                foreach ($schoolDays as $d) {
                    if (($row['marks'][$d] ?? self::MARK_PRESENT) === self::MARK_PRESENT) {
                        $maleDaily[$d]++;
                    }
                }
            } else {
                $female[] = $row;
                foreach ($schoolDays as $d) {
                    if (($row['marks'][$d] ?? self::MARK_PRESENT) === self::MARK_PRESENT) {
                        $femaleDaily[$d]++;
                    }
                }
            }
        }

        $combinedDaily = [];
        foreach ($schoolDays as $d) {
            $combinedDaily[$d] = ($maleDaily[$d] ?? 0) + ($femaleDaily[$d] ?? 0);
        }

        return [
            'columns' => $columns,
            'padded_columns' => $padded,
            'male' => $male,
            'female' => $female,
            'male_daily_totals' => $maleDaily,
            'female_daily_totals' => $femaleDaily,
            'combined_daily_totals' => $combinedDaily,
        ];
    }

    /**
     * @param  list<string>  $schoolDays
     * @return array{student: Sf2ReportStudent, marks: array<string, string>, absent_total: int, tardy_total: int}
     */
    protected function buildStudentRow(Sf2ReportStudent $student, array $schoolDays): array
    {
        $absent = collect($student->absent_dates ?? [])->map(fn ($d) => $this->normalizeDate($d))->filter()->all();
        $tardy = collect($student->tardy_dates ?? [])->map(fn ($d) => $this->normalizeDate($d))->filter()->all();

        $absentSet = array_flip($absent);
        $tardySet = array_flip($tardy);

        $marks = [];
        $absentTotal = 0;
        $tardyTotal = 0;

        foreach ($schoolDays as $date) {
            if (isset($absentSet[$date])) {
                $marks[$date] = self::MARK_ABSENT;
                $absentTotal++;
            } elseif (isset($tardySet[$date])) {
                $marks[$date] = self::MARK_TARDY;
                $tardyTotal++;
            } else {
                $marks[$date] = self::MARK_PRESENT;
            }
        }

        return [
            'student' => $student,
            'marks' => $marks,
            'absent_total' => $absentTotal,
            'tardy_total' => $tardyTotal,
        ];
    }

    protected function dowLabel(Carbon $date): string
    {
        return match ((int) $date->dayOfWeekIso) {
            1 => 'M',
            2 => 'T',
            3 => 'W',
            4 => 'TH',
            5 => 'F',
            default => '',
        };
    }

    public function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value, config('sf2.timezone', 'Asia/Manila'))->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Parse textarea / comma-separated date lines from manual entry.
     *
     * @return list<string>
     */
    public function parseDateList(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $parts = preg_split('/[\s,;]+/', trim($raw)) ?: [];
        $out = [];

        foreach ($parts as $part) {
            $normalized = $this->normalizeDate($part);
            if ($normalized !== null) {
                $out[] = $normalized;
            }
        }

        return array_values(array_unique($out));
    }
}
