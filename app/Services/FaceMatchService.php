<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Collection;

class FaceMatchService
{
    public function threshold(): float
    {
        return (float) config('face.match_threshold', 0.55);
    }

    /**
     * @param  list<float|int>  $descriptor
     * @return array{student: Student, distance: float}|null
     */
    public function findBestMatch(array $descriptor): ?array
    {
        $descriptor = $this->normalizeDescriptor($descriptor);
        if ($descriptor === null) {
            return null;
        }

        $best = null;
        $bestDistance = PHP_FLOAT_MAX;
        $threshold = $this->threshold();

        foreach ($this->enrolledStudents() as $student) {
            $stored = $student->face_descriptor;
            if (! is_array($stored) || count($stored) !== count($descriptor)) {
                continue;
            }

            $distance = $this->euclideanDistance($descriptor, $stored);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $student;
            }
        }

        if ($best === null || $bestDistance > $threshold) {
            return null;
        }

        return [
            'student' => $best,
            'distance' => $bestDistance,
        ];
    }

    /** @return Collection<int, Student> */
    public function enrolledStudents(): Collection
    {
        return Student::query()
            ->whereNotNull('face_descriptor')
            ->get(['id', 'firstname', 'lastname', 'profile_picture', 'year', 'educational_level', 'face_descriptor', 'qrcode']);
    }

    public function enrolledCount(): int
    {
        return Student::query()->whereNotNull('face_descriptor')->count();
    }

    /**
     * @param  mixed  $raw
     * @return list<float>|null
     */
    public function normalizeDescriptor(mixed $raw): ?array
    {
        if (! is_array($raw)) {
            return null;
        }

        $expected = (int) config('face.descriptor_length', 128);
        if (count($raw) !== $expected) {
            return null;
        }

        $out = [];
        foreach ($raw as $value) {
            if (! is_numeric($value)) {
                return null;
            }
            $out[] = (float) $value;
        }

        return $out;
    }

    /**
     * @param  list<float>  $a
     * @param  list<float>  $b
     */
    public function euclideanDistance(array $a, array $b): float
    {
        $sum = 0.0;
        $n = count($a);

        for ($i = 0; $i < $n; $i++) {
            $diff = $a[$i] - $b[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }
}
