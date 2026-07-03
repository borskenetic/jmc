<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsRfidImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $updated = 0;

    public int $skipped = 0;

    public int $notFound = 0;

    /** @var list<string> */
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $studentId = trim((string) ($row['student_id'] ?? $row['id_number'] ?? $row['id'] ?? ''));
            $rfid = trim((string) ($row['rfid'] ?? $row['tag'] ?? ''));

            if ($studentId === '' && $rfid === '') {
                continue;
            }

            if ($studentId === '' || $rfid === '') {
                $this->skipped++;
                $this->errors[] = "Row {$line}: student_id and rfid are both required.";

                continue;
            }

            $student = Student::where('student_id', $studentId)->first();

            if ($student === null) {
                $this->notFound++;
                $this->errors[] = "Row {$line}: no student with ID \"{$studentId}\".";

                continue;
            }

            $duplicate = Student::where('rfid', $rfid)
                ->where('id', '!=', $student->id)
                ->exists();

            if ($duplicate) {
                $this->skipped++;
                $this->errors[] = "Row {$line}: RFID \"{$rfid}\" is already assigned to another student.";

                continue;
            }

            if ($student->rfid === $rfid) {
                $this->skipped++;

                continue;
            }

            $student->update(['rfid' => $rfid]);
            $this->updated++;
        }
    }
}
