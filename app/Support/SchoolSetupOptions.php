<?php

namespace App\Support;

use App\Models\GradeSection;
use App\Models\Program;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SchoolSetupOptions
{
    public static function programsForForms(): Collection
    {
        if (! Schema::hasTable('programs')) {
            return collect();
        }

        return Program::query()->orderBy('program_name')->get();
    }

    /** @return array<string, list<string>> */
    public static function sectionsByGrade(): array
    {
        if (! Schema::hasTable('grade_sections')) {
            return [];
        }

        return GradeSection::groupedByGrade();
    }

    /** @return array{
     *   sectionsByGrade: array<string, list<string>>,
     *   sectionsByGradeStrand: array<string, array<string, list<string>>>,
     *   strands: list<string>,
     *   seniorHighGrades: list<string>
     * }
     */
    public static function registrationData(): array
    {
        if (! Schema::hasTable('grade_sections')) {
            return [
                'sectionsByGrade' => [],
                'sectionsByGradeStrand' => [],
                'strands' => config('patron.shs_strands', []),
                'seniorHighGrades' => config('patron.senior_high_grades', ['Grade 11', 'Grade 12']),
            ];
        }

        return GradeSection::registrationData();
    }
}
