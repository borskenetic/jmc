<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = ['program_code', 'program_name', 'total_years'];

  
    public function years()
    {
        return $this->hasMany(ProgramYear::class);
    }

    public function courses()
    {
        return $this->hasMany(ProgramCourse::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($program) {
            $program->courses()->delete();

            foreach ($program->years as $year) {
                $year->courses()->whereNull('program_id')->delete();
                $year->delete();
            }
        });
    }
    
    public function books() {
        return $this->belongsToMany(Book::class, 'book_program');
    }
}
