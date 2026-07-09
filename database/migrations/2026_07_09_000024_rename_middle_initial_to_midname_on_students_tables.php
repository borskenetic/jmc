<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('students', 'middle_initial')) {
            Schema::table('students', function (Blueprint $table) {
                $table->renameColumn('middle_initial', 'midname');
            });
        }

        if (Schema::hasColumn('pending_students', 'middle_initial')) {
            Schema::table('pending_students', function (Blueprint $table) {
                $table->renameColumn('middle_initial', 'midname');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'midname')) {
            Schema::table('students', function (Blueprint $table) {
                $table->renameColumn('midname', 'middle_initial');
            });
        }

        if (Schema::hasColumn('pending_students', 'midname')) {
            Schema::table('pending_students', function (Blueprint $table) {
                $table->renameColumn('midname', 'middle_initial');
            });
        }
    }
};
