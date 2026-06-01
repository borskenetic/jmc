<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['students', 'pending_students'] as $table) {
            if (! Schema::hasColumn($table, 'educational_level')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->string('educational_level', 32)->nullable()->after('year');
                });
            }
        }

        DB::table('students')
            ->whereNull('educational_level')
            ->update(['educational_level' => 'college']);

        DB::table('pending_students')
            ->whereNull('educational_level')
            ->update(['educational_level' => 'college']);
    }

    public function down(): void
    {
        foreach (['students', 'pending_students'] as $table) {
            if (Schema::hasColumn($table, 'educational_level')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('educational_level');
                });
            }
        }
    }
};
