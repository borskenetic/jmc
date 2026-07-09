<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students') || Schema::hasColumn('students', 'normalized_name')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            $table->string('normalized_name', 255)->nullable()->after('lastname');
            $table->index('normalized_name');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('students') || ! Schema::hasColumn('students', 'normalized_name')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['normalized_name']);
            $table->dropColumn('normalized_name');
        });
    }
};
