<?php

namespace App\Models;

use App\Enums\EducationalLevel;
use Illuminate\Database\Eloquent\Model;

class PendingStudent extends Model
{
    protected $fillable = [
        'student_id',
        'firstname',
        'lastname',
        'middle_initial',
        'birth_date',
        'blood_type',
        'course',
        'year',
        'educational_level',
        'mobile_number',
        'profile_picture',
        'emergency_person',
        'emergency_relationship',
        'emergency_number',
        'emergency_address',
        'student_signature',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'educational_level' => EducationalLevel::class,
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
