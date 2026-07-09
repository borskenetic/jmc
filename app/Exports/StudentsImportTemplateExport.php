<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsImportTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'ID NUMBER',
            'LAST NAME',
            'FIRST NAME & MI',
            'GRADE LEVEL',
            'LRN',
            'DATE OF BIRTH',
            'CONTACT PERSON',
            'NUMBER',
            'ADDRESS',
            'PROFILE PICTURE',
            'RFID',
        ];
    }

    public function array(): array
    {
        return [
            [
                '2024-00001',
                'Dela Cruz',
                'Juan M',
                'Grade 7',
                '123456789012',
                '2004-03-15',
                'Maria Dela Cruz',
                '09171234567',
                '123 Main St, Davao City',
                '2024-00001.jpg',
                '',
            ],
        ];
    }
}
