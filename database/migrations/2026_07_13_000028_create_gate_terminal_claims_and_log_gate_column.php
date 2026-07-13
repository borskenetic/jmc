<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gate_terminal_claims')) {
            Schema::create('gate_terminal_claims', function (Blueprint $table) {
                $table->id();
                $table->string('gate', 120);
                $table->string('terminal_token', 64)->unique();
                $table->timestamp('last_seen_at');
                $table->timestamps();

                $table->unique('gate');
            });
        }

        if (Schema::hasTable('attendance_logs') && ! Schema::hasColumn('attendance_logs', 'gate')) {
            Schema::table('attendance_logs', function (Blueprint $table) {
                $table->string('gate', 120)->nullable()->after('section');
                $table->index('gate');
            });
        }

        if (Schema::hasTable('visitor_logs') && ! Schema::hasColumn('visitor_logs', 'gate')) {
            Schema::table('visitor_logs', function (Blueprint $table) {
                $table->string('gate', 120)->nullable()->after('status');
                $table->index('gate');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_terminal_claims');

        if (Schema::hasTable('attendance_logs') && Schema::hasColumn('attendance_logs', 'gate')) {
            Schema::table('attendance_logs', function (Blueprint $table) {
                $table->dropIndex(['gate']);
                $table->dropColumn('gate');
            });
        }

        if (Schema::hasTable('visitor_logs') && Schema::hasColumn('visitor_logs', 'gate')) {
            Schema::table('visitor_logs', function (Blueprint $table) {
                $table->dropIndex(['gate']);
                $table->dropColumn('gate');
            });
        }
    }
};
