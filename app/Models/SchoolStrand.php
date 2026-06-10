<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SchoolStrand extends Model
{
    protected $fillable = ['name'];

    /** @return list<string> */
    public static function orderedNames(): array
    {
        if (! Schema::hasTable('school_strands')) {
            return config('patron.shs_strands', []);
        }

        $fromDb = static::query()->orderBy('name')->pluck('name')->all();

        return $fromDb !== []
            ? $fromDb
            : config('patron.shs_strands', []);
    }
}
