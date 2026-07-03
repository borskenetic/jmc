<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsRfidImportTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'student_id',
            'rfid',
        ];
    }

    public function array(): array
    {
        return [
            ['2024-00001', 'E200001234567890'],
            ['2024-00002', 'E200001234567891'],
        ];
    }
}
