<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_courses', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        if (Schema::hasTable('program_years')) {
            DB::statement('
                UPDATE program_courses
                SET program_id = (
                    SELECT program_id FROM program_years
                    WHERE program_years.id = program_courses.program_year_id
                )
                WHERE program_year_id IS NOT NULL AND program_id IS NULL
            ');
        }
    }

    public function down(): void
    {
        Schema::table('program_courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });
    }
};
