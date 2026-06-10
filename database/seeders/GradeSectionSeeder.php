<?php

namespace Database\Seeders;

use App\Models\GradeSection;
use Illuminate\Database\Seeder;

class GradeSectionSeeder extends Seeder
{
    public function run(): void
    {
        $basic = [
            ['grade_level' => 'Grade 7', 'strand' => '', 'section' => 'St. Francis'],
            ['grade_level' => 'Grade 7', 'strand' => '', 'section' => 'St. Ignatius'],
            ['grade_level' => 'Grade 8', 'strand' => '', 'section' => 'St. Francis'],
        ];

        $senior = [
            ['grade_level' => 'Grade 11', 'strand' => 'STEM', 'section' => 'St. Francis'],
            ['grade_level' => 'Grade 11', 'strand' => 'STEM', 'section' => 'St. Ignatius'],
            ['grade_level' => 'Grade 11', 'strand' => 'ABM', 'section' => 'St. Clare'],
            ['grade_level' => 'Grade 12', 'strand' => 'STEM', 'section' => 'St. Francis'],
            ['grade_level' => 'Grade 12', 'strand' => 'ABM', 'section' => 'St. Clare'],
        ];

        foreach (array_merge($basic, $senior) as $row) {
            GradeSection::firstOrCreate([
                'grade_level' => $row['grade_level'],
                'strand' => $row['strand'],
                'section' => $row['section'],
            ]);
        }
    }
}
