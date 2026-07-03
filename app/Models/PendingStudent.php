<?php

namespace App\Models;

use App\Enums\EducationalLevel;
use App\Support\ProfilePicture;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'section',
        'sex',
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

    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => ProfilePicture::relativePath($value),
            set: fn (?string $value) => $value,
        );
    }
}
