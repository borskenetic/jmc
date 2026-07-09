<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (! Schema::hasColumn('students', 'emergency_person')) {
                    $table->string('emergency_person')->nullable();
                }
                if (! Schema::hasColumn('students', 'emergency_relationship')) {
                    $table->string('emergency_relationship')->nullable();
                }
                if (! Schema::hasColumn('students', 'emergency_number')) {
                    $table->string('emergency_number')->nullable();
                }
                if (! Schema::hasColumn('students', 'emergency_address')) {
                    $table->text('emergency_address')->nullable();
                }
            });
        }

        if (Schema::hasTable('pending_students')) {
            Schema::table('pending_students', function (Blueprint $table) {
                if (! Schema::hasColumn('pending_students', 'emergency_person')) {
                    $table->string('emergency_person')->nullable();
                }
                if (! Schema::hasColumn('pending_students', 'emergency_relationship')) {
                    $table->string('emergency_relationship')->nullable();
                }
                if (! Schema::hasColumn('pending_students', 'emergency_number')) {
                    $table->string('emergency_number')->nullable();
                }
                if (! Schema::hasColumn('pending_students', 'emergency_address')) {
                    $table->text('emergency_address')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $columns = array_filter([
                    Schema::hasColumn('students', 'emergency_person') ? 'emergency_person' : null,
                    Schema::hasColumn('students', 'emergency_relationship') ? 'emergency_relationship' : null,
                    Schema::hasColumn('students', 'emergency_number') ? 'emergency_number' : null,
                    Schema::hasColumn('students', 'emergency_address') ? 'emergency_address' : null,
                ]);

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('pending_students')) {
            Schema::table('pending_students', function (Blueprint $table) {
                $columns = array_filter([
                    Schema::hasColumn('pending_students', 'emergency_person') ? 'emergency_person' : null,
                    Schema::hasColumn('pending_students', 'emergency_relationship') ? 'emergency_relationship' : null,
                    Schema::hasColumn('pending_students', 'emergency_number') ? 'emergency_number' : null,
                    Schema::hasColumn('pending_students', 'emergency_address') ? 'emergency_address' : null,
                ]);

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
