<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramCourse extends Model
{
    protected $fillable = [
        'program_id',
        'program_year_id',
        'course_code',
        'course_name',
    ];

    public function year()
    {
        return $this->belongsTo(ProgramYear::class, 'program_year_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function ebooks()
    {
        return $this->hasMany(Ebook::class, 'course_id'); // make sure ebooks has course_id
    }
}
