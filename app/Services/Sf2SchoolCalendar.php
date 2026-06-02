<?php

namespace App\Services;

use Carbon\Carbon;

class Sf2SchoolCalendar
{
    /**
     * Weekdays (Mon–Fri) in the given month, capped for DepEd SF2 columns.
     *
     * @return list<string> Y-m-d dates
     */
    public function schoolDaysInMonth(int $year, int $month, ?int $max = null): array
    {
        $max = $max ?? (int) config('sf2.max_day_columns', 25);
        $tz = config('sf2.timezone', 'Asia/Manila');

        $start = Carbon::create($year, $month, 1, 0, 0, 0, $tz)->startOfDay();
        $end = $start->copy()->endOfMonth();

        $days = [];
        for ($d = $start->copy(); $d->lte($end) && count($days) < $max; $d->addDay()) {
            if ($d->isWeekday()) {
                $days[] = $d->toDateString();
            }
        }

        return $days;
    }

    public function dayCount(int $year, int $month): int
    {
        return count($this->schoolDaysInMonth($year, $month));
    }
}
